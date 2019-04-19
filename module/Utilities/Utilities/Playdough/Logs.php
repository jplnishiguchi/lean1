<?php

namespace Utilities\Playdough;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Database\Model\LogsObject;
use Database\Model\LogsTable;

class Logs {

    protected $_dbConfig;
    protected $_adapter;
    protected $_logsObject;
    protected $_logsGateway;
    protected $_logsTable;

    function __construct($config) {
        $this->_dbConfig = $config['db'];

        $this->_adapter = new Adapter($this->_dbConfig);
        $this->_logsObject = new LogsObject();
        $this->_logsGateway = new TableGateway($this->_logsObject->getTablename(), $this->_adapter);
        $this->_logsTable = new LogsTable($this->_logsGateway);
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
        $records = $this->_logsTable->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->_logsTable->getTransactionCount($params);

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
        $result = $this->_logsTable->update(array(
            "option_value" => $posts['option_value'],
                ), array('id' => $posts['id']));
    }

    public function getOptionsObject($id) {
        return $this->_logsTable->getObject($id);
    }

    public function logrequest($username = NULL, $ipaddress = NULL, $url = NULL, $postData = NULL, $event = NULL, $controller = NULL, $action = NULL, $sourceURL, $notes = NULL, $logType = NULL) {

        //default
        if ($event == 'finish') {
            if (strpos($postData, "log_type") !== FALSE) {
                $postData_decode = json_decode($postData, true);
                $logType = $postData_decode['log_type'];
            }
        }

        //check log scenario
        if ($controller == 'index' && $action == 'index' && $sourceURL == "/login" && $event == 'finish') {
            $logType = LogsObject::LOG_TYPE_LOGIN_SUCCESS;
        }

        if ($controller == 'login' && $action == 'index' && $sourceURL == "/login" && $event == 'finish') {
            if (strpos($postData, "Login Failed") !== FALSE || strpos($postData, "Concurrent Login") !== FALSE) {
                $logType = LogsObject::LOG_TYPE_LOGIN_FAILED;
            }
        }

        if ($controller == 'login' && $action == 'index' && $sourceURL == "/login" && $event == 'finish') {
            if (strpos($postData, "Account has been locked") !== FALSE || strpos($postData, "account locked") !== FALSE) {
                $logType = LogsObject::LOG_TYPE_LOGIN_ACCOUNT_LOCK;
            }
        }





        $skipLog = FALSE;

        if ($controller == 'weblogs') {
            $skipLog = TRUE;
        }

        if (strpos($controller, "weblogs?show") !== FALSE) {
            $skipLog = TRUE;
        }

        if (!$skipLog) {
            $this->_logsGateway->insert(array(
                'username' => $username,
                'ipaddress' => $ipaddress,
                'url' => $url,
                'post_data' => $postData,
                'event_status' => $event,
                'controller' => $controller,
                'action' => $action,
                'source_url' => $sourceURL,
                'log_type' => $logType,
                'notes' => $notes
            ));
        }
    }

}
