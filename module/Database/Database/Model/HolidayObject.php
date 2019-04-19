<?php

namespace Database\Model;

class HolidayObject {

    protected $_tableName = "holidays";

    public function getTablename() {
        return $this->_tableName;
    }

}
