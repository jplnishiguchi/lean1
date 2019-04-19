<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


return array(
     'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=playdough_dev;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => 'playdough_dev',
        'password' => 'sN8804xM',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'transactions' => array(
        'limit_per_page' => 10,
        'export_dir' => 'public/files/exports',
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
);

//$connection = mysql_connect('localhost', 'playdough_dev', 'sN8804xM')
//or die('Could not connect: ' . mysql_error());
//mysql_select_db('playdough_dev') or die('Could not select database');