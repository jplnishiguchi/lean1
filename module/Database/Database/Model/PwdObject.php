<?php

namespace Database\Model;

class PwdObject {


    public $username;
    public $password;
    protected $_tableName = "playdough_login_pwd_history";

    public function getTablename() {
        return $this->_tableName;
    }

}
