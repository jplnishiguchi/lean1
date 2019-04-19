<?php

namespace Utilities;

use Zend\Db\Sql\Where;
use Database\Model\RequestTable;
use Database\Model\EmployeeJapTable;
use Zend\Authentication\AuthenticationService;

class Requests {

        protected $_adapter;
        protected $_requestTable;
        protected $_employeeTable;


        function __construct($adapter) {
            $this->_adapter = $adapter;
            $this->_requestTable = new RequestTable($this->_adapter);
            $this->_employeeTable = new EmployeeJapTable($this->_adapter);
        }

        public function insert($insertData){
            return $this->_requestTable->insert($insertData);
        }

        public function addRequest($data){
            $sessionManager = new \Zend\Session\SessionManager();
            $sessionStorage = $sessionManager->getStorage();
            $userEmployeeId = $sessionStorage->getMetaData('employee_id');

            $insertData = array(
                'employee_id' => $userEmployeeId,
                'type' => $data['type'],
                'reason' => $data['reason'],
                'start' => $data['start_date'],
                'end' => $data['end_date'],
                'date' => $data['date'],
                'approved_by' => $data['approved_by'],
                'approved_date' => $data['approved_date'],
            );

            $this->_requestTable->insert($insertData);
        }

        public function getList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {

            $where = new Where();
            if (!empty($search)) {
				$employee = $this->_employeeTable->getlastname($search);
				$where->equalTo("employee_id", $search)->or->equalTo("employee_id", $employee['id']);
            }

            $params = array(
                'limit' => $limit,
                'offset' => $offset,
                'columns' => $columns,
                //  'order' => 'orderDate ' . $sort,
                'order' => $orderBy . ' ' . $sort,
                'where' => $where
            );

            //var_dump($params);
            //die();
            $records = $this->_requestTable->fetchAll($params)->toArray();

            foreach($records as $key=>$record){
                $employee = $this->_employeeTable->getObject($record['employee_id']);

                $insArr = array(
                    'last_name' => $employee['last_name'],
                    'first_name' => $employee['first_name'],
                );

                $res = array_slice($record, 0, 2) + $insArr + array_slice($record, 2, count($record));
                $records[$key] = $res;
            }

            // query the count of all matching records. unset limiters first
            unset($params['columns']);
            unset($params['limit']);
            unset($params['offset']);
            unset($params['order']);
            $count = $this->_requestTable->getTransactionCount($params);

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

        public function getWhere($where){
            $result = $this->_requestTable->fetchWhere($where);
            $details = $result->current();
            return $details;
        }

        public function updateRequest($set,$where){
            $rowset = $this->_requestTable->update($set,$where);
        }

}

