<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class RolesPagesTable {

    protected $tableGateway;
    protected $_adapter;
    protected $_tableName = "playdough_roles_pages";

    public function __construct($adapter) {
        $this->_adapter = $adapter;
        $this->tableGateway = new TableGateway($this->_tableName, $this->_adapter);        
    }        

    public function fetchWhere($whereCriteria) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($whereCriteria) {

            if (!empty($whereCriteria)) {
                foreach ($whereCriteria as $cri) {
                    //var_dump($cri);
                    $select->where($cri);
                }
            }
            
            $select->order('role ASC');
        });
        return $resultSet;
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

   public function deleteAllPagesByRoleId($roleId)
    {
        $this->tableGateway->delete(array('role' => $roleId));
    }
   
   public function deleteAllByPageId($id)
   {
        $this->tableGateway->delete(array('page' => $id));
   }
}
