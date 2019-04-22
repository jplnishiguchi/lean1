<?php

namespace Application\Controller;

use Utilities\Employee;
use Utilities\EmployeeJap;
use Utilities\Empgroup;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Utilities\Playdough\User;
use Utilities\Playdough\Loggedusers;

use Database\Model\EmployeeJapTable;


class ReftableController extends AbstractActionController {

    public function sssAction() {      
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        
        $result = $empTable->getSssTableData();
        
        echo "<pre>";
        print_r($result);
        die();
    }
    
    public function myprofileAction() {
        $auth = $this->getServiceLocator()->get('AuthService');
        
        $role = $auth->getIdentity()->role;
        $params = $this->params()->fromQuery();
               
        if($role != "system-administrator" || ($role == "system-administrator" && !empty($params['id']))) {
            $empClass = new EmployeeJap($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
                    
            if($role == "system-administrator"  && !empty($params['id'])){
                $data = $empClass->getEmployeeObject($params['id']);
            }
            else{
                $data = $empClass->getByCompanyEmail($auth->getIdentity()->email);                       
            }

            $view = new ViewModel(array(
                "data" => $data,
            ));            
            $view->setTemplate('application/employee/index-single.phtml');
            return $view;
        }        
    }

    /**
     * @todo:validators
     */
    public function updateAction() {
        $request = new Request();
        $config = $this->getServiceLocator()->get('Config');
        
        $employee = new EmployeeJap($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        if($request->isPost()) {
            $posts = $request->getPost()->toArray();
            $employee->update($posts);
//            $employee = $employee->table->fetchWhere([['id' => $posts['id']]]);
            
            $view = new ViewModel(array(
                'data' => array(
                    'msg' => 'Employee was successfully updated.',                    
                    )
                )
                );
            $view->setTemplate('application/employee/notice.phtml'); // path to phtml file under view folder
            return $view;
        } else {
            
            $empId = $this->params()->fromRoute('id');
            
            $employee = $employee->_empTable->fetchWhere([['id' => $empId]]);

            //$egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));   
            $userUtil = new User($config);
            $userList = $userUtil->getUsersWithoutEmployees();
            $assocUser = $userUtil->getAssociatedUser($empId);
            $assocUserParam = !empty($assocUser) ? $assocUser['username'] : NULL;
            return new ViewModel([
                'employee' => $employee->current(),
                'acl' => $this->layout()->acl,
                'userlist' => $userList,
                'assocuser' => $assocUserParam
            ]);
        }        
    }
    
    public function addAction() {
        $request = new Request();
        $posts = $request->getPost()->toArray();

        $config = $this->getServiceLocator()->get('Config');

        if ($posts == null) {
            $userUtil = new User($config);
            
            $result = $userUtil->getUsersWithoutEmployees();
            
            $data = array('users'=>$result);
            return($data);
            
        } else {
            $config = $this->getServiceLocator()->get('Config');
            $empClass = new EmployeeJap($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $employee = $posts;           
            
            $employee['company_email'] = $posts['email'];
            $employee['date_hired'] = date('Y-m-d',strtotime($posts['emp']['date_hired']));
            $empClass->createEmployee($employee);

            $view = new ViewModel(
                    array(
                'data' => array(
//                    'username' => $posts['username'],
                    'msg' => 'Employee was successfully created.'
//                     'log_type' => LogsObject::LOG_TYPE_USER_ADD
                    ))
            );

            $view->setTemplate('application/user/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }
    
    public function viewallAction(){
        
    }
    
    public function viewallsearchAction() {
        $params = $this->params()->fromQuery();
        $this->_empClass = new EmployeeJap($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        
        $page = isset($params['pg']) ? $params['pg'] : 1;
        $orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "id";
        $sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";        
        $columns = array(
            "id",
            "employee_number",
            "last_name",
            "middle_name",
            "first_name",
//            "telephone",
//            "mobile",
//            "group",
//            "role",            
        );
//        $orderBy = "username";
//        $sort = "ASC";
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_empClass->getList($search, $columns, $orderBy, $sort, $limit, $offset, $page);

        echo json_encode($resultList);

        die;
    }

    public function viewAction() {

        $config = $this->getServiceLocator()->get('Config');
        $employee = new Employee($config);
        $employee = $employee->table->fetchWhere([['id' => $this->params()->fromRoute('id')]]);

        return new ViewModel([
            'employee' => $employee->current()
        ]);


    }

}
