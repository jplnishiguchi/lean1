<?php
namespace Utilities;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

use Zend\Db\Sql\Where;

use Database\Model\SchedulesTable;
use Database\Model\SchedulesObject;

class Schedules {

    protected $_config;
    protected $_dbConfig;
    protected $_adapter;
    protected $_gateway;
    protected $_identityObject;
    public $table;

    public function __construct($config) {
        $this->_config = $config;
        $this->_dbConfig = $config['db'];
        $this->_adapter = new Adapter($this->_dbConfig);
        $this->_identityObject = new SchedulesObject();
        $this->_gateway = new TableGateway($this->_identityObject->getTablename(), $this->_adapter);
        $this->table = new SchedulesTable($this->_gateway);
    }

    public function getSchedules($params = array()) {
        $where = new Where();
        
        //if(!empty($search)){
            $where->like("employee_id", $params['employee_id']);
        //}

        $columns = array(           
            "id",
            "start_date",
            "end_date",
            "start_time",
            "end_time",
        );

        /** TODO: configurable limit for schedules **/
        $limit = 10;        
        $page = $params['pg'];
        $sortCol = 'id';
        $sortVal = 'ASC';
        $offset = ($page-1) * $limit; 

        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
            // 'order' => 'orderDate ' . $sort,
            'order' => $sortCol.' '.$sortVal,
            'where' => $where,
        );                                 
        
        $records = $this->table->fetchAll($params)->toArray();                  
        
        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        
        $count = $this->table->getCount($params);        

        //$schedules = $this->table->fetchAll($params);
        //return $schedules->current();

        //$pageCount = intval($count/$limit);
        //if($count % $limit > 0) $pageCount++;        
        
        return array(
            //'headers' => $headers,
            'records' => $records,
            'count' => $count,
            //'pageCount' => $pageCount,
            'currPage' => $page,    
            'limit' => $limit,
            //'headers' => $headers,
        );
    }

    public function getScheduleById($id){
        $rowset = $this->table->fetchWhere([['id' => $id]]);
        $row = $rowset->current();
        return $row;
    }

    public function updateSchedule($set,$where){
        $rowset = $this->table->update($set,$where);
    }

}
