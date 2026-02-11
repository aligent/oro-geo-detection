<?php
/**
 * @category  Aligent
 * @package
 * @author    Chris Rossi <chris.rossi@aligent.com.au>
 * @copyright 2022 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */

namespace Aligent\GeoDetectionBundle\Tests\Unit\DependencyInjection;

use Aligent\GeoDetectionBundle\DependencyInjection\AligentGeoDetectionExtension;
use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;

class AligentGeoDetectionExtensionTest extends ExtensionTestCase
{
    public function testLoad(): void
    {
        $this->loadExtension(new AligentGeoDetectionExtension(), ['aligent_geo_detection' => ['database' => '']]);

        // Services
        $expectedDefinitions = [
            'aligent_geo_detection.form.type.geo_detection_countries_collection',
            'aligent_geo_detection.form.type.geo_detection_country',
            'aligent_geo_detection.form.type.geo_detection_system_config',
            'aligent_geo_detection.reader',
            'aligent_geo_detection.layout_context_configurator.geo_detection',
            'aligent_geo_detection.provider.redirection_config_provider',
            'aligent_geo_detection.provider.geo_detection',
            'aligent_geo_detection.cache',
            'aligent_geo_detection.layout.block_type.redirection_block',
            'aligent_geo_detection.layout.block_type.site_select_block',
            'aligent_geo_detection.layout.data_provider.redirection_provider',
            'aligent_geo_detection.cache.warmer',
        ];
        $this->assertDefinitionsLoaded($expectedDefinitions);

        $expectedExtensionConfigs = ['aligent_geo_detection'];
        $this->assertExtensionConfigsLoaded($expectedExtensionConfigs);
    }
}
