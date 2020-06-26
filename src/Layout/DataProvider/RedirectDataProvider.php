<?php
/**
 *
 *
 * @category  Aligent
 * @package
 * @author    Adam Hall <adam.hall@aligent.com.au>
 * @copyright 2018 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */

namespace Aligent\GeoDetectionBundle\Layout\DataProvider;

use Aligent\GeoDetectionBundle\Providers\RedirectionConfigurationProvider;
use Aligent\GeoDetectionBundle\SystemConfig\CountryConfig;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RedirectDataProvider
{
    /** @var Reader */
    protected $reader;

    /** @var FrontendHelper */
    protected $frontendHelper;

    /** @var  RedirectionConfigurationProvider */
    protected $redirectionConfigProvider;

    /** @var Request */
    protected $request;

    /**
     * GeoDetectionContextConfigurator constructor.
     * @param Reader $reader
     * @param FrontendHelper $frontendHelper
     * @param RedirectionConfigurationProvider $configurationProvider
     * @param RequestStack $requestStack
     */
    public function __construct(
        Reader $reader,
        FrontendHelper $frontendHelper,
        RedirectionConfigurationProvider $configurationProvider,
        RequestStack $requestStack
    ) {

        $this->reader = $reader;
        $this->frontendHelper = $frontendHelper;
        $this->redirectionConfigProvider = $configurationProvider;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return CountryConfig|void
     * @throws InvalidDatabaseException
     */
    private function getSuggestedSite()
    {
        try {
            // Fetch current country based on IP Address
            $record = $this->reader->city($this->request->getClientIp());
            $currentCountry = $record->country->isoCode;
        } catch (AddressNotFoundException $e) {
            // IP could not be matched in the DB, so trust the user knows what website they are on
            return;
        }

        return $this->redirectionConfigProvider->getSuggestedSite($currentCountry);
    }

    /**
     * @return array
     */
    public function getAlternateWebsites()
    {
        $sites = $this->redirectionConfigProvider->getEnabledWebsites();

        uasort($sites, function (CountryConfig $a, CountryConfig $b) {
            return $a->isDefault() ? -1 : 1;
        });

        return $sites;
    }

    /**
     * @return array
     * @throws InvalidDatabaseException
     */
    public function getRedirectionConfig()
    {
        $suggestedSite = $this->getSuggestedSite();
        $defaultSite = $this->redirectionConfigProvider->getDefaultWebsite();

        return [
            'defaultSiteLocale'   => $defaultSite->getCountry()->getName(),
            'defaultSiteUrl'      => $defaultSite->getUrl(),
            'suggestedSiteLocale' => $suggestedSite->getCountry()->getName(),
            'suggestedSiteUrl'    => $suggestedSite->getUrl(),
        ];
    }
}
