<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

class Saml2AuthFactory implements FactoryInterface
{
    private $auth;

    /**
     * @param ServiceLocatorInterface $services
     * @return \OneLogin_Saml2_Settings
     */
    public function createService(ServiceLocatorInterface $services)
    {
        if ($this->auth) {
            return $this->auth;
        }

        try {
            /** @var \OneLogin_Saml2_Settings $settings */
            $settings = $services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings');

            return $this->auth = new \OneLogin_Saml2_Auth();
        } catch (\Exception $e) {
            throw new Exception\RuntimeException($e);
        }
    }
}
