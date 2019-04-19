<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;


class TimeTable {

    protected $tableGateway;
    protected $_tableName = "time_logs";
    protected $_adapter;

    
 
    public function __construct($adapter){
       $this->_adapter = $adapter;
        $this->tableGateway = new TableGateway($this->_tableName, $this->_adapter);

    }
       public function getObjectTime($id){
        $dbRow = $this->getById('id', $id);

        return $dbRow;
    }
      public function getObjectEmployee($id){
        $dbRow = $this->getById('id', $id);

        return $dbRow;
    }

       public function getById($field, $value) {
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

    public function update($set, $where) {
        $this->tableGateway->update($set, $where);
    }

    public function insert($set) {
        $this->tableGateway->insert($set);
    }

    public function deleteUser($username) {
        $this->tableGateway->delete(array('username' => $username));
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
}