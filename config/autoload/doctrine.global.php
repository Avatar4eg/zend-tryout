<?php
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Doctrine\DBAL\Driver\PDOMySql\Driver::class,
                'params'      => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => '%username%',
                    'password' => '%password%',
                    'dbname'   => '%dbname%',
                    'charset' => 'UTF8',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ],
                ],
                'doctrine_type_mappings' => [
                    'enum' => 'string'
                ],
            ]
        ]
    ]
];