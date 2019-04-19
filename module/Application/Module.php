<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\Storage\Session;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\UserObject;
use Database\Model\UserTable;
use Database\Model\OptionsObject;
use Database\Model\OptionsTable;
use Database\Model\RolesObject;
use Database\Model\RolesTable;
use Database\Model\PagesObject;
use Database\Model\PagesTable;
use Database\Model\RolesPagesObject;
use Database\Model\RolesPagesTable;
use Utilities\Playdough\Logs;
use Utilities\Playdough\Acl;
use Zend\Http\PhpEnvironment\Request;

class Module {

    public function onBootstrap(MvcEvent $e) {

        $eventManager = $e->getApplication()->getEventManager();
        $checkAcl = $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {

            $route = $e->getRouteMatch()->getParams();
            $auth = new AuthenticationService();
            $isLogin = $auth->hasIdentity();
            if ($route['controller'] == 'Application\Controller\Login') {
                if ($isLogin) {
                    $response = $e->getResponse();
                    $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/');
                    $response->setStatusCode(303);
                }
            } else {
                if (!$isLogin) {
                    $response = $e->getResponse();
                    $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/login');
                    $response->setStatusCode(303);
                } else {
                    //$userConf = include __DIR__ . '/../../config/autoload/user.options.config.php';
                    //var_dump($userConf); die();
                    //reset session inactivity timeout every user click

                    $sessionManager = new \Zend\Session\SessionManager();
                    $sessionStorage = $sessionManager->getStorage();

                    $isNewUser = $sessionStorage->getMetaData('is_new_user');
                    if ($isNewUser == 1) {
                        if ($route['controller'] == 'Application\Controller\Pwdchange') {
                            //continue
                        } else {
                            $response = $e->getResponse();
                            $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/pwdchange');
                            $response->setStatusCode(303);
                        }
                    }


                    $inact_sec = $sessionStorage->getMetaData('inactivity_timeout_seconds');
                    $date = new \DateTime();
                    $lastLog = new \DateTime();
                    try {
                        $date->add(new \DateInterval('PT' . $inact_sec . 'S'));
                    } catch (\Exception $e) {
                         $date->add(new \DateInterval('PT3600S'));
                    }
                    $login_exp_date = $date->format('Y-m-d H:i:s');
                    $env = getenv('APPLICATION_ENV') ? : 'production';
                    $config = include __DIR__ . '/../../config/autoload/' . $env . '.php';
                    $adapter = new Adapter($config['db']);
                    $user = new UserObject();
                    $userGateway = new TableGateway($user->getTablename(), $adapter);
                    $userTable = new UserTable($userGateway);
                    $userTable->update(array('login_exp_date' => $login_exp_date, 'last_activity_time' => $lastLog->format('Y-m-d H:i:s')), array('username' => $auth->getIdentity()->username));

                    $sessionManager->rememberMe($inact_sec);
                }
            }
        });



        // add logs post dispatch of requests..
        $logEndRequest = $eventManager->attach(MvcEvent::EVENT_FINISH, function($e) {

            if (!is_null($e->getRouteMatch())) {
                $route = $e->getRouteMatch()->getMatchedRouteName();
                $controllerFull = $e->getRouteMatch()->getParam('controller');
                $contArray = explode("\\", $controllerFull);
                $controller = strtolower($contArray[count($contArray) - 1]);
                $action = $e->getRouteMatch()->getParam('action');

                $auth = new AuthenticationService();
                $isLogin = $auth->hasIdentity();

                $ipaddress = $_SERVER['REMOTE_ADDR'];
                $url = $_SERVER['REQUEST_URI'];
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $ref = $_SERVER['HTTP_REFERER'];
                    $sourceURL = str_replace("http://" . $_SERVER['HTTP_HOST'], "", $ref);
                    $sourceURL = str_replace("https://" . $_SERVER['HTTP_HOST'], "", $sourceURL);
                } else {
                    $sourceURL = NULL;
                }
                $data = NULL;
                if ($e->getViewModel()->hasChildren()) {
                    $children = $e->getViewModel()->getChildren();
                    $data = array();
                    foreach ($children as $child) {
                        $childData = $child->getVariable('data');
                        if (is_array($childData)) {
                            $data = array_merge($data, $childData);
                        }
                    }
                } else {
                    $data = $e->getViewModel()->getVariable('data');
                }


                $postData = json_encode($data);
                //$postData = $e->getViewModel()->getVariables();

                if (!$isLogin) {
                    $username = "NONE";
                    if (isset($data['failed_username'])) {
                        $username = $data['failed_username'];
                    }
                } else {
                    $username = $auth->getIdentity()->username;
                }
                $env = getenv('APPLICATION_ENV') ? : 'production';
                $config = include __DIR__ . '/../../config/autoload/' . $env . '.php';
                $logsClass = new Logs($config);
                $logsClass->logrequest($username, $ipaddress, $url, $postData, MvcEvent::EVENT_FINISH, $controller, $action, $sourceURL);
            }
        });

        //echo "here"; die();
        $this->logEntry($e);
        $env = getenv('APPLICATION_ENV') ? : 'production';
        $config = include __DIR__ . '/../../config/autoload/' . $env . '.php';
        $aclClass = new Acl();
        $e->getViewModel()->acl = $aclClass->initAcl($config['db']);
        $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function checkAcl(MvcEvent $e) {
        $route = $e->getRouteMatch()->getMatchedRouteName();

        $controllerFull = $e->getRouteMatch()->getParam('controller');
        $contArray = explode("\\", $controllerFull);
        $controller = strtolower($contArray[count($contArray) - 1]);
        $action = $e->getRouteMatch()->getParam('action');

        //change route to controller-action
        $route = $controller . "-" . $action;
        //echo $route; die();
        //you set your role
        //$userRole = 'service-desk';
        //$userRole = 'security-administrator';

        $auth = new AuthenticationService();
        $isLogin = $auth->hasIdentity();
        if (!$isLogin) {
            $userRole = 'guest';
        } else {
            $sessionManager = new \Zend\Session\SessionManager();
            $sessionStorage = $sessionManager->getStorage();
            $userRole = $sessionStorage->getMetaData('role');

            if (strlen(trim($userRole)) == 0 || !in_array($userRole, $e->getViewModel()->acl->getRoles())) {
                $userRole = 'guest';
            }
        }

        $overrideACL = FALSE;

        $overrideList = array(
            'user-checkpassword', 'user-checkusername', 'customerror-not-allowed', 'pwdchange-index', 'login-logout', 'timeclock-index', 'timeclock-logtime'
        );
        if (in_array($route, $overrideList)) {
            $overrideACL = TRUE;
        }

        if (!$overrideACL) {
            //echo $route . "--" . $userRole;

            if (!$e->getViewModel()->acl->hasResource($route) || !$e->getViewModel()->acl->isAllowed($userRole, $route)) {
                $response = $e->getResponse();
                //location to page or what ever
                $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/not-allowed');
                $response->setStatusCode(303);

                // To avoid additional processing
                // we can attach a listener for Event Route with a high priority
                $stopCallBack = function($event) use ($response) {
                    $event->stopPropagation();
                    return $response;
                };
                //Attach the "break" as a listener with a high priority
                $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack, -10000);
                return $response;
            }
        } else {
            
        }
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'AuthService' => function($sm) {

            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'playdough_login', 'username', 'password', 'MD5(?)');

            $authService = new AuthenticationService();
            $authService->setAdapter($dbTableAuthAdapter);
            $authService->setStorage(new Session());

            return $authService;
        },
                'Options' => function($sm) {

            $env = getenv('APPLICATION_ENV') ? : 'production';
            $config = include __DIR__ . '/../../config/autoload/' . $env . '.php';
            $adapter = new Adapter($config['db']);
            $optionsTable = new OptionsTable($adapter);

            $result = $optionsTable->fetchWhere();
            $optionsList = array();
            foreach ($result as $r) {
                $optionsList[$r->option_key] = $r->option_value;
            }
            return $optionsList;
        },
                'acl' => function ($sm) {
            $env = getenv('APPLICATION_ENV') ? : 'production';
            $config = include __DIR__ . '/../../config/autoload/' . $env . '.php';
            $aclClass = new Acl();
            return $aclClass->initAcl($config['db']);
        }
            ),
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'Login_widget' => function ($helperPluginManager) {
            $viewHelper = new View\Helper\Authhelper();
            return $viewHelper;
        },
                'Nav_widget' => function ($helperPluginManager) {
            $viewHelper = new View\Helper\Navhelper();
            $viewHelper->setServiceLocator($helperPluginManager->getServiceLocator());
            return $viewHelper;
        },
                'Breadcrumb_widget' => function($helperPluginManager) {
            $viewHelper = new View\Helper\Breadcrumbhelper();
            return $viewHelper;
        },
                'loggedUser' => function($helperPluginManager) {
            $viewHelper = new View\Helper\LoggedUserhelper();
            $viewHelper->setServiceLocator($helperPluginManager->getServiceLocator());
            return $viewHelper;
        },
                'Conf_widget' => function($helperPluginManager) {
            $viewHelper = new View\Helper\Confhelper();
            $viewHelper->setServiceLocator($helperPluginManager->getServiceLocator());
            return $viewHelper;
        },
            )
        );
    }

    public function logEntry($e) {
        $auth = new AuthenticationService();
        $isLogin = $auth->hasIdentity();

        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $url = $_SERVER['REQUEST_URI'];
        if (isset($_SERVER['HTTP_REFERER'])) {
            $ref = $_SERVER['HTTP_REFERER'];
            $sourceURL = str_replace("http://" . $_SERVER['HTTP_HOST'], "", $ref);
            $sourceURL = str_replace("https://" . $_SERVER['HTTP_HOST'], "", $sourceURL);
        } else {
            $sourceURL = NULL;
        }
        $request = new Request();
        $freshPostData = $request->getPost()->toArray();
        
        //unset any password post data
        unset($freshPostData['password']);
        unset($freshPostData['password_confirm']);
        
        //remove from URL any password data
        $sourceURL_array = explode("&", $sourceURL);
        $sourceURL_temp = "";
        foreach ($sourceURL_array as $val) {
            if (strpos($val, "password") !== FALSE) {
                $sourceURL_temp .= $val;
            }
        }
        
        $sourceURL = $sourceURL_temp;
        
        $postData = json_encode($freshPostData);
        //var_dump($request);
        //var_dump($postData); die();
        //$route = $e->getRouteMatch()->getMatchedRouteName();
        //$controllerFull = $e->getRouteMatch()->getParam('controller');
        //$contArray = explode("\\", $controllerFull);
        //$controller = strtolower($contArray[count($contArray) - 1]);
        //$action = $e->getRouteMatch()->getParam('action');
        //var_dump($request); die();
        $urlArray = explode("/", $url);
        if (isset($urlArray[1])) {
            $controller = $urlArray[1];
        } else {
            $controller = "index";
        }
        if (isset($urlArray[2])) {
            $actionArray = explode("?", $urlArray[2]);
            $action = $actionArray[0];
        } else {
            $action = "index";
        }


        if (!$isLogin) {
            $username = "NONE";
        } else {
            $username = $auth->getIdentity()->username;
        }
        
        
        //special case for pwdchange
        
        $logType = NULL;
        if ($controller == "pwdchange" && $action == "index") {
            $logType = 'password reset';
        }
        
        $env = getenv('APPLICATION_ENV') ? : 'production';
        $config = include __DIR__ . '/../../config/autoload/' . $env . '.php';
        $logsClass = new Logs($config);
        $logsClass->logrequest($username, $ipaddress, $url, $postData, MvcEvent::EVENT_DISPATCH, $controller, $action, $sourceURL,NULL, $logType);
    }

}
