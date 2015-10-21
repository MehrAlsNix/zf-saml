<?php // @codingStandardsIgnoreFile

return array(
    'controllers' => array(
        'factories' => array(
            'MehrAlsNix\ZF\SAML\Controller\Auth' => 'MehrAlsNix\ZF\SAML\Factory\AuthControllerFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'saml' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/saml',
                    'defaults' => array(
                        'controller' => 'MehrAlsNix\ZF\SAML\Controller\Auth',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'sso' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/sso',
                            'defaults' => array(
                                'action' => 'sso',
                            ),
                        ),
                    ),
                    'slo' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/slo',
                            'defaults' => array(
                                'action' => 'slo',
                            ),
                        ),
                    ),
                    'metadata' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/metadata',
                            'defaults' => array(
                                'action' => 'metadata',
                            ),
                        ),
                    ),
                    'consume' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/consume',
                            'defaults' => array(
                                'action' => 'consume',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'aliases' => array(
            'MehrAlsNix\ZF\SAML\Provider\UserId' => 'MehrAlsNix\ZF\SAML\Provider\UserId\AuthenticationService',
        ),
        'factories' => array(
            'MehrAlsNix\ZF\SAML\Adapter\PdoAdapter'    => 'MehrAlsNix\ZF\SAML\Factory\PdoAdapterFactory',
            'MehrAlsNix\ZF\SAML\Adapter\IbmDb2Adapter' => 'MehrAlsNix\ZF\SAML\Factory\IbmDb2AdapterFactory',
            'MehrAlsNix\ZF\SAML\Adapter\MongoAdapter'  => 'MehrAlsNix\ZF\SAML\Factory\MongoAdapterFactory',
            'MehrAlsNix\ZF\SAML\Provider\UserId\AuthenticationService' => 'MehrAlsNix\ZF\SAML\Provider\UserId\AuthenticationServiceFactory',
            'MehrAlsNix\ZF\SAML\Service\SAMLServer'  => 'MehrAlsNix\ZF\SAML\Factory\SAMLServerFactory'
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'saml/attributes' => __DIR__ . '/../view/zf/auth/attributes.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'zf-saml' => array(
        'grant_types' => array(
            'client_credentials' => true,
            'authorization_code' => true,
            'password'           => true,
            'refresh_token'      => true,
            'jwt'                => true,
        ),
        /*
         * Error reporting style
         *
         * If true, client errors are returned using the
         * application/problem+json content type,
         * otherwise in the format described in the saml specification
         * (default: true)
         */
        'api_problem_error_response' => true,
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'MehrAlsNix\ZF\SAML\Controller\Auth' => array(
                'ZF\ContentNegotiation\XmlModel' => array(
                    'application/xml',
                    'application/*+xml',
                ),
                'Zend\View\Model\ViewModel' => array(
                    'text/html',
                    'application/xhtml+xml',
                ),
            ),
        ),
    ),
);
