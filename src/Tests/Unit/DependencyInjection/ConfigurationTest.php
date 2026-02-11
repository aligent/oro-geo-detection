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

use Aligent\GeoDetectionBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigTreeBuilder(): void
    {
        $configuration = new Configuration();
        $builder = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $builder);

        $root = $builder->buildTree();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\ArrayNode', $root);
        $this->assertEquals('aligent_geo_detection', $root->getName());
    }

    public function testProcessConfiguration(): void
    {
        $configuration = new Configuration();
        $processor     = new Processor();

        $expected =  [
            'settings' => [
                'resolved' => true,
                'enabled' => [
                    'value' => false,
                    'scope' => 'app'
                ],
                'enabled_countries' => [
                    'value' => [],
                    'scope' => 'app'
                ],
                'database_download_url' => [
                    'value' => Configuration::INITIAL_DOWNLOAD_URL_VALUE,
                    'scope' => 'app'
                ]
            ],
        ];

        $this->assertEquals($expected, $processor->processConfiguration($configuration, []));
    }
}
