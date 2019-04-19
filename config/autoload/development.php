<?php

return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=lean1;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => 'lean1',
        'password' => 'lean1',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'transactions' => array(
        'limit_per_page' => 10,
        'export_dir' => 'public/files/exports'
    ),
    'users' => array(
        'limit_per_page' => 10,
    ),
    'roles' => array(
        'limit_per_page' => 10,
    ),
    'options' => array(
        'limit_per_page' => 5,
    ),
    'pages' => array(
        'limit_per_page' => 10,
    ),
    'weblogs' => array(
        'limit_per_page' => 10,
    ),
    'request' => array(
        'limit_per_page' => 10,
	),
	'holiday' => array(
        'limit_per_page' => 10,
	)
);
