<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\ResultSet\ResultSet;


class UserTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
      Password hashing thru DB function.
     * */
    public function hashPasword($salt, $pass) {
        $adapter = $this->tableGateway->getAdapter();

        // please see reference: http://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php
        $statement = $adapter->createStatement("SELECT MD5(CONCAT(:salt, :pass)) AS password FROM DUAL");
        $results = $statement->execute(array('salt' => $salt, 'pass' => $pass));
        $password = null;
        foreach ($results as $result) {
            $password = $result['password'];
            break;
        }
        return $password;
    }
    
    public function getAssociatedUser($empId){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM playdough_login WHERE employee_id = :empId";
        
        $statement = $adapter->createStatement($sql);
        $results = $statement->execute(array('empId'=>$empId));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return isset($resultSet[0]) ? $resultSet[0] : NULL;
    }
    
    public function updateAssociatedUser($empId, $oldUser, $newUser){
        $adapter = $this->tableGateway->getAdapter();
        
        // update old user
        if(!empty($oldUser)){
            $sql = "UPDATE playdough_login SET employee_id=NULL WHERE username=:olduser";
            $statement = $adapter->createStatement($sql);
            $results = $statement->execute(array('olduser'=>$oldUser));
        }
        
        // update new user
        if(!empty($newUser)){
            $sql = "UPDATE playdough_login SET employee_id=:empid WHERE username=:newuser";
            $statement = $adapter->createStatement($sql);
            $results = $statement->execute(array('newuser'=>$newUser,'empid'=>$empId));
        }
        
        return true;
        
//        $sql = "SELECT * FROM playdough_login WHERE employee_id = :empId";
//        
//        $statement = $adapter->createStatement($sql);
//        $results = $statement->execute(array('empId'=>$empId));
//        $resultSet = new ResultSet;
//        $resultSet->initialize($results);
//        $resultSet = $resultSet->toArray();
//        return isset($resultSet[0]) ? $resultSet[0] : NULL;
    }
    
    public function getUsersWithoutEmployees(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM playdough_login WHERE employee_id IS NULL";
        
        $statement = $adapter->createStatement($sql);
        $results = $statement->execute();
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        return $resultSet->toArray();
    }

    public function isLocked($username) {
        $user = $this->getUserByField('username', $username);
        if (!isset($user->username)) {
            return FALSE;
        }

        $user = $this->getUserByField('username', $username);
        if ($user->locked == 0) {
            return FALSE;
        }
        return TRUE;
    }

    public function getUserByField($field, $value) {
//        if ($field == '')
//            throw new \Exception("field cannot be blank");
//        if ($value == '')
//            throw new \Exception("value cannot be blank");
        
        if ($field == '' || $value == '')
            return false;

        $rowset = $this->tableGateway->select(array($field => $value));
        $row = $rowset->current();
        return $row;
    }

    public function getUserByFields($fieldArray) {
        $rowset = $this->tableGateway->select($fieldArray);
        $row = $rowset->current();
        return $row;
    }

    public function getUserObject($userId) {
        $dbRow = $this->getUserByField('username', $userId);

        return $dbRow;
    }

    public function isAdmin($username) {
        $user = $this->getUserByField('username', $username);
        if (!isset($user->user_id)) {
            return FALSE;
        }

        return false;
    }

    public function fetchAll($params) {

        $resultSet = $this->tableGateway->select(
                function(Select $select) use($params) {
            foreach ($params as $key => $value) {
                if ($params[$key] && (!is_null($value))) {
                    $select->$key($value);
                }
            }
        }
        );
        return $resultSet;
    }

    public function fetchWhere($whereCriteria) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($whereCriteria) {


            if (!empty($whereCriteria)) {
                foreach ($whereCriteria as $cri) {
                    //var_dump($cri);
                    $select->where($cri);
                }
            }
        });
        return $resultSet;
    }

    public function update($set, $where) {
        $this->tableGateway->update($set, $where);
    }

    public function insert($set) {
        $this->tableGateway->insert($set);
    }

    public function deleteUser($username) {
        $this->tableGateway->delete(array('username' => $username));
    }

    public function getTransactionCount($params) {
        $resultSet = $this->tableGateway->select(
                        function(Select $select) use ($params) {
                    $select->columns(
                            array(
                                'count' => new Expression('COUNT(*)')
                            )
                    );

                    foreach ($params as $key => $value) {
                        if ($params[$key]) {
                            $select->$key($value);
                        }
                    }
                }
                )->toArray();

        return $resultSet[0]['count'];
    }

}
