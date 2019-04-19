<?php
namespace Utilities;

use Zend\Db\TableGateway\TableGateway;

use Zend\Db\Sql\Where;

use Database\Model\RolesTable;
use Database\Model\RolesPagesTable;

class Roles
{       
    
    protected $_dbConfig;        
    protected $_rolesConfig;    
        
    protected $_adapter;
    protected $_rolesObject;
    protected $_rolesGateway;
    protected $_rolesTable;
    protected $_rolesPagesTable;

    function __construct($adapter, $config) {                        
        $this->_rolesConfig = $config;
        
        $this->_adapter = $adapter;                
        $this->_rolesTable = new RolesTable($adapter);
        $this->_rolesPagesTable = new RolesPagesTable($adapter);
    }
    
    public function getRolesList($page, $search = "", $sortCol = "id", $sortVal = "DESC"){        
        // TODO: Create service class for non-db-related functions  
                
        $columns = array(           
            "id",
            "role",
            "status",
            "dateCreated",
        );
        
        $limit = $this->_rolesConfig['limit_per_page'];        
        $offset = ($page-1) * $limit;                        
        
        //$search = 639271060231;
        
        $where = new Where();
        if(!empty($search)){
            $where->like("role", "%$search%");
        }
        
        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
//            'order' => 'orderDate ' . $sort,
            'order' => $sortCol.' '.$sortVal,
            'where' => $where,
        );                                 
        
        $records = $this->_rolesTable->fetch($params)->toArray();                  
        
        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_rolesTable->getCount($params);           
        
//        echo "<pre>";
//        print_r($records);
//        die();
        
        // Get headers
//        $headers = $this->getHeaders(array_keys(reset($records)));                                
        
        $pageCount = intval($count/$limit);
        if($count % $limit > 0) $pageCount++;        
        
        return array(
            //'headers' => $headers,
            'records' => $records,
            'count' => $count,
            'pageCount' => $pageCount,
            'currPage' => $page,    
            'limit' => $limit,
//            'headers' => $headers,
        );
    }
    
    public function update($posts) {        
        $result = $this->_rolesTable->update(array(
            "status" => $posts['status'],
            'role' => $posts['role'],
            //'dateCreated' => $posts['dateCreated']
                ), array('id' => $posts['id']));
    }
    
    public function getRolesObject($id){
        return $this->_rolesTable->getObject($id);
    }
    
    public function createRole($data, $pages){        
        $newRoleId = $this->_rolesTable->insert($data);                        
        if (!empty($pages)) {
            $this->updateRolesPages($pages, $newRoleId);            
        }
    }   
    
    public function updateRolesPages($pages, $role) {
        $this->_rolesPagesTable->deleteAllPagesByRoleId($role);
        foreach ($pages as $value) {
            $this->_rolesPagesTable->insert(array('role'=>$role, 'page'=> $value));
        }
    }
    
    public function getRolesPages($roleId) {
        $result = $this->_rolesPagesTable->fetchWhere(array(array('role' => $roleId)));
        $pages = array();
        
        foreach ($result as $rp) {
            //$page = $pageTable->getPagesObject($rp->page);
            $pages[] = $rp->page;
        }

        return $pages;
    }
    
    public function getAllRoles() {
        return $this->_rolesTable->fetch();
    }

    public function deleteRole($id){        
        $this->_rolesPagesTable->deleteAllPagesByRoleId($id);
        $this->_rolesTable->deleteById($id);        
    }    
}