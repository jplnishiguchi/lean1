<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
             'login' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/login[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Login',
                        'action' => 'index',
                    ),
                ),
            ),

            'not-allowed' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/not-allowed',
                    'defaults' => array(
                        'controller' => 'Application\Controller\CustomError',
                        'action' => 'not-allowed',
                    ),
                ),
            ),
            'test-index' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/test-index',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Test',
                        'action' => 'index',
                    ),
                ),
            ),
            'user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action' => 'index',
                    ),
                ),
            ),
            'time' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/time[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Time',
                        'action' => 'index',
                    ),
                ),
            ),
           'employee' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/employee[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Employee',
                        'action' => 'index',
                    ),
                ),
            ),
            'reftable' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/reftable[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Reftable',
                        'action' => 'sss',
                    ),
                ),
            ),
            'pay' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/pay[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Pay',
                        'action' => 'sss',
                    ),
                ),
            ),
	     'transactions' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/transactions[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Transactions',
                        'action' => 'index',
                    ),
                ),
            ),
            'roles' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/roles[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Roles',
                        'action' => 'index',
                    ),
                ),
            ),
            'pages' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/pages[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Pages',
                        'action' => 'index',
                    ),
                ),
            ),
            'pwdchange' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/pwdchange[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Pwdchange',
                        'action' => 'index',
                    ),
                ),
            ),
            'pwdreset' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/pwdreset[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Pwdreset',
                        'action' => 'index',
                    ),
                ),
            ),
             'options' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/options[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Options',
                        'action' => 'index',
                    ),
                ),
            ),
            'weblogs' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/weblogs[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Weblogs',
                        'action' => 'index',
                    ),
                ),
            ),
			'request' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/request[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Request',
                        'action' => 'index',
                    ),
                ),
            ),
            'schedules' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/schedules[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Schedules',
                        'action' => 'index',
                    ),
                ),
            ),
            'empgroup' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/empgroup[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Empgroup',
                        'action' => 'index',
                    ),
                ),
            ),
            'timeclock' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/timeclock[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Timeclock',
                        'action' => 'index',
                    ),
                ),
            ),
            'holiday' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/holiday[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Holiday',
                        'action' => 'index',
                    ),
                ),
            ),

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                       '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Login' => 'Application\Controller\LoginController',
            'Application\Controller\User' => 'Application\Controller\UserController',
            'Application\Controller\CustomError' => 'Application\Controller\CustomErrorController',
            'Application\Controller\Test' => 'Application\Controller\TestController',
            'Application\Controller\Transactions' => 'Application\Controller\TransactionsController',
            'Application\Controller\Roles' => 'Application\Controller\RolesController',
            'Application\Controller\Pages' => 'Application\Controller\PagesController',
            'Application\Controller\Pwdchange' => 'Application\Controller\PwdchangeController',
            'Application\Controller\Pwdreset' => 'Application\Controller\PwdresetController',
            'Application\Controller\Options' => 'Application\Controller\OptionsController',
            'Application\Controller\Weblogs' => 'Application\Controller\WeblogsController',
            'Application\Controller\Time' => 'Application\Controller\TimeController',
	    'Application\Controller\Request' => 'Application\Controller\RequestController',
            'Application\Controller\Employee' => 'Application\Controller\EmployeeController',
            'Application\Controller\Schedules' => 'Application\Controller\SchedulesController',
            'Application\Controller\Empgroup' => 'Application\Controller\EmpgroupController',
            'Application\Controller\Timeclock' => 'Application\Controller\TimeclockController',
            'Application\Controller\Holiday' => 'Application\Controller\HolidayController',
            'Application\Controller\Reftable' => 'Application\Controller\ReftableController',
            'Application\Controller\Pay' => 'Application\Controller\PayController',

        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/nav' => __DIR__ . '/../view/layout/nav.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(
        // added for Authentication and Authorization. Without this each time we have to create a new instance.
        // This code should be moved to a module to allow Doctrine to overwrite it
        'aliases' => array(// !!! aliases not alias
            'Zend\Authentication\AuthenticationService' => 'my_auth_service',
        ),
        'invokables' => array(
            'my_auth_service' => 'Zend\Authentication\AuthenticationService',
        ),
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'static_salt' => 'rjcmZx1%S0];AxV9d',
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);

