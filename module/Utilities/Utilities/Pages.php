<?php
namespace Utilities;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

use Zend\Db\Sql\Where;

use Database\Model\PagesObject;
use Database\Model\PagesTable;

use Database\Model\RolesPagesObject;
use Database\Model\RolesPagesTable;

class Pages
{       
    
    protected $_dbConfig;        
    protected $_pagesConfig;    
        
    protected $_adapter;
    protected $_pagesObject;
    protected $_pagesGateway;
    protected $_pagesTable;

    function __construct($adapter, $config) {
        $this->_pagesConfig = $config;        
        $this->_adapter = $adapter;                         
        $this->_pagesTable = new PagesTable($this->_adapter);
    }
    
    public function getPagesList($page, $search = "", $sortCol = "", $sortVal = ""){        
        // TODO: Create service class for non-db-related functions  
                
        $columns = array(
            "id",
            "controller",
            "action",
            "status",
            "route",
            "pagename",
            "dateCreated",
        );
        
        $limit = $this->_pagesConfig['limit_per_page'];        
        $offset = ($page-1) * $limit;                        
        
        //$search = 639271060231;
        
        $where = new Where();
        if(!empty($search)){
            $where->like("pagename", "%$search%");
        }
        
        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
//            'order' => 'orderDate ' . $sort,
            'order' => $sortCol.' '.$sortVal,
            'where' => $where,
        );                                 
        
        $records = $this->_pagesTable->fetch($params)->toArray();                  
        
        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_pagesTable->getCount($params);           
        
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
        $result = $this->_pagesTable->update(array(
            "controller" => $posts['controller'],
            "action" => $posts['action'],
            "status" => $posts['status'],
            "route" => $posts['route'],
            "pagename" => $posts['pagename'],                   
                ), array('id' => $posts['id']));
    }
    
    public function getPagesObject($id){
        return $this->_pagesTable->getObject($id);
    }
    
    public function createPage($data){
        return $this->_pagesTable->insert($data);
    }
    
    public function fetch($params) {
        return $this->_pagesTable->fetch($params);
    }
    
    public function deletePage($id){
        $rolesPagesObject = new RolesPagesObject();
        $rolesPagesGateway = new TableGateway($rolesPagesObject->getTablename(), $this->_adapter);
        $rolesPagesTable = new RolesPagesTable($rolesPagesGateway);
        
        $rolesPagesTable->deleteAllByPageId($id);
        $this->_pagesTable->deleteById($id);        
    }
}