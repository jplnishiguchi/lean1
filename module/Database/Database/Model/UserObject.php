<?php

namespace Database\Model;

class UserObject {

    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_ACTIVE = 'ACTIVE';

    public $username;
    public $password;
    protected $_tableName = "playdough_login";

    public function getTablename() {
        return $this->_tableName;
    }

}
