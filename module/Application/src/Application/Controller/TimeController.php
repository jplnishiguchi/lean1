<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Utilities\Playdough\Time;
use Utilities\Playdough\PdValidator;
use Utilities\CbValidator;
use Database\Model\TimeTable;
use Database\Model\EmployeeJapTable;
use Utilities\EmployeeJap;


class TimeController extends AbstractActionController {

    protected $_timeClass = NULL;
    protected $_EmployeeClass = NULL;

	public function indexAction() {
		$request = new Request();
		$posts = $request->getPost()->toArray();
		$auth = $this->getServiceLocator()->get('AuthService');
		$empClass = new EmployeeJap($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

		$data = $empClass->getByCompanyEmail($auth->getIdentity()->email);

		return array(
			'id' => $data['id']
		);

    }

    public function searchAction(){
        $this->_timeClass = new Time($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $params = $this->params()->fromQuery();
		$page = isset($params['pg']) ? $params['pg'] : 1;
		$orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "employee_id";
		$dateFrom = isset($params['date-from']) && !empty($params['date-from']) ? $params['date-from'] : "";
		$dateTo = isset($params['date-to']) && !empty($params['date-to']) ? $params['date-to'] : "";
		$sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";
		$whereArray = NULL;
        $columns = array(
            "id",
            "employee_id",
            "date",
            'clock_in',
            "clock_out",
        );
//        $orderBy = "username";
//        $sort = "ASC";

        //hardcoded
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_timeClass->getTimerList($search, $columns, $orderBy, $dateFrom, $dateTo, $sort, $limit, $offset, $page);


        echo json_encode($resultList);

        die;
    }

    public function addAction() {
        ini_set('display_errors', 0);
        $request = new Request();
        $posts = $request->getPost()->toArray();

        $config = $this->getServiceLocator()->get('Config');

        if ($posts == null) {
            $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config['roles']);
            $rolesList = $rolesClass->getAllRoles();
            return(array(
                'roles' => $rolesList
            ));
        } else {

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



    public function updateAction() {
        $request = new Request();
        $config = $this->getServiceLocator()->get('Config');
        $this->_TimeClass = new Time($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $posts = $request->getPost()->toArray();

        $postParam = $request->getPost('id');
        $getParam = $request->getQuery('id');



        if (empty($postParam) && empty($getParam)) {

            $this->redirect()->toRoute('time');

        }

        if ($posts == null) {

            $data = $this->_TimeClass->getTime($getParam);

           // $data2 = $this->_TimeClass->getEmployee($getParam);
            return (array(
                'data' => $data,
            ));

        } else {

            $result = $this->_TimeClass->updateTime($posts);
            $view = new ViewModel(
                    array(
                'data' => array(
                    'employee_id' => $posts['employee_id'],
                    'msg' => 'User Account <b>' . $posts['employee_id'] . '</b> was successfully updated.')
                    )
            );
            $view->setTemplate('application/time/notice.phtml'); // path to phtml file under view folder
            return $view;
        }


        return $result;
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

}
