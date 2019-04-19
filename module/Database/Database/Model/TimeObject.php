<?php

namespace Database\Model;

class TimeObject {

    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_ACTIVE = 'ACTIVE';

   // public $username;
    //public $password;
    protected $_tableName = "time_logs";

    public function getTablename() {
        return $this->_tableName;
    }

}
