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
use Oro\Bundle\AddressBundle\Form\Type\CountryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeoDetectionCountryType extends AbstractType
{
    const NAME = 'aligent_geo_detection_country';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'label',
            TextType::class,
            [

            ]
        )->add(
            'url',
            UrlType::class,
            [

            ]
        )->add(
            'country',
            CountryType::class,
            [
                'required' => true,
                'label' => 'oro.address.country.label'
            ]
        )->add(
            'default',
            CheckboxType::class
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CountryConfig::class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}
