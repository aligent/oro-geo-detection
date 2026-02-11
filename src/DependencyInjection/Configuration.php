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

namespace Aligent\GeoDetectionBundle\DependencyInjection;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const ENABLED = 'enabled';
    const ENABLED_COUNTRIES = 'enabled_countries';
    const DATABASE_DOWNLOAD_URL = 'database_download_url';

    const LICENSE_KEY = 'VsAiIiW6Crumg67J';
    const INITIAL_DOWNLOAD_URL_VALUE =
        'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=' .
        self::LICENSE_KEY .
        '&suffix=tar.gz';

    /**
     * Generates the configuration tree builder.
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(AligentGeoDetectionExtension::ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                self::ENABLED => ['type' => 'boolean', 'value' => false],
                self::ENABLED_COUNTRIES => ['type' => 'array', 'value' => []],
                self::DATABASE_DOWNLOAD_URL => ['type' => 'string', 'value' => self::INITIAL_DOWNLOAD_URL_VALUE],
            ]
        );

        $rootNode
            ->children()
                ->scalarNode('database')
                ->end();

        return $treeBuilder;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getConfigKeyByName($name): string
    {
        return sprintf(
            AligentGeoDetectionExtension::ALIAS . '%s%s',
            ConfigManager::SECTION_MODEL_SEPARATOR,
            $name
        );
    }
}
