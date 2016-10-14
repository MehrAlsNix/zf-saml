<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Saml2SettingsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     *
     * @return \OneLogin_Saml2_Settings
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \OneLogin_Saml2_Error
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('Config');
        $config = isset($config['zf-saml']) ? $config['zf-saml'] : [];

        return new \OneLogin_Saml2_Settings($config);
    }
}
