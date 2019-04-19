<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Utilities\Playdough\User;
use Zend\Authentication\AuthenticationService;


use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\UserObject;
use Database\Model\UserTable;

class PwdresetController extends AbstractActionController {

    public function indexAction() {
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
        }

        $username = $identity->username;



        $request = new Request();
        $config = $this->getServiceLocator()->get('Config');
        $this->_userClass = new User($config);

        $posts = $request->getPost()->toArray();

        if ($posts == null) {
            return array(
                'data' => array('username' => $username)
            );
        } else {
            $result = $this->_userClass->resetPass($posts, FALSE );

             $auth = new AuthenticationService();

            if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
            }
            
            $config = $this->getServiceLocator()->get('Config');
            $adapter = new Adapter($config['db']);
            $user = new UserObject();
            $userGateway = new TableGateway($user->getTablename(), $adapter);
            $userTable = new UserTable($userGateway);
            $userTable->update(array('logged' => 0, 'login_exp_date' => NULL), array('username' => $identity->username));

           
            $auth->clearIdentity();
            $sessionManager = new \Zend\Session\SessionManager();
            $sessionManager->forgetMe();
            $this->getServiceLocator()->get('AuthService')->getStorage()->clear();

            return $this->redirect()->toUrl('login?username=' . $username . '&pwdchangemsg=Password has been reset. Please login again with your new password.');
        }
    }

}
