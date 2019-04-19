<?php

namespace Utilities\Playdough;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\UserObject;
use Database\Model\UserTable;
use Zend\Db\Sql\Where;
use Utilities\Playdough\Pwd;

class User {

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
        $this->_identityObject = new UserObject();
        $this->_gateway = new TableGateway($this->_identityObject->getTablename(), $this->_adapter);
        $this->table = new UserTable($this->_gateway);
    }
    
    public function getUsersWithoutEmployees(){        
        return $this->table->getUsersWithoutEmployees();
    }
    
    public function getAssociatedUser($empId){
        return $this->table->getAssociatedUser($empId);
    }

    public function updateUser($posts) {
        $userList = $this->table->update(array(
            'pwd_exp_date' => $posts['pwd_exp_date_view'],
            'role' => $posts['role'],
            'locked' => $posts['locked']=='yes' ? 1 : 0,
            'status' => $posts['status'],
            'employee_id' => $posts['employee_id']
                ), array('username' => $posts['username']));
    }

    public function resetPass($posts, $resetNew = TRUE) {
        if ($resetNew) {
            $isnew = 1;
        } else {
            $isnew = 0;
        }
        if (!is_null($posts['new_password'])) {
            $staticSalt = $this->_config['static_salt'];
            $pass = $this->table->hashPasword($staticSalt, $posts['new_password']);
            $userList = $this->table->update(array(
                'password' => $pass,
                'is_new_user' => $isnew
                    ), array('username' => $posts['username']));

            $pwdHistory = new Pwd($this->_config);
            $pwdHistory->table->insert(array(
                'username' => $posts['username'],
                'password' => $pass
            ));
        }
        $userList = $this->table->update(array(
            'pwd_exp_date' => $posts['pwd_exp_date'],
                ), array('username' => $posts['username']));
    }

    public function renewPass($username, $pass, $pwd_exp_date) {
        if (!is_null($pass)) {
            $staticSalt = $this->_config['static_salt'];
            $pass = $this->table->hashPasword($staticSalt, $pass);
            $userList = $this->table->update(array(
                'password' => $pass,
                'pwd_exp_date' => $pwd_exp_date,
                'is_new_user' => 0
                    ), array('username' => $username));


            $pwdHistory = new Pwd($this->_config);
            $pwdHistory->table->insert(array(
                'username' => $username,
                'password' => $pass
            ));
        }
    }

    public function getUsersList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {

        $where = new Where();
        if (!empty($search)) {
            $where->like("username", "%$search%");
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
        $records = $this->table->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->table->getTransactionCount($params);

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

    public function isLoggedIn($username) {
        $userData = $this->table->getUserObject($username);
        if (isset($userData) && $userData->logged == 1) {
            if ($userData->login_exp_date >= date("Y-m-d H:i:s")) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function getHashPassword($pass) {
        $staticSalt = $this->_config['static_salt'];
        $pass = $this->table->hashPasword($staticSalt, $pass);
        return $pass;
    }

    public function deleteUser($username) {
        $pwd = new Pwd($this->_config);
        $pwd->deleteByUsername($username);

        $this->table->deleteUser($username);
    }

    public function bulkadd($file) {
        

        $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        if (empty($file) || !in_array($file['file']['type'], $mimes)) {
            $saveMessage[] = 'ERROR Wrong File Format.';
            return array('data' => array('saveMessages' => $saveMessage, 'file' =>$file ),'status' => FALSE);
        }
        
        $fileLocation = $file['file']['tmp_name'];

        $row = 1;

        $rowName = array();
        $bulkData = array();



        // try to open file on the tmp location
        if (($handle = fopen($fileLocation, "r")) !== FALSE) {
            // loop CSV
            $rowCount = 0;
            $userData = array();
            $rowName = array();
            $allData = array();
            while (($csvRow = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowCount++;
                // fetch first row to get headers
                if ($row == 1) {
                    //$rowName = $csvRow;
foreach ($csvRow as $val) {
$string = str_replace(' ', '-', $val);
$rowName[] = preg_replace('/[^A-Za-z0-9_\-]/', '', $string);
}
                    $row++;
                    if (!$this->_checkRequiredCols($rowName)) {
                        return array('status' => FALSE, 'data' => array(
                            'saveMessages' => array(
                            'Columns does not match, must have username, password, role and pwd_exp_date headers.',
'Received header are: '.implode(",", $rowName))
                            ));
                    };
                    continue;
                }

                // associate header to its data
                for ($csvColumn = 0; $csvColumn < count($rowName); $csvColumn++) {

                    $userData[$rowName[$csvColumn]] = $csvRow[$csvColumn];
                }

                $allData[] = $userData;
            }
            fclose($handle);
            return array('status' => TRUE, 'data' => $allData);
        }
    }

    protected function _checkRequiredCols($cols) {
        $required = array('username', 'password', 'pwd_exp_date','role');
        sort($required);
        sort($cols);
        
        //var_dump($required);
        //var_dump($cols);

        if ($required !== $cols) {
            return false;
        }

        return true;
    }
    
    public function getUsersByRole($role){
        $where = new Where();
        $where->equalTo("role",$role);
        $params['where'] = $where;
        
        return $this->table->fetchAll($params);
    }
    
    public function getLoggedusersList($search, $columns, $orderBy, $sort, $limit, $offset, $page) {
        
        $where = new Where();
        if (!empty($search)) {
            $where->like("username", "%$search%");
        } if (empty($search)) {
            $where->like("logged", 1);
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
        $records = $this->table->fetchAll($params)->toArray();

        // query the count of all matching records. unset limiters first
        unset($params['columns']);
        unset($params['limit']);
        unset($params['offset']);
        unset($params['order']);
        $count = $this->table->getTransactionCount($params);

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
