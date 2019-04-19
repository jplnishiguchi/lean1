<?php

namespace Utilities;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\EmployeeTable;
use Database\Model\EmployeeObject;
use Database\Model\UserTable;


class Employee {

    protected $_config;
    protected $_dbConfig;
    protected $_adapter;
    protected $_gateway;
    protected $_identityObject;
    public $table;
    public $userTable;

    public function __construct($config) {
        $this->_config = $config;
        $this->_dbConfig = $config['db'];
        $this->_adapter = new Adapter($this->_dbConfig);
        $this->_identityObject = new EmployeeObject();
        $this->_gateway = new TableGateway($this->_identityObject->getTablename(), $this->_adapter);
        $this->table = new EmployeeTable($this->_gateway);
        $this->userTable = new UserTable(new TableGateway('playdough_login',$this->_adapter));
    }

    public function getEmployee($id) {
        $employee = $this->table->fetchWhere([['id'=>$id]]);
        return $employee->current();
    }

    public function update($data) {
        $oldUser = $data['assocuser'];
        $newUser = $data['username'];
        
        if(isset($newUser) && $newUser!=$oldUser){
            $this->userTable->updateAssociatedUser($data['id'], $oldUser, $newUser);
        }        
        
        unset($data['assocuser']);
        unset($data['username']);
        
        $this->table->update(
            $data, [
                'id' => $data['id']
            ]
        );
        
        return true;
    }

}
