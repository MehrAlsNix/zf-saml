<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SamlServerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @return \OneLogin_Saml2_Auth
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('Config');
        $config = isset($config['zf-saml']) ? $config['zf-saml'] : [];
        return new SamlServerInstanceFactory($config, $services);
    }
}
