<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class PagesTable {

    protected  $_tableName = "playdough_pages";
    protected $tableGateway;
    protected $_adapter;

    public function __construct($adapter) {               
        $this->_adapter = $adapter;
        $this->tableGateway = new TableGateway($this->_tableName, $this->_adapter);
    }

    public function fetch($params = array()) {        
        $resultSet = $this->tableGateway->select(
                        function(Select $select) use($params)
                        {
                            foreach($params as $key=>$value){
                                if($params[$key]) { $select->$key($value); }
                            }                            
                        }
                    );
        return $resultSet;                    
    }
    
    public function getCount($params = array()){        
        $resultSet = $this->tableGateway->select(
                        function(Select $select) use ($params)
                        {                       
                            $select->columns(
                                array(
                                    'count' => new Expression('COUNT(*)')
                                )
                            );
                            
                            foreach($params as $key=>$value){
                                if($params[$key]) { $select->$key($value); }
                            }                 
                        }
                    )->toArray();
        
        return $resultSet[0]['count'];
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
    
    public function getObject($id) {
        
        $dbRow = $this->getByField('id', $id);        

        return $dbRow;
    }
    
     public function update($set, $where) {
        $this->tableGateway->update($set, $where);
    }
    
    public function insert($set) {
        $this->tableGateway->insert($set);
    }
    
    public function deleteById($id) {
        $this->tableGateway->delete(array("id"=>$id));
    }

//    public function deleteUser($username)
//    {
//        $this->tableGateway->delete(array('username' => $username));
//    }
}
