<?php

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

/**
 * Class Saml2MetadataFactory
 * @package MehrAlsNix\ZF\SAML\Factory
 */
class Saml2MetadataFactory implements FactoryInterface
{
    /**
     * @var \OneLogin_Saml2_Metadata
     */
    private $metadata;

    /**
     * Create an \OneLogin_Saml2_Metadata instance.
     *
     * @param ServiceLocatorInterface $services
     *
     * @return string
     *
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        if ($this->metadata) {
            return $this->metadata;
        }

        try {
            /** @var \OneLogin_Saml2_Settings $settings */
            $settings = $services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings');
        } catch (ServiceNotFoundException $e) {
            throw new Exception\RuntimeException($e);
        }

        return $this->metadata = \OneLogin_Saml2_Metadata::builder($settings->getSPData());
    }
}
