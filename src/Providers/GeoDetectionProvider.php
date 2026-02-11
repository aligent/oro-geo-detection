<?php

namespace Aligent\GeoDetectionBundle\Providers;

use Doctrine\Common\Cache\CacheProvider;
use GeoIp2\Database\Reader;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class GeoDetectionProvider
 *
 * @category  Aligent
 * @package   Aligent\GeoDetectionBundle\Providers
 * @author    Adam Hall <adam.hall@aligent.com.au>
 * @copyright 2020 Aligent Consulting.
 * @link      http://www.aligent.com.au/
 */
class GeoDetectionProvider
{
    const CACHE_TTL = 3600;
    // @todo: Extract this to a configurable option
    const FALLBACK_COUNTRY_CODE = 'AU';

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var FrontendHelper
     */
    protected $frontendHelper;

    /**
     * @var CacheProvider
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * GeoDetectionProvider constructor.
     * @param RequestStack $requestStack
     * @param Reader $reader
     * @param FrontendHelper $frontendHelper
     * @param CacheProvider $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestStack $requestStack,
        Reader $reader,
        FrontendHelper $frontendHelper,
        CacheProvider $cache,
        LoggerInterface $logger
    ) {
        $this->requestStack = $requestStack;
        $this->reader = $reader;
        $this->frontendHelper = $frontendHelper;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @return string|null - ISO2 Country Code of the origin IP address
     */
    public function getClientCountry(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClientIp();

        if (!$this->cache->contains($ip)) {
            try {
                $record = $this->reader->city($request->getClientIp());
                $country = $record->country->isoCode;
                $this->cache->save($ip, $country, static::CACHE_TTL);
            } catch (\Exception $exception) {
                $this->logger->debug('Failed to read country code from IP database.');
                $country = static::FALLBACK_COUNTRY_CODE;
            }
        } else {
            $country = $this->cache->fetch($ip);
        }

        return $country;
    }
}
