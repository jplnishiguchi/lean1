<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Utilities\Playdough\User;
use Utilities\Playdough\Pwd;
use Utilities\Roles;
use Utilities\Playdough\PdValidator;
use Utilities\CbValidator;
use Utilities\Options;
use Utilities\Empgroup;
use Utilities\EmployeeJap;
use Utilities\Playdough\Loggedusers;
use Database\Model\LogsObject;

class UserController extends AbstractActionController {

    protected $_userClass = NULL;

    public function indexAction() {

    }

    public function searchAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);

        $config = $this->getServiceLocator()->get('Config');
        $this->_userClass = new User($config);

        /**
         * Set paging parameters
         */
        $params = $this->params()->fromQuery();

        $pdValidator = new PdValidator();
        $result = $pdValidator->validate($params);
        if (is_array($result)) {
            echo json_encode($result);
            die;
        }

        $page = isset($params['pg']) ? $params['pg'] : 1;
        $orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "username";
        $sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";
        $whereArray = NULL;
        $columns = array(
            "username",
		   "email",
            "pwd_exp_date",
            'status',
            "role",
        );
//        $orderBy = "username";
//        $sort = "ASC";
        $limit = $config['users']['limit_per_page'];
        $offset = ($page - 1) * $limit;

        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_userClass->getUsersList($search, $columns, $orderBy, $sort, $limit, $offset, $page);

        echo json_encode($resultList);

        die;
    }

    public function checkusernameAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);

        $params = $this->params()->fromQuery();

        $config = $this->getServiceLocator()->get('Config');
        $staticSalt = $config['static_salt'];
        $this->_userClass = new User($config);
        $result = $this->_userClass->table->fetchWhere(array(array('username' => $params['username'])));

        echo count($result);

        die();
    }

	public function checkemailAction(){
		$request = new Request();
		$posts = $request->getPost()->toArray();
		$config = $this->getServiceLocator()->get('Config');
		$this->_userClass = new User($config);

		$result = $this->_userClass->table->fetchWhere(array(array('email' => $posts['email'])));

		echo count($result);

		die();

	}

    public function addAction() {
                error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $request = new Request();
        $posts = $request->getPost()->toArray();

        $config = $this->getServiceLocator()->get('Config');

        if ($posts == null) {
            $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config['roles']);
            $rolesList = $rolesClass->getAllRoles();
                        
            $egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $egGroups = $egClass->getGroups();
            $egRoles = $egClass->getRoles();
            
            $data = array(
                'roles' => $rolesList,
                'eggroups' => $egGroups,
                'egroles' => $egRoles,
            );
                        
            return($data);
        } else {
                        
            $optionsClass = new Options($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $passwordLength = $optionsClass->getByOptionKey("password_length");
            $options['password_length'] = $passwordLength['option_value'];

//            $cbValidator = new CbValidator('user-add', $options);
//            $result = $cbValidator->validate($posts);
//            if (is_array($result)) {
//                $view = new ViewModel(
//                        array(
//                    'data' => array('msg' => $result['msg'])
//                        )
//                );
//                $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
//                return $view;
//            }
            
            
            

            $staticSalt = $config['static_salt'];
            $this->_userClass = new User($config);
            $hashPassword = $this->_userClass->table->hashPasword($staticSalt, $posts['password']);
            $userList = $this->_userClass->table->insert(array(
				'username' => $posts['username'],
				'email' => $posts['email'],
				'password' => $hashPassword,
				'pwd_exp_date' => $posts['pwd_exp_date'],
				'role' => $posts['role'],
				'status' => $posts['status']
            ));

            $pwdHistory = new Pwd($config);
            $pwdHistory->table->insert(array(
                'username' => $posts['username'],
                'password' => $hashPassword
            ));
            
//            $empClass = new EmployeeJap($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
//            $employee = $posts['emp'];
//            
//            $employee['company_email'] = $posts['email'];
//            $employee['date_hired'] = date('Y-m-d',strtotime($posts['emp']['date_hired']));
//                        
//            $empClass->createEmployee($employee);

            $view = new ViewModel(
                    array(
                'data' => array(
                    'username' => $posts['username'],
                    'msg' => 'User Account for <b>' . $posts['username'] . '</b> was successfully created.',
                     'log_type' => LogsObject::LOG_TYPE_USER_ADD
                    ))
            );

            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }

    public function bulkaddAction() {
        $request = $this->getRequest();
        $saveMessage = array();
        if ($request->isPost()) {
            // get file
            $file = $request->getFiles()->toArray();

            $config = $this->getServiceLocator()->get('Config');
            $userclass = new User($config);
            $return = $userclass->bulkadd($file);

            if ($return['status'] == FALSE) {
                return array('data' => $return['data']);
            } else {
                $staticSalt = $config['static_salt'];
                $this->_userClass = new User($config);
                $pwdHistory = new Pwd($config);

                $saveMessages = array();
                foreach ($return['data'] as $data) {
                    if (isset($data['username']) && isset($data['password']) && isset($data['role']) && isset($data['pwd_exp_date'])) {
                        try {
                            $hashPassword = $this->_userClass->table->hashPasword($staticSalt, $data['password']);
                            $userList = $this->_userClass->table->insert(array(
                                'username' => $data['username'],
                                'password' => $hashPassword,
                                'pwd_exp_date' => $data['pwd_exp_date'],
                                'role' => $data['role'],
                            ));


                            $pwdHistory->table->insert(array(
                                'username' => $data['username'],
                                'password' => $hashPassword
                            ));
                            $saveMessages[] = "Account for " . $data['username'] . " successfully created.";
//                            $view = new ViewModel(array(
//                                'data' => array(
//                                'msg' => "Account for " . $data['username'] . " successfully created.",
//                                'log_type' => LogsObject::LOG_TYPE_USER_ADD
//                                )
//                            ));
//                            $saveMessages[] = $view;
                        } catch (\Exception $e) {
                            $saveMessages[] = "Data error for " . $data['username'] . " (" . implode(",", $data) . "). " . $e->getMessage();
                        }
                    } else {
                        $saveMessages[] = "Data incomplete for " . $data['username'] . " (" . implode(",", $data) . ").";
                    }
                }

                return array('data' => array('saveMessages' => $saveMessages, 'log_type' => LogsObject::LOG_TYPE_USER_ADD));
            }
        }
    }

    public function updateAction() {
        $request = new Request();
        $config = $this->getServiceLocator()->get('Config');
        $this->_userClass = new User($config);

        $posts = $request->getPost()->toArray();

        $pdValidator = new PdValidator();
        $result = $pdValidator->validate($posts);
        if (is_array($result)) {
            $view = new ViewModel(
                    array(
                'data' => array('msg' => $result['msg'])
                    )
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }

        $username = $request->getQuery('username');

        $postParam = $request->getPost('username');
        $getParam = $request->getQuery('username');
        if (empty($postParam) && empty($getParam)) {
            $this->redirect()->toRoute('user');
        }

        if ($posts == null) {
            $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config['roles']);
            $rolesList = $rolesClass->getAllRoles();
            $userData = $this->_userClass->table->getUserObject($username);
            return (array(
                'user' => $userData,
                'roles' => $rolesList
            ));
        } else {
            $result = $this->_userClass->updateUser($posts);

            $view = new ViewModel(
                    array(
                'data' => array(
                    'username' => $posts['username'],
                    'msg' => 'User Account <b>' . $posts['username'] . '</b> was successfully updated.',
                    'log_type' => LogsObject::LOG_TYPE_USER_UPDATE)
                    )
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }


        return $result;
    }

    public function pwdresetAction() {
        ini_set('display_errors', 1);
        $request = new Request();
        $config = $this->getServiceLocator()->get('Config');
        $this->_userClass = new User($config);

        $posts = $request->getPost()->toArray();

        $username = $request->getQuery('username');

        if ($posts == null) {
            $userData = $this->_userClass->table->getUserObject($username);
            if ($this->_userClass->isLoggedIn($username)) {
                $view = new ViewModel(
                        array(
                    'data' => array('username' => $username, 'msg' => 'User Account <b>' . $username . '</b> is currently logged in. You can not reset his password.')
                        )
                );
                $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
                return $view;
            }
            return (array(
                'user' => $userData
            ));
        } else {
            $optionsClass = new Options($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $passwordLength = $optionsClass->getByOptionKey("password_length");
            $options['password_length'] = $passwordLength['option_value'];

            $pdValidator = new PdValidator('pwd-reset', $options);
            $result = $pdValidator->validate($posts);
            if (is_array($result)) {
                $view = new ViewModel(
                        array(
                    'data' => array('msg' => $result['msg'])
                        )
                );
                $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
                return $view;
            }

            if ($this->_userClass->isLoggedIn($posts['username'])) {
                $view = new ViewModel(
                        array(
                    'data' => array('username' => $username, 'msg' => 'User Account <b>' . $posts['username'] . '</b> is currently logged in. You can not reset his password.')
                        )
                );
                $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
                return $view;
            }

            $result = $this->_userClass->resetPass($posts);

            $view = new ViewModel(
                    array('data' => array(
                        'username' => $posts['username'],
                        'msg' => 'User Account <b>' . $username . '</b> was successfully updated.',
                        'log_type' => LogsObject::LOG_TYPE_USER_PWDRESET
                        ))
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }


        return $result;
    }

    public function checkpasswordAction() {
//
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);
        ini_set('display_errors', 0);

        $params = $this->params()->fromQuery();

//        $pdValidator = new PdValidator();
//        $result = $pdValidator->validate($params);
//        if(is_array($result)){
//            echo json_encode($result);
//            die;
//        }

        $config = $this->getServiceLocator()->get('Config');
        $optionsConfig = $this->getServiceLocator()->get('Options');
        $config = array_merge($config, $optionsConfig);

        $staticSalt = $config['static_salt'];
        $this->_userClass = new User($config);

        $hashPwd = $this->_userClass->getHashPassword($params['password']);


        $pwdHistory = new Pwd($config);
        echo $pwdHistory->allowChange($hashPwd, $params['username'], $config['pwd_history']);

        //echo $hashPwd;


        die();
    }

    public function deleteAction() {
        $config = $this->getServiceLocator()->get('Config');

        $request = new Request();
        $username = $request->getQuery('username');

        if (is_null($username) || strlen(trim($username))==0) {
            $view = new ViewModel(
                    array('data' => array('msg' => 'Unknown username. Deletion Failed.'))
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
        } else {
            $userClass = new User($config);

            $result = $userClass->deleteUser($username);

            $view = new ViewModel(
                    array('data' => array('msg' => 'User <b>' . $username . '</b> was successfully deleted.',
                        'log_type' => LogsObject::LOG_TYPE_USER_DELETE))
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
        }

        return $view;
    }

    public function bulkdeleteAction() {
        $config = $this->getServiceLocator()->get('Config');

        $request = new Request();

        $usernames = $request->getPost('usernames');

        $posts = $request->getPost()->toArray();

        $pdValidator = new PdValidator("user-bulkdelete");
        $result = $pdValidator->validate($posts);

        if (is_array($result)) {
            $view = new ViewModel(
                    array('data' => array('msg' => $result['msg']))
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }

        if (strlen(trim($usernames)) > 0) {
            $userArray = explode(",", $usernames);
        } else {
            $userArray = array();
        }
        $userClass = new User($config);

        if (count($userArray) > 0) {
            foreach ($userArray as $username) {
                if (strlen(trim($username)) > 0) {
                    $result = $userClass->deleteUser(trim($username));
                }
            }

            $view = new ViewModel(
                    array('data' => array(
                        'msg' => 'User(s) <b>' . $usernames . '</b> was successfully deleted.',
                        'log_type' => LogsObject::LOG_TYPE_USER_DELETE
                        ))
            );
        } else {
            $view = new ViewModel(
                    array('data' => array('msg' => 'No selected username(s).')));
        }

        $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
        return $view;
    }

    public function loggedusersAction() {

    }

    public function loggedsearchAction() {
        $config = $this->getServiceLocator()->get('Config');
        $this->_loggedClass = new User($config);

        /**
         * Set paging parameters
         */
        $params = $this->params()->fromQuery();

        $pdValidator = new PdValidator();
        $result = $pdValidator->validate($params);
        if (is_array($result)) {
            echo json_encode($result);
            die;
        }

        $page = isset($params['pg']) ? $params['pg'] : 1;
        $orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "username";
        $sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";
        $whereArray = NULL;
        $columns = array(
            "username",
            "pwd_exp_date",
            'status',
            "role",
            "last_activity_time",
        );
//        var_dump($last_activity_time);
//        die;

//        $orderBy = "username";
//        $sort = "ASC";
        $limit = $config['users']['limit_per_page'];
        $offset = ($page - 1) * $limit;

        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_loggedClass->getLoggedusersList($search, $columns, $orderBy, $sort, $limit, $offset, $page);

        echo json_encode($resultList);

        die;
    }
    
    public function myaccountAction() {
        $request = new Request();
        $config = $this->getServiceLocator()->get('Config');
        $this->_userClass = new User($config);

        $posts = $request->getPost()->toArray();

        $pdValidator = new PdValidator();
        $result = $pdValidator->validate($posts);
        if (is_array($result)) {
            $view = new ViewModel(
                    array(
                'data' => array('msg' => $result['msg'])
                    )
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
        
        $auth = $this->getServiceLocator()->get('AuthService');
        $username = $auth->getIdentity()->username;

        if ($posts == null) {
            $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config['roles']);
            $rolesList = $rolesClass->getAllRoles();
            $userData = $this->_userClass->table->getUserObject($username);
            return (array(
                'user' => $userData,
                'roles' => $rolesList
            ));
        } else {
            $result = $this->_userClass->updateUser($posts);

            $view = new ViewModel(
                    array(
                'data' => array(
                    'username' => $posts['username'],
                    'msg' => 'User Account <b>' . $posts['username'] . '</b> was successfully updated.',
                    'log_type' => LogsObject::LOG_TYPE_USER_UPDATE)
                    )
            );
            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }


        return $result;
    }

}
