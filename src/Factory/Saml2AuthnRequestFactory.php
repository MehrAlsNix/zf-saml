<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

class Saml2AuthnRequestFactory
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
     * @var ServiceLocatorInterface $services ServiceLocator for retrieving storage adapters.
     */
    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
    }

    /**
     * Create an \OneLogin_Saml2_AuthnRequest instance.
     *
     * @return \OneLogin_Saml2_AuthnRequest
     *
     * @throws Exception\RuntimeException
     */
    public function __invoke()
    {
        if ($this->request) {
            return $this->request;
        }

        $settings = $this->services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings');
        return $this->request = new \OneLogin_Saml2_AuthnRequest($settings);
    }
}
