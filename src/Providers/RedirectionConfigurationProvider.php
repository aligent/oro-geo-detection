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

namespace Aligent\GeoDetectionBundle\Providers;

use Aligent\GeoDetectionBundle\DependencyInjection\Configuration;
use Aligent\GeoDetectionBundle\SystemConfig\CountryConfig;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class RedirectionConfigurationProvider
{
    /** @var ConfigManager */
    protected $configManager;

    /** @var CountryConfig[] */
    protected $enabledRedirects;

    /** @var bool */
    protected $enabled;

    /**
     * RedirectionConfigurationProvider constructor.
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
        $this->enabledRedirects = $this->configManager->get(
            Configuration::getConfigKeyByName('enabled_countries')
        );
        $this->enabled = $this->configManager->get(
            Configuration::getConfigKeyByName('enabled')
        );
    }

    /**
     * @param $currentCountry
     * @return CountryConfig|void
     */
    public function getSuggestedSite($currentCountry)
    {
        /** @var CountryConfig $site */
        foreach ($this->enabledRedirects as $site) {
            if ($site->getCountry()->getIso2Code() === $currentCountry) {
                return $site;
            }
        }
    }

    /**
     * @return CountryConfig|void
     */
    public function getDefaultWebsite()
    {
        /** @var CountryConfig $site */
        foreach ($this->enabledRedirects as $site) {
            if ($site->isDefault()) {
                return $site;
            }
        }
        return;
    }

    /**
     * @return array
     */
    public function getEnabledWebsites(): array
    {
        return $this->enabledRedirects;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function hasRedirects(): bool
    {
        return count($this->enabledRedirects) > 0;
    }
}
