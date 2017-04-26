<?php
return [
    'bjyauthorize' => [
        'default_role'          => 'guest',
        'unauthorized_strategy' => \BjyAuthorize\View\UnauthorizedStrategy::class,
        'template'              => 'error/404',
        'identity_provider'     => \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::class,
        'role_providers'        => [
            \BjyAuthorize\Provider\Role\ObjectRepositoryProvider::class => [
                'object_manager'    => 'doctrine.entitymanager.orm_default',
                'role_entity_class' => \CustomUser\Entity\Role::class,
            ],
        ],
        'resource_providers'    => [],
        'rule_providers'        => [],
        'guards'                => [
            \BjyAuthorize\Guard\Route::class => [
                ['route' => 'home',                                         'roles' => ['guest', 'user']],
                ['route' => 'application',                                  'roles' => ['guest', 'user']],
                ['route' => 'scn-social-auth-user',                         'roles' => ['user']],
                ['route' => 'scn-social-auth-user/login',                   'roles' => ['guest']],
                ['route' => 'scn-social-auth-user/login/provider',          'roles' => ['guest']],
                ['route' => 'scn-social-auth-user/register',                'roles' => ['guest']],
                ['route' => 'scn-social-auth-user/logout',                  'roles' => ['user']],
                ['route' => 'scn-social-auth-user/authenticate',            'roles' => ['guest']],
                ['route' => 'scn-social-auth-user/authenticate/provider',   'roles' => ['guest']],
                ['route' => 'scn-social-auth-user/change-password',         'roles' => ['user']],
                ['route' => 'scn-social-auth-user/change-email',            'roles' => ['user']],
                ['route' => 'scn-social-auth-user/change-data',             'roles' => ['user']],
                ['route' => 'scn-social-auth-user/activate',                'roles' => ['guest']],
                ['route' => 'scn-social-auth-hauth',                        'roles' => ['guest']],
                ['route' => 'scn-social-auth-user/add-provider/provider',   'roles' => ['guest']],
            ]
        ]
    ]
];
