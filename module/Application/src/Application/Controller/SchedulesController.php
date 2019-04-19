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
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;
use Utilities\Employee;
use Utilities\Schedules;
use Utilities\CbValidator;
use Utilities\Playdough\User;
use Database\Model\LogsObject;
use Utilities\Pages;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class SchedulesController extends AbstractActionController {

    public function viewAction() {
        $param = $this->params()->fromRoute('id');

        $sessionManager = new \Zend\Session\SessionManager();
        $sessionStorage = $sessionManager->getStorage();
        $currentEmployee = $sessionStorage->getMetaData('employee_id');
        $systemRole = $sessionStorage->getMetaData('role');

        $config = $this->getServiceLocator()->get('Config');
        $this->_employeeClass = new Employee($config);
        $employeeDetails = $this->_employeeClass->getEmployee($param);

        if((!$employeeDetails) || ($systemRole != 'system-administrator' && $currentEmployee != $param)){
            $data = array(
                'employee' => array(
                    'first_name' => 'Not Found',
                    'last_name' => '',
                ),
            );

        }else{
            $data = array(
                'employee' => $employeeDetails,
            );
        }

        return $data;
    }


    /**
    * @todo: Validation
    **/  

    public function updateAction() {
        $param = $this->params()->fromRoute('id');  

        $config = $this->getServiceLocator()->get('Config');
        $request = new Request();
        $posts = $request->getPost()->toArray();

        if ($posts == null) {                                    
            $this->_schedulesClass = new Schedules($config);
            $scheduleDetails = $this->_schedulesClass->getScheduleById($param);
            $this->_employeeClass = new Employee($config);
            $employeeDetails = $this->_employeeClass->getEmployee($scheduleDetails['employee_id']);

            return (array(
                'schedule' => $scheduleDetails,
                'employee' => $employeeDetails
            ));

        } else {           
            $this->_schedulesClass = new Schedules($config);
            $scheduleUpdate = $this->_schedulesClass->updateSchedule(
                array(
                    'employee_id' => $posts['employee_id'],
                    'type' => $posts['type'],
                    'start_time' => $posts['start_time'],
                    'end_time' => $posts['end_time'],
                    'start_date' => date('Y-m-d',strtotime($posts['start_date'])),
                    'end_date' => date('Y-m-d',strtotime($posts['end_date'])),
                    'approved_by' => $posts['user_employee_id'],
                    'approved_date' => new Expression('NOW()')
                ),
                array(
                    'id' => $posts['schedule']
                )
            );

            /*$pdValidator = new CbValidator('roles-add');
            $result = $pdValidator->validate($posts);
            if(is_array($result)){
                $view = new ViewModel(
                    array('data' =>
                        array('msg' => $result['msg']))
                    );
                $view->setTemplate('application/roles/notice.phtml'); // path to phtml file under view folder
                return $view;
            }            

            $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);
            $newRoleId = $rolesClass->createRole(
                    array(
                        'role' => $posts['role'],
                        'status' => $posts['status']
                    ), 
                    $posts['access']
                );
            */

            $view = new ViewModel(
                array(
                    'data' => array(
                        'url' => '/schedules/view/'.$posts['employee_id'],
                        'rolename' => '', 
                        'msg' => 'Schedule for <b>' . $posts['name'] . '</b> was successfully updated.',
                        'log_type' => LogsObject::LOG_TYPE_SCHEDULE_UPDATE
                    )
                )
            );
            
            $view->setTemplate('application/schedules/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }

    public function searchAction() {
       
        //$param = $this->params()->fromRoute('id');
        
        /**
        * @todo: Validation
        * $pdValidator = new CbValidator();
        * $result = $pdValidator->validate($params);
        * if(is_array($result)){
        *   echo json_encode($result);            
        *   die;
        * }
        **/

        $params = $this->params()->fromQuery();

        //$employeeDetails = $this->_employeeClass->getEmployee($param);
        $config = $this->getServiceLocator()->get('Config');
        $this->_schedulesClass = new Schedules($config);


        $schedulesDetails = $this->_schedulesClass->getSchedules($params);
        
        /*return(array(
            'employee' => $employeeDetails,
            'schedules' => $schedulesDetails['records'],
        ));
        */

        echo json_encode($schedulesDetails);

        die;
        
    }

    /**
    * @todo: Validation
    **/  
    public function addAction() {     
        $param = $this->params()->fromRoute('id');  
        
        $request = new Request();        
        $posts = $request->getPost()->toArray();

        $config = $this->getServiceLocator()->get('Config');
        $this->_employeeClass = new Employee($config);
        $employeeDetails = $this->_employeeClass->getEmployee($param);
        $data = $param;

        if($posts == null){ 
            if(!$employeeDetails){
                /**
                * @todo: add redirect page
                **/
                //$this->_helper->gotoUrl('/');
                return;

            }else{
                 $data = array(
                    'employee' => $employeeDetails,
                );
            }
            return $data;
        }else{

            $config = $this->getServiceLocator()->get('Config');
            $this->_schedulesClass = new Schedules($config);
            $newSched = $this->_schedulesClass->table->insert(
                    array(
                        'employee_id' => $posts['employee_id'],
                        'type' => $posts['type'],
                        'start_time' => $posts['start_time'],
                        'end_time' => $posts['end_time'],
                        'start_date' => date('Y-m-d',strtotime($posts['start_date'])),
                        'end_date' => date('Y-m-d',strtotime($posts['end_date'])),
                        'approved_by' => $posts['user_employee_id'],
                        'approved_date' => new Expression('NOW()')
                    )
                );
            
            $view = new ViewModel(
                array(
                    'data' => array(
                        'url' => '/schedules/view/'.$posts['employee_id'],
                        'rolename' => '', 
                        'msg' => 'New Schedule for <b>' . $posts['name'] . '</b> was successfully created.',
                        'log_type' => LogsObject::LOG_TYPE_SCHEDULE_ADD
                ))
            );
            $view->setTemplate('application/schedules/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }
    
}


