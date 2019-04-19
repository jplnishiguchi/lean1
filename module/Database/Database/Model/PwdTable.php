<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class PwdTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getUserByField($field, $value) {
//        if ($field == '')
//            throw new \Exception("field cannot be blank");
//        if ($value == '')
//            throw new \Exception("value cannot be blank");
        
        if ($field == '' || $value == '')
            return false;

        $rowset = $this->tableGateway->select(array($field => $value));
        $row = $rowset->current();
        return $row;
    }

    public function getUserByFields($fieldArray) {
        $rowset = $this->tableGateway->select($fieldArray);
        $row = $rowset->current();
        return $row;
    }

    public function getUserObject($userId) {
        $dbRow = $this->getUserByField('username', $userId);

        return $dbRow;
    }

   
    public function fetchWhere($whereCriteria) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($whereCriteria) {


            if (!empty($whereCriteria)) {
                foreach ($whereCriteria as $cri) {
                    //var_dump($cri);
                    $select->where($cri);
                }
            }
        });
        return $resultSet;
    }
    
    public function fetchAll($params) {

        $resultSet = $this->tableGateway->select(
                function(Select $select) use($params) {
            foreach ($params as $key => $value) {
                if ($params[$key] && (!is_null($value))) {
                    $select->$key($value);
                }
            }
        }
        );
        return $resultSet;
    }

    public function update($set, $where) {
        $this->tableGateway->update($set, $where);
    }

    public function insert($set) {
        $this->tableGateway->insert($set);
    }

    public function delete($username) {
        $this->tableGateway->delete(array('username' => $username));
    }


}
