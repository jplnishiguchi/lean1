<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\UserObject;
use Database\Model\UserTable;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\Storage\Session as SessionStorage;
use Application\Form\AuthForm;
use Database\Model\UserAuth;
use Zend\Authentication\Result;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\Parameters;

use Utilities\Employee;
use Database\Model\EmployeeObject;
use Database\Model\EmployeeTable;

class LoginController extends AbstractActionController {

    public function indexAction() {

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        $config = $this->getServiceLocator()->get('Config');
        $adapter = new Adapter($config['db']);

        $user = new UserObject();
        $userGateway = new TableGateway($user->getTablename(), $adapter);
        $userTable = new UserTable($userGateway);

        $auth = $this->getServiceLocator()->get('AuthService');

        $optionsConfig = $this->getServiceLocator()->get('Options');
        $config = array_merge($config, $optionsConfig);

        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $sessionManager = new \Zend\Session\SessionManager();
        $sessionStorage = $sessionManager->getStorage();
        $attempts = 0;


        if ($sessionStorage->getMetaData('login_attempts') == FALSE) {
            $sessionStorage->setMetaData('login_attempts', $attempts);
        }

        $userIdentity = $this->identity();
        $form = new AuthForm();
        $form->get('submit')->setValue('Login');
        $messages = null;


        $request = $this->getRequest();
        $maxLoginAttempts = $config['max_login_attempts'];
        $username = '';
        if ($request->isPost()) {
            $authFormFilters = new UserAuth();
            $form->setInputFilter($authFormFilters->getInputFilter());
            $form->setData($request->getPost());
            
            $posts = $request->getPost()->toArray();

            if ($form->isValid()) {
                $data = $form->getData();
                $username = $data['username'];
                $sm = $this->getServiceLocator();

                // get static salt from configuration
                $staticSalt = $config['static_salt'];



                $row = $userTable->getUserByFields(array('username' => $data['username']));

                //check last activity time.
                // if is still logged but 1 day no activity, then log it out.
                if (isset($row->logged) && $row->logged == 1) {
                    $LogHimOut = FALSE;
                    if ($row->last_activity_time == NULL) {
                        $LogHimOut = TRUE;
                    } else {
                        $date = new \DateTime();
                        $date->setTimestamp(strtotime($row->last_activity_time));
                        $date->add(new \DateInterval('PT86400S'));
                        $login_withoutact_exp_date = $date->format('Y-m-d H:i:s');
                        $dateNow = new \DateTime();
                        if ($dateNow->format('Y-m-d H:i:s') > $login_withoutact_exp_date) {
                            $LogHimOut = TRUE;
                        }
                    }
                    if ($LogHimOut) {
                        $userTable->update(array('logged' => 0, 'login_exp_date' => date("Y-m-d H:i:s")), array('username' => $data['username']));
                        $row = $userTable->getUserByFields(array('username' => $data['username']));
                    }
                }


                if (isset($row->logged) && $row->logged == 1 && $config['allow_concurrent_sessions'] == "FALSE") {
                    if (isset($row->login_exp_date) && $row->login_exp_date > date("Y-m-d H:i:s")) {

                        $viewModel->setVariables(array(
                            'data' => array('form' => $form,
                                'error_messages' => array(
                                    'Concurrent Login Not Allowed.'),
                                'failed_username' => $data['username'])
                                )
                        );
                        return $viewModel;
                    }
                }

                $password = $userTable->hashPasword($staticSalt, $data['password']);
                $row = $userTable->getUserByFields(array('username' => $data['username'], 'password' => $password));


                if ($userTable->isLocked($data['username'])) {
                    if (isset($row->locked_date)) {
                        $date = new \DateTime();
                        $date->setTimestamp(strtotime($row->locked_date));
                        $date->add(new \DateInterval('PT' . $config['lockout_seconds'] . 'S'));
                        $lockout_exp_date = $date->format('Y-m-d H:i:s');

                        if ($lockout_exp_date < date("Y-m-d H:i:s")) {
                            //cont..
                        } else {
                            $viewModel->setVariables(array(
                                'data' => array('form' => $form,
                                    'error_messages' => array(
                                        'Account has been locked. Please contact your administrator to unlock.'),
                                    'failed_username' => $data['username'])
                                    )
                            );
                            return $viewModel;
                        }
                    } else {
                        $userTable->update(array('locked' => 1, 'locked_date' => date("Y-m-d H:i:s")), array('username' => $data['username']));

                        $viewModel->setVariables(array(
                            'data' => array('form' => $form,
                                'error_messages' => array(
                                    'Account has been locked. Please contact your administrator to unlock.'),
                                'failed_username' => $data['username'])
                                )
                        );

                        return $viewModel;
                    }
                }

                if (isset($row)) {
                    if (isset($row->pwd_exp_date) && $row->pwd_exp_date < date("Y-m-d H:i:s")) {
                        $viewModel->setVariables(array(
                            'data' => array(
                                'form' => $form,
                                'error_messages' => array(
                                    'Your password have already expired. Please contact your administrator to reset your password.', 'Login Failed.'),
                                'failed_username' => $data['username']
                            )
                                )
                        );
                        return $viewModel;
                    }

                    if (isset($row->status) && $row->status == UserObject::STATUS_INACTIVE) {
                        $viewModel->setVariables(array(
                            'data' => array(
                                'form' => $form,
                                'error_messages' => array(
                                    'Login Failed.'),
                                'failed_username' => $data['username']
                            )
                                )
                        );
                        return $viewModel;
                    }
                }

                $authAdapter = new AuthAdapter($adapter, $user->getTablename(), //method setTableName  for dbAdapter
                        'username', // a method for setIdentityColumn
                        'password', //  a method for setCredentialColumn
                        "MD5(CONCAT('$staticSalt', ?))" // setCredentialTreatment(parametrized string) 'MD5(?)'
                );
                $authAdapter
                        ->setIdentity($data['username'])
                        ->setCredential($data['password'])
                ;

                $result = $auth->authenticate($authAdapter);

                switch ($result->getCode()) {
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
// do stuff for nonexistent identity

                        $message[] = 'Non-existent Credential';
                        $message[] = 'Login Failed';
                        break;
                    default:
                    case Result::FAILURE_CREDENTIAL_INVALID:

                        if (!$userTable->isAdmin($username)) {
                            $attempts = $sessionStorage->getMetaData('login_attempts') + 1;
                            $message[] = ($maxLoginAttempts - $attempts) . " attempt/s left before lockout<br/>";
                        }
                        $sessionStorage->setMetaData('login_attempts', $attempts);

                        // do stuff for invalid credential
                        //$message[] = 'Invalid Credential';
                        $message[] = 'Login Failed';


                        break;

                    case Result::SUCCESS:
                        $storage = $auth->getStorage();

                        $storage->write($authAdapter->getResultRowObject(null, 'password'));

                        $loggedUser = $userTable->getUserObject($this->identity()->username);

                        $sessionStorage->setMetaData('login_attempts', 0);
                        $storage->read()->{'loggedUser'} = $loggedUser;

                        $sessionManager = new \Zend\Session\SessionManager();
                        $sessionStorage->setMetaData('role', $loggedUser->role);

                        $employee = new \Utilities\Employee($this->getServiceLocator()->get('Config'));
                        $employeeObj = $employee->table->fetchWhere([['company_email' => $this->identity()->email]]);
                        $employeeDetails = $employeeObj->current();
                        $sessionStorage->setMetaData('employee_id', $employeeDetails->id);
                        $sessionStorage->setMetaData('employee_email', $employeeDetails->company_email);

                        if ($data['rememberme']) {

                            $date = new \DateTime();
                            $date->add(new \DateInterval('PT' . $config['rememberme_timeout_seconds'] . 'S'));
                            $login_exp_date = $date->format('Y-m-d H:i:s');

                            $sessionManager->rememberMe($config['rememberme_timeout_seconds']);
                        } else {

                            $date = new \DateTime();
                            $date->add(new \DateInterval('PT' . $config['inactivity_timeout_seconds'] . 'S'));
                            $login_exp_date = $date->format('Y-m-d H:i:s');


                            $sessionManager->rememberMe($config['inactivity_timeout_seconds']);
                            $sessionStorage->setMetaData('inactivity_timeout_seconds', $config['inactivity_timeout_seconds']);
                        }

                        $userTable->update(array('logged' => 1, 'login_exp_date' => $login_exp_date), array('username' => $username));

                        if ($userTable->isLocked($data['username'])) {
                            $userTable->update(array('locked' => 0, 'locked_date' => NULL), array('username' => $username));
                        }

                        $sessionStorage->setMetaData('is_new_user', $row->is_new_user);

                        if(!empty($posts['mobile'])){
                            $result = array(
                                "success" => true,
                            );
                            echo json_encode($result);
                            die;
                        }else{
                            return $this->redirect()->toRoute('home');
                            break;
                       }                        
                }

                foreach ($message as $msg) {
                    // foreach ($result->getMessages() as $message) {
                    $messages[] = $msg;
                }
            }
        }



        if ($sessionStorage->getMetaData('login_attempts') >= $maxLoginAttempts) {
            $sessionStorage->setMetaData('login_attempts', 0);

            $userTable->update(array('locked' => 1, 'locked_date' => date("Y-m-d H:i:s")), array('username' => $username));
            $messages[] = "<br>account locked for $username";
        }

        $ispwdchange = $request->getQuery('pwdchangemsg');
        
        if(!empty($posts['mobile'])){
            $result = array(
                "success" => false,
                "msg" => $messages,
            );
            echo json_encode($result);
            die;
        }

        if (!is_null($ispwdchange)) {
            $messages[] = $ispwdchange;
            $viewModel->setVariables(array(
                'data' => array('form' => $form, 'error_messages' => $messages, 'ispwdchange' => 1, 'username' => $request->getQuery('username'))
                    )
            );
        } else {
            $failedUsername = NULL;
            if (isset($data['username'])) {
                $failedUsername = $data['username'];
            }

            $viewModel->setVariables(array(
                'data' => array('form' => $form, 'error_messages' => $messages,
                    'failed_username' => $failedUsername)
                    )
            );
        }

        return $viewModel;
    }

    public function logoutAction() {
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
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

            return $this->redirect()->toRoute('login', array('controller' => 'login', 'action' => 'index'));
        } else {
            return $this->redirect()->toRoute('login', array('controller' => 'login', 'action' => 'index'));
        }
    }

    public function testAction() {
        
    }

}
