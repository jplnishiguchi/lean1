<?php

namespace Database\Model;

class LogsObject {


    public $username;
    public $password;
    protected $_tableName = "playdough_web_logs";
    
    const LOG_TYPE_LOGIN_SUCCESS = 'successful login';
    const LOG_TYPE_LOGIN_FAILED = 'failed login';
    const LOG_TYPE_LOGIN_ACCOUNT_LOCK = 'account lockout';
    const LOG_TYPE_USER_ADD = 'user add';
    const LOG_TYPE_USER_UPDATE = 'user update';
    const LOG_TYPE_USER_DELETE = 'user delete';
    const LOG_TYPE_ROLE_ADD = 'role add';
    const LOG_TYPE_ROLE_UPDATE = 'role update';
    const LOG_TYPE_ROLE_DELETE = 'role delete';
    const LOG_TYPE_OPTION_UPDATE = 'option update';
    const LOG_TYPE_PAGE_ADD = 'page add';
    const LOG_TYPE_PAGE_UPDATE = 'page update';
    const LOG_TYPE_PAGE_DELETE = 'page delete';
    const LOG_TYPE_USER_PWDRESET = 'password reset';
    const LOG_TYPE_SCHEDULE_ADD = 'schedules add';
    const LOG_TYPE_SCHEDULE_UPDATE = 'schedules update';
    const LOG_TYPE_TIMECLOCK_MISMATCH = 'timeclock timeclock';
    const LOG_TYPE_TIMECLOCK_LOGTIME = 'timeclock logtime';
    const LOG_TYPE_HOLIDAY_BULKADD = 'holiday bulkadd';

    public function getTablename() {
        return $this->_tableName;
    }

}
