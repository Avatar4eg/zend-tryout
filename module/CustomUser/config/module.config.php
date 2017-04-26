<?php
namespace CustomUser;

use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'scn-social-auth-user' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => Controller\CustomUserController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'activate' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/activate',
                            'defaults' => [
                                'controller' => Controller\CustomUserController::class,
                                'action' => 'activate',
                            ],
                        ],
                    ],
                    'change-password' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/change-password',
                            'defaults' => [
                                'controller' => Controller\CustomUserController::class,
                                'action'     => 'change-password',
                            ],
                        ],
                    ],
                    'change-email' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/change-email',
                            'defaults' => [
                                'controller' => Controller\CustomUserController::class,
                                'action' => 'change-email',
                            ],
                        ],
                    ],
                    'change-data' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/change-data',
                            'defaults' => [
                                'controller' => Controller\CustomUserController::class,
                                'action' => 'change-data',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'invokables' => [
            \Zend\Session\SessionManager::class => \Zend\Session\SessionManager::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => './vendor/zf-commons/zfc-user/src/ZfcUser/language/',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\CustomUserController::class => Factory\CustomUserControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'zfc-user' => __DIR__ . '/../view',
            'scn-social-auth' => __DIR__ . '/../view',
            'CustomUser' => __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'custom_user_entity' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'paths' => [__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'custom_user_entity',
                ],
            ],
        ],
    ],
];
