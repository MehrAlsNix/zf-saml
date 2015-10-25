<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

class Saml2ResponseFactory implements FactoryInterface
{
    /**
     * @var \OneLogin_Saml2_AuthnRequest
     */
    private $response;

    /**
     * Create an \OneLogin_Saml2_AuthnRequest instance.
     *
     * @param ServiceLocatorInterface $services
     *
     * @return \OneLogin_Saml2_Response
     *
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        if ($this->response) {
            return $this->response;
        }

        try {
            /** @var \OneLogin_Saml2_Settings $settings */
            $settings = $services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings');
            $request = $services->get('Request');
            return $this->response = new \OneLogin_Saml2_Response($settings, $request->getPost('SAMLResponse'));
        } catch (\Exception $e) {
            throw new Exception\RuntimeException($e);
        }
    }
}
