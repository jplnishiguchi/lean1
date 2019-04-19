<?php

namespace Utilities\Playdough;

use Zend\Db\Sql\Where;
use Database\Model\EmployeeJapTable;
use Database\Model\TimeTable;
use Utilities\Employee;
use Zend\Authentication\AuthenticationService;

class Time {

    protected $_adapter;
    protected $_timeTable;
    protected $_employeeTable;

    public function __construct($adapter) {
        $this->_adapter = $adapter;
        $this->_timeTable = new TimeTable($this->_adapter);
        $this->_employeeTable = new EmployeeJapTable($this->_adapter);
    }

    public function getTime($id){

         $time = $this->_timeTable->getObjectTime($id);
         $employee = $this->_employeeTable->getObject($time['employee_id']);

            $time['last_name'] = $employee['last_name'];
            $time['first_name'] = $employee['first_name'];


         return $time;

    }

    public function selectTime($params){
        $time = $this->_timeTable->fetchAll($params);
        return $time->current();

    }

    public function getEmployee($id){
        return $this->_employeeTable->getObjectEmployee($id);
    }

    public function updateTime($posts) {
        /*  echo "<pre>";
        print_r($posts);
        die;*/
        $updatetime = $this->_timeTable->update(array(
                'clock_in' => $posts['clock_in'],
                'clock_out' => $posts['clock_out']
            ), array('id' => $posts['id'])
        );
    }


    public function getTimerList($search, $columns, $orderBy, $dateFrom, $dateTo, $sort, $limit, $offset, $page) {

        $where = new Where();
		if(!empty($search)) {
            /** If $search is digit or equal to "", add $where employee = $search **/
            if(ctype_digit($search) || $search == ""){
                $where->equalTo("employee_id", "$search");
            }
            /** else get employee detail using where last_name = $search, 
                then get the employee_id and add $where employee = $employeeDetails['id'] 
            **/
            else{
                $employeeObject = $this->_employeeTable->fetchAll(
                    array('where' => 
                        array('last_name' => $search)
                    )
                );
                $employeeDetails = $employeeObject->current();
                $where->equalTo("employee_id", $employeeDetails['id']);
            }
		}if(!empty($dateFrom)||!empty($dateTo)){
			$where->literal("date >= '".$dateFrom."' AND date <= '".$dateTo."'");
		}

        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
            // 'order' => 'orderDate ' . $sort,
            'order' => $orderBy . ' ' . $sort,
            'where' => $where
        );
        //die();
        $records = $this->_timeTable->fetchAll($params)->toArray();
        // query the count of all matching records. unset limiters first
        foreach($records as $key=>$record){
                $employee = $this->_employeeTable->getObject($record['employee_id']);

                $insArr = array(
                    'last_name' => $employee['last_name'],
                    'first_name' => $employee['first_name'],
                );

                $res = array_slice($record, 1, 1) + $insArr + array_slice($record, 0, count($record));
                $records[$key] = $res;
            }
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_timeTable->getTransactionCount($params);

        $pageCount = intval($count / $limit);
        if ($count % $limit > 0)
            $pageCount++;

        return array(
            'records' => $records,
            'count' => $count,
            'pageCount' => $pageCount,
            'currPage' => $page,
            'limit' => $limit,
        );
    }

    public function isLoggedIn($username) {
        $userData = $this->table->getUserObject($username);
        if (isset($userData) && $userData->logged == 1) {
            if ($userData->login_exp_date >= date("Y-m-d H:i:s")) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function getHashPassword($pass) {
        $staticSalt = $this->_config['static_salt'];
        $pass = $this->table->hashPasword($staticSalt, $pass);
        return $pass;
    }





    protected function _checkRequiredCols($cols) {
        $required = array('username', 'password', 'pwd_exp_date','role');
        sort($required);
        sort($cols);

        if ($required !== $cols) {
            return false;
        }

        return true;
    }

    public function getUsersByRole($role){
        $where = new Where();
        $where->equalTo("role",$role);
        $params['where'] = $where;

        return $this->table->fetchAll($params);
    }

    public function getLoggedusersList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {

        $where = new Where();
        if (!empty($search)) {
            $where->like("username", "%$search%");
        } if (empty($search)) {
            $where->like("logged", 1);
        }

        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
            //'order' => 'orderDate ' . $sort,
            'order' => $orderBy . ' ' . $sort,
            'where' => $where
        );

        //var_dump($params);
        //die();
        $records = $this->table->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->table->getTransactionCount($params);

        $pageCount = intval($count / $limit);
        if ($count % $limit > 0)
            $pageCount++;

        return array(
            'records' => $records,
            'count' => $count,
            'pageCount' => $pageCount,
            'currPage' => $page,
            'limit' => $limit,
        );
    }

    public function insertTime($insertObject){
        $this->_timeTable->insert($insertObject);
    }

}

