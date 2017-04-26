<?php
return [
    'zfcuser' => [
        'login_redirect_route'      => 'zfcuser',
        'logout_redirect_route'     => 'home',
        'enable_registration'       => true,
        'enable_display_name'       => false,
        'enable_username'           => false,
        'enable_user_state'         => true,
        'default_user_state'        => 0,
        'allowed_login_states'      => [1],
        'user_entity_class'         => CustomUser\Entity\User::class,
        'enable_default_entities'   => false,

        //Custom config
        'default_role'              => 'user',
    ],
];