<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

class Saml2AuthnRequestFactory implements FactoryInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    private $services;

    /**
     * @var \OneLogin_Saml2_AuthnRequest
     */
    private $request;

    /**
     * Create an \OneLogin_Saml2_AuthnRequest instance.
     *
     * @param ServiceLocatorInterface $services
     *
     * @return \OneLogin_Saml2_AuthnRequest
     */
    public function createService(ServiceLocatorInterface $services)
    {
        if ($this->request) {
            return $this->request;
        }

        $settings = $services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings');
        return $this->request = new \OneLogin_Saml2_AuthnRequest($settings);
    }
}
