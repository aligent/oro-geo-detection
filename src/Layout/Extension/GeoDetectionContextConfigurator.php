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

namespace Aligent\GeoDetectionBundle\Layout\Extension;

use Aligent\GeoDetectionBundle\Providers\RedirectionConfigurationProvider;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Oro\Component\Layout\ContextConfiguratorInterface;
use Oro\Component\Layout\ContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GeoDetectionContextConfigurator implements ContextConfiguratorInterface
{
    const DISPLAY_REDIRECT_MODAL = 'display_redirect_modal';
    const ENABLED = 'geo_detection_enabled';
    const COOKIE_NAME = 'displayRedirectModal';

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
     * @param ContextInterface $context
     * @throws InvalidDatabaseException
     */
    public function configureContext(ContextInterface $context)
    {
        $context->getResolver()->setDefined(self::DISPLAY_REDIRECT_MODAL);
        $context->getResolver()->setDefined(self::ENABLED);
        $context->set(self::DISPLAY_REDIRECT_MODAL, false); // By default don't show
        $context->set(self::ENABLED, $this->redirectionConfigProvider->isEnabled());

        if (!$this->frontendHelper->isFrontendRequest()
            || !$this->redirectionConfigProvider->isEnabled()
            || !$this->redirectionConfigProvider->hasRedirects()
        ) {
            return;
        }

        // check if cookie has been set
        if ($this->request->cookies->has(static::COOKIE_NAME)) {
            return;
        }

        try {
            // Fetch current country based on IP Address
            $record = $this->reader->city($this->request->getClientIp());
            $currentCountry = $record->country->isoCode;
        } catch (AddressNotFoundException $e) {
            // IP could not be matched in the DB, so trust the user knows what website they are on
            return;
        }

        // Filter config down to just the website that matches the users current country and the default
        $suggestedSite = $this->redirectionConfigProvider->getSuggestedSite($currentCountry);
        $defaultSite = $this->redirectionConfigProvider->getDefaultWebsite();

        // If they match or there is no suggested site don't redirect
        if ($suggestedSite === $defaultSite || !$suggestedSite) {
            return;
        }

        $context->set(self::DISPLAY_REDIRECT_MODAL, true);
    }
}
