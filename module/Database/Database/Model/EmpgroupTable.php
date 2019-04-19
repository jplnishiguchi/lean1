<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class EmpgroupTable {

    protected $_tableName = "employee_groups";
    protected $_adapter;
    protected $tableGateway;

    public function __construct($adapter) {
        $this->_adapter = $adapter;
        $this->tableGateway = new TableGateway($this->_tableName, $this->_adapter);        
    }

    public function getByField($field, $value) {
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
    
    public function fetchWhere($whereCriteria = NULL) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($whereCriteria) {
            if (!empty($whereCriteria)) {
                foreach ($whereCriteria as $cri) {
                    $select->where($cri);
                }
            }
        });
        return $resultSet;
    }

    public function getTransactionCount($params) {
        $resultSet = $this->tableGateway->select(
                        function(Select $select) use ($params) {
                    $select->columns(
                            array(
                                'count' => new Expression('COUNT(*)')
                            )
                    );

                    foreach ($params as $key => $value) {
                        if ($params[$key]) {
                            $select->$key($value);
                        }
                    }
                }
                )->toArray();

        return $resultSet[0]['count'];
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
    
    public function getObject($id) {        
        $dbRow = $this->getByField('id', $id);

        return $dbRow;
    }
    
    public function getByOptionKey($optionKey){
        $dbRow = $this->getByField('option_key', $optionKey);
        
        return $dbRow;
    }

}
