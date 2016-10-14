<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

/**
 * Class Saml2AuthnRequestFactory
 * @package MehrAlsNix\ZF\SAML\Factory
 */
class Saml2AuthnRequestFactory implements FactoryInterface
{
    /**
     * @var \OneLogin_Saml2_AuthnRequest
     */
    private $authnRequest;

    /**
     * Create an \OneLogin_Saml2_AuthnRequest instance.
     *
     * @param ServiceLocatorInterface $services
     *
     * @return \OneLogin_Saml2_AuthnRequest
     *
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        if ($this->authnRequest) {
            return $this->authnRequest;
        }

        try {
            /** @var \OneLogin_Saml2_Settings $settings */
            $settings = $services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings');
        } catch (ServiceNotFoundException $e) {
            throw new Exception\RuntimeException($e);
        }

        return $this->authnRequest = new \OneLogin_Saml2_AuthnRequest($settings);
    }
}
