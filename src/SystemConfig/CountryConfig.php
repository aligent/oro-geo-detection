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

namespace Aligent\GeoDetectionBundle\SystemConfig;

use Oro\Bundle\AddressBundle\Entity\Country;

class CountryConfig
{
    /** @var string */
    protected $label;

    /** @var string */
    protected $url;

    /** @var Country */
    protected $country;

    /** @var bool */
    protected $default;

    /**
     * CountryConfig constructor.
     * @param string $label
     * @param string $url
     * @param string $country
     * @param bool $default
     */
    public function __construct($label = null, $url = null, $country = null, $default = null)
    {
        $this->label = $label;
        $this->url = $url;
        $this->country = $country;
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return CountryConfig
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return CountryConfig
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return \Extend\Entity\EX_OroAddressBundle_Country|Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \Extend\Entity\EX_OroAddressBundle_Country|Country $country
     * @return CountryConfig
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param bool $default
     * @return CountryConfig
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'label' => $this->getLabel(),
            'url' => $this->getUrl(),
            'country' => $this->getCountry(),
            'isDefault' => $this->isDefault()
        ];
    }
}
