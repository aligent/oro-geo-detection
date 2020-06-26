<?php

namespace Aligent\GeoDetectionBundle\Providers;

use Doctrine\Common\Cache\CacheProvider;
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
     * GeoDetectionProvider constructor.
     * @param RequestStack $requestStack
     * @param Reader $reader
     * @param FrontendHelper $frontendHelper
     * @param CacheProvider $cache
     */
    public function __construct(
        RequestStack $requestStack,
        Reader $reader,
        FrontendHelper $frontendHelper,
        CacheProvider $cache
    ) {
        $this->requestStack = $requestStack;
        $this->reader = $reader;
        $this->frontendHelper = $frontendHelper;
        $this->cache = $cache;
    }

    /**
     * @return string - ISO2 Country Code of the origin IP address
     */
    public function getClientCountry(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClientIp();

        if (!$this->cache->contains($ip)) {
            $record = $this->reader->city($request->getClientIp());
            $country = $record->country->isoCode;
            $this->cache->save($ip, $country, static::CACHE_TTL);
        } else {
            $country = $this->cache->fetch($ip);
        }

        return $country;
    }
}