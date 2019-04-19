<?php

namespace Utilities;

use Zend\Db\Sql\Where;
use Database\Model\EmployeeJapTable;
use Database\Model\EmproleTable;

class EmployeeJap {

    protected $_adapter;
    protected $_empTable;
    protected $_egTable;

    function __construct($adapter) {
        $this->_adapter = $adapter;
        $this->_empTable = new EmployeeJapTable($this->_adapter);
        $this->_erTable = new EmproleTable($this->_adapter);
    }

    public function getList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {

        $where = new Where();
        if (!empty($search)) {
            $where->like("id", "$search")->or->like("employee_number", "$search")->or->like("last_name", "$search");
        }


        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
//            'order' => 'orderDate ' . $sort,
            'order' => $orderBy . ' ' . $sort,
            'where' => $where
        );

        //var_dump($params);
        //die();
        $records = $this->_empTable->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_empTable->getTransactionCount($params);

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

    public function update($posts) {
        $result = $this->_empTable->update(array(
            "option_value" => strip_tags($posts['option_value']),
                ), array('id' => $posts['id']));
    }

    public function getEmployeeObject($id){
        return $this->_empTable->getObject($id);
    }

    public function createEmployee($data){
        $roleObj = $this->_erTable->getByField("name", $data['role']);
        $data['employee_role_id'] = $roleObj->id;

        $this->_empTable->insert($data);
    }

    public function getByCompanyEmail($email){
        return $this->_empTable->getByField("company_email", $email);
    }
}
