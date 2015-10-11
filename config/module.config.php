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
                        'action'     => 'token',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'authorize' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/authorize',
                            'defaults' => array(
                                'action' => 'authorize',
                            ),
                        ),
                    ),
                    'resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/resource',
                            'defaults' => array(
                                'action' => 'resource',
                            ),
                        ),
                    ),
                    'code' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/receivecode',
                            'defaults' => array(
                                'action' => 'receiveCode',
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
            'MehrAlsNix\ZF\SAML\Service\OAuth2Server'  => 'MehrAlsNix\ZF\SAML\Factory\OAuth2ServerFactory'
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'oauth/authorize'    => __DIR__ . '/../view/zf/auth/authorize.phtml',
            'oauth/receive-code' => __DIR__ . '/../view/zf/auth/receive-code.phtml',
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
