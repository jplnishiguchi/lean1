<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Utilities\Requests;
use Database\Model\RequestTable;
use Utilities\CbValidator;
use Utilities\Employee;
use Zend\Db\Sql\Expression;
use Utilities\EmployeeJap;

class RequestController extends AbstractActionController {

	protected $_requestsClass = NULL;

	public function indexAction() {

        }

	public function addAction() {
            $request = new Request();
            $requestClass = new Requests($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

            $posts = $request->getPost()->toArray();
//		var_dump($posts);die;
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);
            if ($posts == null) {

            } else {
                $pdValidator = new CbValidator();
                $result = $pdValidator->validate($posts);
                if(is_array($result)){
                    $view = new ViewModel(array(
                        'data' => array(
                            'msg' => $result['msg']
                        )
                        ));
                    $view->setTemplate('application/request/notice.phtml'); // path to phtml file under view folder
                    return $view;
                }

                $posts['approved_by'] = 0;
                $posts['approved_date'] = $posts['date'] . " " . $posts['time'];

                $requestClass->addRequest($posts);

                $view = new ViewModel(array(
                        'data' => array(
                                'request_name' => $posts['type'], 'msg' => 'Request For <b>' . $posts['type'] . '</b> was successfully created.',
                        )
                ));
                $view->setTemplate('application/request/notice.phtml'); // path to phtml file under view folder
                return $view;
            }

	}

	public function viewAction(){
		$request = new Request();
		$posts = $request->getPost()->toArray();
		$auth = $this->getServiceLocator()->get('AuthService');
		$empClass = new EmployeeJap($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

		$data = $empClass->getByCompanyEmail($auth->getIdentity()->email);

		return array(
			'id' => $data['id']
		);
	}

	public function getallAction(){
        $config = $this->_getConfig();
        $this->_requestsClass = new Requests($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $params = $this->params()->fromQuery();

        $page = isset($params['pg']) ? $params['pg'] : 1;
        $orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "id";
        $sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "DESC";
        $columns = array(
                "id",
                "employee_id",
                "date",
                "type",
                "reason",
                "start",
                "end",
                "status",
                "status_reason",
                "approved_by",
                "approved_date"
        );

        $limit = $config['limit_per_page'];
        $offset = ($page - 1) * $limit;

		$search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_requestsClass->getList($search, $columns, $orderBy, $sort, $limit, $offset, $page);

        echo json_encode($resultList);

        die;
    }

    protected function _getConfig(){
        $config = $this->getServiceLocator()->get('Config');
        return $config['request'];
    }

    /**
    * @todo: validation
    **/
    public function disapproveAction() {
        /* Get Session variables systemRole and userEmployeeId*/
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionStorage = $sessionManager->getStorage();
        $systemRole = $sessionStorage->getMetaData('role');
        $userEmployeeId = $sessionStorage->getMetaData('employee_id');

        /* Get HTTP GET parameter */
        $param = $this->params()->fromRoute('id');

        $config = $this->_getConfig();
        $this->_requestsClass = new Requests($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        /* This is for the HTTP POST request */
        $request = new Request();
        $posts = $request->getPost()->toArray();

        if ($posts == null) {
            /* Get request details & employee (requester) details */
            $requestDetails = $this->_requestsClass->getWhere([['id' => $param]]);
            $config = $this->getServiceLocator()->get('Config');
            $this->_employeeClass = new Employee($config);
            $employeeDetails = $this->_employeeClass->getEmployee($requestDetails['employee_id']);

            return (array(
                'request' => $requestDetails,
                'employee' => $employeeDetails
            ));

        } else {
            $this->_requestsClass->updateRequest(
                array(
                    'status' => 'Disapproved',
                    'status_reason' => $posts['status_reason'],
                    'approved_by' => $userEmployeeId,
                    //'approved_date' => new Expression('NOW()')
                    'approved_date' => $posts['approve_date'] . " " . $posts['approve_time']
                )
                ,array('id' => $posts['request_id'])
            );

            $view = new ViewModel(array(
                    'data' => array(
                            'request_name' => $posts['type'], 'msg' => 'Request For <b>' . $posts['employee_name'] . '</b> was successfully disapproved.',
                    )
            ));
            $view->setTemplate('application/request/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }

    /**
    * @todo: validation
    **/
    public function approveAction() {
        /* Get Session variables systemRole and userEmployeeId*/
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionStorage = $sessionManager->getStorage();
        $systemRole = $sessionStorage->getMetaData('role');
        $userEmployeeId = $sessionStorage->getMetaData('employee_id');

        /* Get HTTP GET parameter */
        $param = $this->params()->fromRoute('id');

        $config = $this->_getConfig();
        $this->_requestsClass = new Requests($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        /* This is for the HTTP POST request */
        $request = new Request();
        $posts = $request->getPost()->toArray();

        if ($posts == null) {
            /* Get request details & employee (requester) details */
            $requestDetails = $this->_requestsClass->getWhere([['id' => $param]]);
            $config = $this->getServiceLocator()->get('Config');
            $this->_employeeClass = new Employee($config);
            $employeeDetails = $this->_employeeClass->getEmployee($requestDetails['employee_id']);

            return (array(
                'request' => $requestDetails,
                'employee' => $employeeDetails
            ));

        } else {
            $this->_requestsClass->updateRequest(
                array(
                    'status' => 'Approved',
                    'status_reason' => $posts['status_reason'],
                    'approved_by' => $userEmployeeId,
                    //'approved_date' => new Expression('NOW()')
                    'approved_date' => $posts['approve_date'] . " " . $posts['approve_time']
                )
                ,array('id' => $posts['request_id'])
            );

            $view = new ViewModel(array(
                    'data' => array(
                            'request_name' => $posts['type'], 'msg' => 'Request For <b>' . $posts['employee_name'] . '</b> was successfully approved.',
                    )
            ));
            $view->setTemplate('application/request/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }

    /**
    * @todo: validation
    **/
    public function cancelAction() {
        /* Get Session variables systemRole and userEmployeeId*/
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionStorage = $sessionManager->getStorage();
        $systemRole = $sessionStorage->getMetaData('role');
        $userEmployeeId = $sessionStorage->getMetaData('employee_id');

        /* Get HTTP GET parameter */
        $param = $this->params()->fromRoute('id');

        $config = $this->_getConfig();
        $this->_requestsClass = new Requests($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        /* This is for the HTTP POST request */
        $request = new Request();
        $posts = $request->getPost()->toArray();

        if ($posts == null) {
            /* Get request details & employee (requester) details */
            $requestDetails = $this->_requestsClass->getWhere([['id' => $param]]);
            $config = $this->getServiceLocator()->get('Config');
            $this->_employeeClass = new Employee($config);
            $employeeDetails = $this->_employeeClass->getEmployee($requestDetails['employee_id']);

            return (array(
                'request' => $requestDetails,
                'employee' => $employeeDetails
            ));

        } else {
            $this->_requestsClass->updateRequest(
                array(
                    'status' => 'Cancelled',
                    'status_reason' => $posts['status_reason'],
                    'approved_by' => $userEmployeeId,
                    //'approved_date' => new Expression('NOW()')
                    'approved_date' => $posts['approve_date'] . " " . $posts['approve_time']
                )
                ,array('id' => $posts['request_id'])
            );

            $view = new ViewModel(array(
                    'data' => array(
                            'request_name' => $posts['type'], 'msg' => 'Request For <b>' . $posts['employee_name'] . '</b> was successfully cancelled.',
                    )
            ));
            $view->setTemplate('application/request/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }

}