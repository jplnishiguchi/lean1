<?php

namespace Utilities;

class CbValidator {

    protected $_pageName;
    protected $_options;
    protected $_exceptions = array(
                                "password",
                                "password_confirm",
                                "new_password",
                                "option_value",
                                "access",
                                "usernames",
                                "pwd_exp_date",
							    "email",
                                "date_created",
                                "approve_date",
                                "approve_time",
                                "date",
                                "time",
                             );

    function __construct($pageName = null, $options = null) {
        $this->_pageName = $pageName;
        $this->_options = $options;
    }

    function validate($params){
        $invalidParams = array();
        foreach($params as $name=>$value){
            if(
                !in_array($name, $this->_exceptions)
                && !empty($value)
                && !preg_match("/^[a-zA-Z0-9-_ ]+$/", $value)
            )
            {
                $invalidParams[] = $name;
            }
            else if(($this->_pageName=="roles-update" || $this->_pageName=="roles-add") && $name=="access"){
                foreach($value as $access){
                    if(!is_numeric($access)){
                        $invalidParams[] = "access (page numbers)";
                        break;
                    }
                }
            }
            else if($this->_pageName=="user-bulkdelete" && $name=="usernames"){
                $value = explode(",", $value);
                if(is_array($value)){
                    foreach($value as $username){
                        if(!preg_match("/^[a-zA-Z0-9-_ ]+$/", $username)){
                            $invalidParams[] = $name;
                            break;
                        }
                    }
                }else{
                    $invalidParams[] = $name;
                }
            }
            else if($name=="pwd_exp_date"){
                if(!$this->validDate($value)){
                    $invalidParams[] = $name;
                }
            }
            else if($name=="password" || $name=="password_confirm" || $name=="new_password"){
                if(!preg_match("/[a-zA-Z\d!@#$%^&]{1}/", $value) || strlen($value) < (int) $this->_options['password_length']){
                    $invalidParams[] = $name;
                }
            }

        }
        if(count($invalidParams)>0){
            $ret = array(
                "invalid" => true,
                "msg" => "Invalid value for: " . implode(" ,", $invalidParams),
            );

            return $ret;
        }
        return true;
    }

    protected function validDate($dateStr){
        $phpDate = date_create($dateStr);
        if (!empty($dateStr) && $phpDate) {
            return true;
        }else{
            return false;
        }
    }
}
