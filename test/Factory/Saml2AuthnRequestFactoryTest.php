<?php

namespace MehrAlsNix\ZFTest\SAML\Factory;

use MehrAlsNix\ZF\SAML\Factory\Saml2AuthnRequestFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class Saml2AuthnRequestFactoryTest extends AbstractHttpControllerTestCase
{
    /**
     * @var Saml2AuthnRequestFactory
     */
    protected $factory;
    /**
     * @var ServiceManager
     */
    protected $services;
    protected function setUp()
    {
        $this->services = $services = new ServiceManager();
        $this->setApplicationConfig([
            'modules' => [
                'MehrAlsNix\ZF\SAML',
            ],
            'module_listener_options' => [
                'module_paths' => [__DIR__ . '/../../'],
                'config_glob_paths' => [],
            ],
            'service_listener_options' => [],
            'service_manager' => [],
        ]);
        parent::setUp();
        // @codingStandardsIgnoreStart
        $this->services->setService('Config', [
            'zf-saml' => [
                'sp' =>  [
                    'entityId' => 'http://stuff.com/endpoints/metadata.php',
                    'assertionConsumerService' =>  [
                        'url' => 'http://stuff.com/endpoints/endpoints/acs.php',
                    ],
                    'singleLogoutService' =>  [
                        'url' => 'http://stuff.com/endpoints/endpoints/sls.php',
                    ],
                    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:unspecified',
                ],
                'idp' =>  [
                    'entityId' => 'http://idp.example.com/',
                    'singleSignOnService' =>  [
                        'url' => 'http://idp.example.com/SSOService.php',
                    ],
                    'singleLogoutService' =>  [
                        'url' => 'http://idp.example.com/SingleLogoutService.php',
                    ],
                    'x509cert' => 'MIICgTCCAeoCCQCbOlrWDdX7FTANBgkqhkiG9w0BAQUFADCBhDELMAkGA1UEBhMCTk8xGDAWBgNVBAgTD0FuZHJlYXMgU29sYmVyZzEMMAoGA1UEBxMDRm9vMRAwDgYDVQQKEwdVTklORVRUMRgwFgYDVQQDEw9mZWlkZS5lcmxhbmcubm8xITAfBgkqhkiG9w0BCQEWEmFuZHJlYXNAdW5pbmV0dC5ubzAeFw0wNzA2MTUxMjAxMzVaFw0wNzA4MTQxMjAxMzVaMIGEMQswCQYDVQQGEwJOTzEYMBYGA1UECBMPQW5kcmVhcyBTb2xiZXJnMQwwCgYDVQQHEwNGb28xEDAOBgNVBAoTB1VOSU5FVFQxGDAWBgNVBAMTD2ZlaWRlLmVybGFuZy5ubzEhMB8GCSqGSIb3DQEJARYSYW5kcmVhc0B1bmluZXR0Lm5vMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDivbhR7P516x/S3BqKxupQe0LONoliupiBOesCO3SHbDrl3+q9IbfnfmE04rNuMcPsIxB161TdDpIesLCn7c8aPHISKOtPlAeTZSnb8QAu7aRjZq3+PbrP5uW3TcfCGPtKTytHOge/OlJbo078dVhXQ14d1EDwXJW1rRXuUt4C8QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBACDVfp86HObqY+e8BUoWQ9+VMQx1ASDohBjwOsg2WykUqRXF+dLfcUH9dWR63CtZIKFDbStNomPnQz7nbK+onygwBspVEbnHuUihZq3ZUdmumQqCw4Uvs/1Uvq3orOo/WJVhTyvLgFVK2QarQ4/67OZfHd7R+POBXhophSMv1ZOo',
                ],
            ],
        ]);
        $this->services->setFactory('MehrAlsNix\ZF\SAML\Service\SAML2Settings', 'MehrAlsNix\ZF\SAML\Factory\Saml2SettingsFactory');
        // @codingStandardsIgnoreEnd
        $this->factory = new Saml2AuthnRequestFactory($this->services);
    }

    /**
     * @test
     */
    public function instantiation()
    {
        $service = $this->factory->__invoke();
        $this->assertInstanceOf('OneLogin_Saml2_AuthnRequest', $service);
    }
}
