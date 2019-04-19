<?php

namespace Utilities;

use Zend\Db\Sql\Where;
use Database\Model\OptionsTable;

class Options {

    protected $_tableName = "playdough_options";
    protected $_adapter;    
    protected $_optionsTable;        

    function __construct($adapter) {
        $this->_adapter = $adapter;                
        $this->_optionsTable = new OptionsTable($this->_adapter);        
    }

    public function getList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {

        $where = new Where();
        if (!empty($search)) {
            $where->like("option_key", "%$search%");
        }

        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
//            'order' => 'orderDate ' . $sort,
            'order' => $orderBy . ' ' . $sort,
            'where' => $where
        );

        //var_dump($params); 
        //die();
        $records = $this->_optionsTable->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_optionsTable->getTransactionCount($params);

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

    public function update($posts) {        
        $result = $this->_optionsTable->update(array(
            "option_value" => strip_tags($posts['option_value']),                        
                ), array('id' => $posts['id']));
    }
    
    public function getOptionsObject($id){
        return $this->_optionsTable->getObject($id);
    }
    
    public function getByOptionKey($optionKey){
        return $this->_optionsTable->getByOptionKey($optionKey);
    }
}
