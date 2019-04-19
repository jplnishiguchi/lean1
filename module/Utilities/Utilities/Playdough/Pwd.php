<?php

namespace Utilities\Playdough;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\PwdObject;
use Database\Model\PwdTable;
use Zend\Db\Sql\Where;

class Pwd {

    protected $_config;
    protected $_dbConfig;
    protected $_adapter;
    protected $_identityObject;
    protected $_gateway;
    public $table;

    public function __construct($config) {
        $this->_config = $config;
        $this->_dbConfig = $config['db'];
        $this->_adapter = new Adapter($this->_dbConfig);
        $this->_identityObject = new PwdObject();
        $this->_gateway = new TableGateway($this->_identityObject->getTablename(), $this->_adapter);
        $this->table = new PwdTable($this->_gateway);
    }

    public function updateUser($posts) {
        $userList = $this->table->update(array(
            'pwd_exp_date' => $posts['pwd_exp_date'],
            'role' => $posts['role'],
            'status' => $posts['status']
                ), array('username' => $posts['username']));
    }

    public function allowChange($password, $username, $limit) {

        $where = new Where();
        $where->like("username", $username);

        $params = array(
            'limit' => $limit,
            'order' => 'date_created DESC',
            'where' => $where
        );

        $records = $this->table->fetchAll($params)->toArray();
        
        $allowChange = 0;
       foreach ($records as $r) {
           if ($password == $r['password']) {
               $allowChange = 1;
           }
       }

        return $allowChange; 
    }
    
    public function deleteByUsername($username){
        $this->table->delete($username);
    }

}
