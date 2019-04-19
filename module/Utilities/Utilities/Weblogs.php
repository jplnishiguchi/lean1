<?php

namespace Utilities;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Database\Model\WeblogsObject;
use Database\Model\WeblogsTable;

class Weblogs {

    protected $_dbConfig;
    protected $_config;
    protected $_adapter;
    protected $_object;
    protected $_gateway;
    protected $_table;

    function __construct($adapter, $config) { 
        $this->_config = $config;
        $this->_adapter = $adapter;
        $this->_table = new WeblogsTable($this->_adapter);
    }

    public function getResults($page, $search = "", $sortCol = "id", $sortVal = "DESC", $logType = NULL) {
        if($logType == 'password_reset' or $logType == 'user_add' or $logType == 'role_add' or $logType == 'page_add' or $logType== 'option_update'){
            $columns = array(
            "date_created",
            "username",
            "post_data",
            "log_type"
        );
        }elseif (is_null($logType) or $logType == 'failed_login' or $logType == 'account_lockout' or $logType == 'successful_login'){
            $columns = array(
            "id",
            "date_created",
            "username",
            "ipaddress",
            "url",
            "post_data",
            "notes",
            "event_status",
            "controller",
            "action",
            "source_url",
            "log_type"
        );
        }
        
        
        $limit = $this->_config['limit_per_page'];
        $offset = ($page - 1) * $limit;


        $whereArray = array();
//        if (!is_null($logType)) {
//            $where = new Where();
//            $where->like("log_type", "$logType");
//            $whereArray[] = $where;
//        }
        if ($logType == 'user_add'){
            $where = new \Zend\Db\Sql\Predicate\PredicateSet(array(
                new \Zend\Db\Sql\Predicate\Like('log_type', 'user_add'),
                new \Zend\Db\Sql\Predicate\Like('log_type', 'user_update'),
                new \Zend\Db\Sql\Predicate\Like('log_type', 'user_delete')
             ),   
                   \Zend\Db\Sql\Predicate\PredicateSet::OP_OR
            );
            $whereArray[] = $where;
        }
        elseif($logType == 'role_add') {
            $where = new \Zend\Db\Sql\Predicate\PredicateSet(array(
                new \Zend\Db\Sql\Predicate\Like('log_type', 'role_add'),
                new \Zend\Db\Sql\Predicate\Like('log_type', 'role_update'),
                new \Zend\Db\Sql\Predicate\Like('log_type', 'role_delete')
             ),   
                   \Zend\Db\Sql\Predicate\PredicateSet::OP_OR
            );
            $whereArray[] = $where;
        }
        elseif ($logType == 'page_add'){
            $where = new \Zend\Db\Sql\Predicate\PredicateSet(array(
                new \Zend\Db\Sql\Predicate\Like('log_type', 'page_add'),
                new \Zend\Db\Sql\Predicate\Like('log_type', 'page_update'),
                new \Zend\Db\Sql\Predicate\Like('log_type', 'page_delete')
             ),   
                   \Zend\Db\Sql\Predicate\PredicateSet::OP_OR
            );
            $whereArray[] = $where;
        }
        elseif (!is_null($logType)or $logType == 'failed_login' or $logType == 'account_lockout' or $logType == 'successful_login' or $logType == 'option_update') {
            $where = new Where();
            $where->like("log_type", "$logType");
            $whereArray[] = $where;
        }
            

        if (!empty($search)) {
            if ($logType == 'user_add' or $logType == 'role_add' or $logType == 'page_add' or $logType == 'option_update' or $logType == 'password_reset'){
            $whereSearch = new \Zend\Db\Sql\Predicate\PredicateSet(
                    array(
                new \Zend\Db\Sql\Predicate\Like('post_data', '%'.$search.'%'),
                new \Zend\Db\Sql\Predicate\Like('username', '%'.$search.'%')
                    ),
                    // optional; OP_AND is default
                    \Zend\Db\Sql\Predicate\PredicateSet::OP_OR
            );
            $whereArray[] = $whereSearch;
            }else{
            $whereSearch = new \Zend\Db\Sql\Predicate\PredicateSet(
                    array(
                new \Zend\Db\Sql\Predicate\Like('url', '%'.$search.'%'),
                new \Zend\Db\Sql\Predicate\Like('username', '%'.$search.'%')
                    ),
                    // optional; OP_AND is default
                    \Zend\Db\Sql\Predicate\PredicateSet::OP_OR
            );
            $whereArray[] = $whereSearch;
            }
        }

        $params = array(
            'limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
            'order' => $sortCol . ' ' . $sortVal,
            'where' => $whereArray,
        );

        $records = $this->_table->fetch($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_table->getCount($params);

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
    
}
