<?php

namespace Utilities;

use Zend\Db\Sql\Where;
use Database\Model\EmpgroupTable;
use Database\Model\EmproleTable;

class Empgroup {
    
    protected $_adapter;    
    protected $_optionsTable;        

    function __construct($adapter) {
        $this->_adapter = $adapter;                
        $this->_empgroupTable = new EmpgroupTable($this->_adapter);        
        $this->_emproleTable = new EmproleTable($this->_adapter);    
    }

    public function getEmpgroupList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {

        $where = new Where();
        if (!empty($search)) {
            $where->like("option_key", "%$search%");
        }

        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
            'order' => $orderBy . ' ' . $sort,
            'where' => $where
        );

        //var_dump($params); 
        //die();
        $records = $this->_empgroupTable->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_empgroupTable->getTransactionCount($params);

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
    
    public function getEmproleList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {

        $where = new Where();
        if (!empty($search)) {
            $where->like("option_key", "%$search%");
        }

        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
            'order' => $orderBy . ' ' . $sort,
            'where' => $where
        );

        //var_dump($params); 
        //die();
        $records = $this->_emproleTable->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_emproleTable->getTransactionCount($params);

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
    
    public function addGroup($data){
        return $this->_empgroupTable->insert($data);
    }
    
    public function addRole($data){
        return $this->_emproleTable->insert($data);
    }

    public function updateGroup($posts) {        
        $result = $this->_empgroupTable->update(
                array(
                    "name" => $posts['name'],
                    "description" => $posts['description'],
                )
                ,array('id' => $posts['id'])
            );
    }
    
    public function updateRole($posts) {        
        $result = $this->_emproleTable->update(
                array(
                    "name" => $posts['name'],
                    "description" => $posts['description'],
                )
                ,array('id' => $posts['id'])
            );
    }
    
    public function groupExists($name){
        $result = $this->_empgroupTable->fetchWhere(array(array('name' => $name)));
        if(count($result) > 0) return true;
        return false;        
    }
    
    public function roleExists($name){
        $result = $this->_emproleTable->fetchWhere(array(array('name' => $name)));
        if(count($result) > 0) return true;
        return false;        
    }
    
    public function getGroupObject($id){        
        return $this->_empgroupTable->getObject($id);
    }
    
    public function getRoleObject($id){        
        return $this->_emproleTable->getObject($id);
    }
    
    public function getRoles(){
        $params = array(
            'columns' => array("name")
        );
        
        $result = $this->_emproleTable->fetchAll($params);
        $ret = array();
        foreach($result as $row){
            $ret[] = $row->name;
        }
        
        return $ret;
    }
    
    public function getGroups(){
        $params = array(
            'columns' => array("name")
        );
        
        $result = $this->_empgroupTable->fetchAll($params);
        $ret = array();
        foreach($result as $row){
            $ret[] = $row->name;
        }
        
        return $ret;
    }
}
