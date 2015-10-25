<?php // @codingStandardsIgnoreFile

return [
    'controllers' => [
        'factories' => [
            'MehrAlsNix\ZF\SAML\Controller\Auth' => 'MehrAlsNix\ZF\SAML\Factory\AuthControllerFactory',
        ],
    ],
    'router' => [
        'routes' => [
            'saml' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/saml',
                    'defaults' => [
                        'controller' => 'MehrAlsNix\ZF\SAML\Controller\Auth',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'sso' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route' => '/sso',
                            'defaults' => [
                                'action' => 'sso',
                            ],
                        ],
                    ],
                    'slo' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route' => '/slo',
                            'defaults' => [
                                'action' => 'slo',
                            ],
                        ],
                    ],
                    'metadata' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route' => '/metadata',
                            'defaults' => [
                                'action' => 'metadata',
                            ],
                        ],
                    ],
                    'consume' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route' => '/consume',
                            'defaults' => [
                                'action' => 'consume',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'MehrAlsNix\ZF\SAML\Provider\UserId' => 'MehrAlsNix\ZF\SAML\Provider\UserId\AuthenticationService',
        ],
        'factories' => [
            'MehrAlsNix\ZF\SAML\Adapter\PdoAdapter'    => 'MehrAlsNix\ZF\SAML\Factory\PdoAdapterFactory',
            'MehrAlsNix\ZF\SAML\Adapter\IbmDb2Adapter' => 'MehrAlsNix\ZF\SAML\Factory\IbmDb2AdapterFactory',
            'MehrAlsNix\ZF\SAML\Adapter\MongoAdapter'  => 'MehrAlsNix\ZF\SAML\Factory\MongoAdapterFactory',
            'MehrAlsNix\ZF\SAML\Provider\UserId\AuthenticationService' => 'MehrAlsNix\ZF\SAML\Provider\UserId\AuthenticationServiceFactory',
            'MehrAlsNix\ZF\SAML\Service\SAML2Settings' => 'MehrAlsNix\ZF\SAML\Factory\Saml2SettingsFactory',
            'MehrAlsNix\ZF\SAML\Service\SAML2AuthnRequest' => 'MehrAlsNix\ZF\SAML\Factory\Saml2AuthnRequestFactory',
            'MehrAlsNix\ZF\SAML\Service\SAML2Metadata' => 'MehrAlsNix\ZF\SAML\Factory\Saml2MetadataFactory',
            'MehrAlsNix\ZF\SAML\Service\SAML2Auth' => 'MehrAlsNix\ZF\SAML\Factory\Saml2AuthFactory',
            'MehrAlsNix\ZF\SAML\Service\SAML2Response' => 'MehrAlsNix\ZF\SAML\Factory\Saml2ResponseFactory'
        ]
    ],
    'view_manager' => [
        'template_map' => [
            'saml/attributes' => __DIR__ . '/../view/zf/auth/attributes.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'zf-saml' => [
        'grant_types' => [
            'client_credentials' => true,
            'authorization_code' => true,
            'password'           => true,
            'refresh_token'      => true,
            'jwt'                => true,
        ],
        /*
         * Error reporting style
         *
         * If true, client errors are returned using the
         * application/problem+json content type,
         * otherwise in the format described in the saml specification
         * (default: true)
         */
        'api_problem_error_response' => true,
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'MehrAlsNix\ZF\SAML\Controller\Auth' => [
                'ZF\ContentNegotiation\XmlModel' => [
                    'application/xml',
                    'application/*+xml',
                ],
                'Zend\View\Model\ViewModel' => [
                    'text/html',
                    'application/xhtml+xml',
                ],
            ],
        ],
    ],
];
