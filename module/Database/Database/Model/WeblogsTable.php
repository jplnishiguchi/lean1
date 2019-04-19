<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class WeblogsTable {

    protected $_tableName = "playdough_web_logs";    
    protected $_tableGateway;
    protected $_adapter;

    public function __construct($adapter) {
        $this->_adapter = $adapter;
        $this->_tableGateway = new TableGateway($this->_tableName, $this->_adapter);
    }

    public function fetch($params = array()) {        
        $resultSet = $this->_tableGateway->select(
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
        $resultSet = $this->_tableGateway->select(
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

//    public function deleteUser($username)
//    {
//        $this->tableGateway->delete(array('username' => $username));
//    }
}
