<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;
use Utilities\Employee;
use Utilities\CbValidator;
use Utilities\Playdough\User;
use Utilities\Playdough\Time;
use Database\Model\LogsObject;
use Utilities\Pages;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;

class TimeclockController extends AbstractActionController {

    protected $_timeClass = NULL;

    public function indexAction() {

    }

    /**
    * @todo: validation
    **/
    public function logtimeAction() {
        $param = $this->params()->fromRoute('id');  

        $request = new Request();
        $posts = $request->getPost()->toArray();

        $config = $this->getServiceLocator()->get('Config');

        if ($posts == null) {
            $view = new ViewModel(
                array(
                'data' => array(
                    'msg' => 'Email and password mismatch.',
                    'log_type' => LogsObject::LOG_TYPE_TIMECLOCK_MISMATCH
                ))
            );

            $view->setTemplate('application/timeclock/notice.phtml'); // path to phtml file under view folder
            return $view;
        } else {

            /*
                $optionsClass = new Options($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
                $passwordLength = $optionsClass->getByOptionKey("password_length");
                $options['password_length'] = $passwordLength['option_value'];

                $cbValidator = new CbValidator('user-add', $options);
                $result = $cbValidator->validate($posts);
                if (is_array($result)) {
                    $view = new ViewModel(
                            array(
                        'data' => array('msg' => $result['msg'])
                            )
                    );
                    $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
                    return $view;
                }
            */

            $staticSalt = $config['static_salt'];
            $this->_userClass = new User($config);
            $hashPassword = $this->_userClass->table->hashPasword($staticSalt, $posts['password']);

            /** Get login details from `playdough_login` table **/            
            $where = new Where();
            $where->equalTo('email',$posts['email'])
                ->equalTo('password',$hashPassword);
            $userObject = $this->_userClass->table->fetchAll(array(
                'where' => $where
            ));
            $userDetails = $userObject->current();

            if(!$userDetails){
                $view = new ViewModel(
                        array(
                    'data' => array(
                        'msg' => 'Email and password mismatch.',
                         'log_type' => LogsObject::LOG_TYPE_TIMECLOCK_MISMATCH
                        ))
                );

                $view->setTemplate('application/timeclock/notice.phtml'); // path to phtml file under view folder
                return $view;
            }

            /** Get employee details from `employee` table using
                `playdough_login`.`email` 
            **/    
            $this->_employeeClass =  new Employee($config);
            $where = new Where();
            $where->equalTo('company_email',$userDetails['email']);
            $employeeObject = $this->_employeeClass->table->fetchAll(array(
                'where' => $where
            ));
            $employeeDetails = $employeeObject->current();

            /** Get clock in details from `time_logs` table using
                `playdough_login`.`id` and date from HTTP POST request
            **/
            $this->time = new Time($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $where = new Where();
            $where->equalTo('employee_id',$employeeDetails['id']);
            $where->equalTo('date',$posts['date']);
            $timeDetails = $this->time->selectTime(array(
                'where' => $where,
                'limit' => 1,
                'order' => 'id DESC'     
            ));

            $inOrOut = '';
            if(!$timeDetails){
                /** (Clock in) Insert time **/
                $this->time->insertTime(array(
                        "employee_id" => $employeeDetails['id'],
                        "medium" => "web",
                        "date" => $posts['date'],
                        "clock_in" => $posts['time'],
                    )
                );
                $inOrOut = 'in';

            }
            /** If already have clock in and clock out, create new entry. **/
            elseif($timeDetails['clock_out'] != NULL){
                /** (Clock in) Insert time **/
                $this->time->insertTime(array(
                        "employee_id" => $employeeDetails['id'],
                        "medium" => "web",
                        "date" => $posts['date'],
                        "clock_in" => $posts['time'],
                    )
                );
                $inOrOut = 'in';       
            }else{
                /** (Clock out) Update clock_out based on clock_in details **/
                $this->time->updateTime(array(
                        'clock_in' => $timeDetails['clock_in'],
                        'clock_out' => $posts['time'],
                        'id' => $timeDetails['id']
                    )
                );
                $inOrOut = 'out';
            }
            /*
                echo "timeDetails -> ".($timeDetails ? "Yes" : "No");
                echo "posts -> ";
                var_dump($posts);
                echo "userDetails -> ";
                var_dump($userDetails);
                echo "employeeDetails -> ";
                var_dump($employeeDetails);
                die();
            */
            // has successfully logged in at YYYY-MM-DD HH:MM:SS" 
            
            $view = new ViewModel(
                    array(
                'data' => array(
                    'msg' => 'Employee ID <b>' . $employeeDetails['employee_number'] . '</b> ('. 
                        $employeeDetails['first_name'] . ' ' . $employeeDetails['last_name'] . 
                        ' ) was successfully logged ' . $inOrOut . ' at '.
                        $posts['date'] . ' ' . $posts['time'] . '.',
                     'log_type' => LogsObject::LOG_TYPE_TIMECLOCK_LOGTIME
                    ))
            );

            $view->setTemplate('application/timeclock/notice.phtml'); // path to phtml file under view folder
            return $view;
            
        }
    }

   
}
