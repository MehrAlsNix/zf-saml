<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use MehrAlsNix\ZF\SAML\Controller\AuthController;
use OneLogin_Saml2_Auth as SamlServer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllers
     * @return AuthController
     */
    public function createService(ServiceLocatorInterface $controllers)
    {
        $services = $controllers->getServiceLocator()->get('ServiceManager');
        $samlServerFactory = $services->get('MehrAlsNix\ZF\SAML\Service\SamlServer');
        if ($samlServerFactory instanceof SamlServer) {
            $samlServer = $samlServerFactory;
            $samlServerFactory = function () use ($samlServer) {
                return $samlServer;
            };
        }
        $authController = new AuthController(
            $samlServerFactory,
            $services->get('MehrAlsNix\ZF\SAML\Provider\UserId')
        );
        $config = $services->get('Config');
        $authController->setApiProblemErrorResponse((isset($config['zf-saml']['api_problem_error_response'])
            && $config['zf-saml']['api_problem_error_response'] === true));
        return $authController;
    }
}