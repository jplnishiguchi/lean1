<?php

namespace Database\Model;

class EmployeeObject {

    public $gender_enum = [
        'male',
        'female'
    ];
    public $employment_status_enum = [
        'consultant',
        'freelance',
        'contractual',
        'probationary',
        'regular'
    ];
    public $group_enum = [
        'admin',
        'pmo',
        'dev'
    ];
    public $role_enum = [
        'HR',
        'Accountant',
        'Manager',
        'Project Manager',
        'Business Analyst',
        'Solutions Architect',
        'Senior Software Engineer',
        'Junior Software Engineer'
    ];
    public $shift_enum = [
        'Regular',
        'Flex'
    ];

    public $id;
    public $employee_number;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $gender;
    public $nickname;
    public $employment_status;
    public $date_hired;
    public $group;
    public $role;
    public $shift;
    public $personal_email;
    public $company_email;
    public $address;
    public $telephone;
    public $contact_number;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $salary;

    protected $_tableName = "employees";

    public function getTablename() {
        return $this->_tableName;
    }

}
