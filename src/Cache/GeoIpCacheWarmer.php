<?php
/**
 * When warming the cache, ensure the MaxMind GeoIP Database has been downloaded
 *
 * @category  Aligent
 * @package
 * @author    Adam Hall <adam.hall@aligent.com.au>
 * @copyright 2018 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */

namespace Aligent\GeoDetectionBundle\Cache;

use Aligent\GeoDetectionBundle\DependencyInjection\Configuration;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use PharData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class GeoIpCacheWarmer implements CacheWarmerInterface
{
    const DB_FILE_SUFFIX = '*.mmdb';
    const GEO_TEMP_DIR = '/geo_temp/';

    /**
     * @var string
     */
    protected $database;

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * GeoIpCacheWarmer constructor.
     * @param string $database
     * @param ConfigManager $configManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        $database,
        ConfigManager $configManager,
        LoggerInterface $logger
    ) {
        $this->database = $database;
        $this->logger = $logger;
        $this->configManager = $configManager;
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional(): bool
    {
        return true;
    }

    /**
     * Warms up the cache.
     *
     * {@inheritdoc}
     */
    public function warmUp($cacheDir): array
    {
        //avoid downloading if db is up to date
        if ($this->isGeoDatabaseUpToDate()) {
            $this->logger->info('Geo Database still up to date, no new download triggered');
            return [];
        }

        $tempWorkingDir = dirname($cacheDir) . static::GEO_TEMP_DIR;
        $this->createTempWorkingDir($tempWorkingDir);

        $databaseUrl = $this->configManager->get(
            Configuration::getConfigKeyByName(
                Configuration::DATABASE_DOWNLOAD_URL
            )
        );
        $tempDownloadFilePath = $this->downloadGeoDatabase($databaseUrl, $tempWorkingDir);
        $this->decompressAndMove($tempDownloadFilePath, $this->database, $tempWorkingDir);
        $this->removeTempWorkingDir($tempWorkingDir);

        return [];
    }

    protected function isGeoDatabaseUpToDate()
    {
        try {
            $fileSystem = new Filesystem();
            if ($fileSystem->exists($this->database)) {
                $fileCreationTime = filectime($this->database);
                $diffInDays = Carbon::now()->diffInDays(Carbon::createFromTimestamp($fileCreationTime));
                if ($diffInDays <= 7) {
                    return true;
                }
            }
            return false;
        } catch (IOExceptionInterface $exception) {
            $this->logger->error(
                "An error occurred while working with the directory at " . $exception->getPath(),
                $exception->getTrace()
            );
            return false;
        }
    }

    /**
     * Decompress the gzipped file at $path with the same filename without the .gz
     * @param $compressedFilePath | the tar.gz file
     * @param $outputDecompressedFilePath
     * @param $tempWorkingDir
     */
    protected function decompressAndMove($compressedFilePath, $outputDecompressedFilePath, $tempWorkingDir)
    {
        $fileToUnzip = new PharData($compressedFilePath);
        $fileToUnzip->extractTo($tempWorkingDir, null, true);

        $finder = new Finder();
        $finder->files()->in($tempWorkingDir);
        $finder->files()->name(static::DB_FILE_SUFFIX);

        $amountOfDbFiles = $finder->count();
        if ($amountOfDbFiles > 1) {
            $this->logger->error('Could not load geo database: More than one geo database was found.');
            return;
        }

        foreach ($finder as $file) {
            try {
                $originalFilePath = $file->getPathname();

                $fileSystem = new Filesystem();
                $fileSystem->copy($originalFilePath, $outputDecompressedFilePath, true);
            } catch (IOExceptionInterface $exception) {
                $this->logger->error(
                    "An error occurred while copying the directory at " . $exception->getPath(),
                    $exception->getTrace()
                );
            }
        }
    }


    /**
     * Builds the file path for the geo database download
     * @param string $databaseUrl
     * @param string $tempWorkPath
     * @return string
     */
    protected function createDownloadFilePath(string $databaseUrl, string $tempWorkPath)
    {
        $remoteDownloadUrl = parse_url($databaseUrl);
        parse_str(implode($remoteDownloadUrl), $params);
        $urlSuffixParam = $params['suffix'];

        $downloadFilePath = $tempWorkPath . basename($remoteDownloadUrl['path']) . "." . $urlSuffixParam;
        return $downloadFilePath;
    }

    /**
     * @param string $tempWorkingDir
     */
    protected function removeTempWorkingDir(string $tempWorkingDir): void
    {
        try {
            $fileSystem = new Filesystem();
            $fileSystem->remove($tempWorkingDir);
        } catch (IOExceptionInterface $exception) {
            $this->logger->error(
                "An error occurred while removing the directory at " . $exception->getPath(),
                $exception->getTrace()
            );
        }
    }

    /**
     * @param string $tempWorkingDir
     */
    protected function createTempWorkingDir(string $tempWorkingDir)
    {
        try {
            $fileSystem = new Filesystem();
            if (!$fileSystem->exists($tempWorkingDir)) {
                $fileSystem->mkdir($tempWorkingDir);
            }
        } catch (IOExceptionInterface $exception) {
            $this->logger->error(
                "An error occurred while creating the directory at " . $exception->getPath(),
                $exception->getTrace()
            );
        }
    }

    /**
     * @param string $databaseUrl
     * @param string $tempWorkingDir
     * @return string
     */
    protected function downloadGeoDatabase(string $databaseUrl, string $tempWorkingDir): string
    {
        $tempDownloadFilePath = $this->createDownloadFilePath($databaseUrl, $tempWorkingDir);

        // Create HTTP client, fetch the file and save to cache
        $client = new Client();
        $this->logger->info('Started Geo Database download');
        $resource = \GuzzleHttp\Psr7\Utils::tryFopen($tempDownloadFilePath, 'w');
        $response = $client->get(
            $databaseUrl,
            [
                'sink' => $resource
            ]
        );
        $this->logger->info('Finished Geo Database download');
        return $tempDownloadFilePath;
    }
}
