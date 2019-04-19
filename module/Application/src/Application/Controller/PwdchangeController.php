<?php

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

class PwdchangeController extends AbstractActionController {

    public function indexAction() {
        $request = new Request();
        $config = $this->getServiceLocator()->get('Config');
        $this->_userClass = new User($config);

        $posts = $request->getPost()->toArray();
        $identity = $this->identity();
        $username = $identity->username;
        $pwd_exp_date = $identity->pwd_exp_date;

        if ($posts == null) {
            return array(
                'data' => array('username' => $username)
                    );
        } else {
            
            $pass = $posts['password'];
            
            $result = $this->_userClass->renewPass($username, $pass, $pwd_exp_date);

            $config = $this->getServiceLocator()->get('Config');
            $adapter = new Adapter($config['db']);
            $user = new UserObject();
            $userGateway = new TableGateway($user->getTablename(), $adapter);
            $userTable = new UserTable($userGateway);
            $userTable->update(array('logged' => 0, 'login_exp_date' => NULL), array('username' => $identity->username));
            
            $auth = new AuthenticationService();

            if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
            }
            $auth->clearIdentity();
            $sessionManager = new \Zend\Session\SessionManager();
            $sessionManager->forgetMe();
            $this->getServiceLocator()->get('AuthService')->getStorage()->clear();

            return $this->redirect()->toUrl('login?username='.$username.'&pwdchangemsg=Password has been reset. Please login again with your new password.');

            //return $this->redirect()->toRoute('log', array('newId' => $schemeId));
            //return $this->redirect()->toRoute('schemes', array('newId'=>$schemeId));
        }
    }

}
