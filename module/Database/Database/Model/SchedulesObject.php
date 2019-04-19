<?php
namespace Database\Model;

class SchedulesObject
{
    /**
     * TODO: Add columns, getters and setters
     * 
     */
    
    protected  $_tableName = "schedules";
    public $employee_id;
    
    public function getTablename() {
        return $this->_tableName;
    }


  
}