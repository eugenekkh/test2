<?php

return array(
    'doctrine' => array(
        'driver'   => 'pdo_mysql',
        'user'     => 'root',
        'password' => '',
        'dbname'   => 'test2',
    ),
    'doctrine_entity_files' => array(
        __DIR__ . '/Model',
    ),
    'router' => array(
        'default' => '/site/index',
        'routes' => array(
            '/site/index' => 'App:Controller:Site:index',
            '/site/preview' => 'App:Controller:Site:preview',
            '/admin/index' => 'App:Controller:Admin:index',
            '/admin/edit' => 'App:Controller:Admin:edit',
            '/admin/login' => 'App:Controller:Admin:login',
            '/admin/logout' => 'App:Controller:Admin:logout',
        )
    ),
    'twig' => array(
        'templates' => __DIR__ . '/Resources/views'
    ),
    'upload_path' => __DIR__ . '/../web/upload'
);
