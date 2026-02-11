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

namespace Aligent\GeoDetectionBundle\Form\Type;

use Aligent\GeoDetectionBundle\SystemConfig\CountryConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeoDetectionCountriesSystemConfigType extends AbstractType
{
    const NAME = 'aligent_geo_detection_countries_system_config';

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_type' => GeoDetectionCountryType::class,
            'entry_options' => [
                'data_class' => CountryConfig::class
            ],
            'allow_add' => true,
            'mapped' => true,
            'label' => false
        ]);
    }

    /**
     * @return string
     */
    public function getParent(): ?string
    {
        return GeoDetectionCountriesCollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return static::NAME;
    }
}
