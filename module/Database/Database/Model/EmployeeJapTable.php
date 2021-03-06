<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\ResultSet\ResultSet;


class EmployeeJapTable {

    protected $_tableName = "employees";
    protected $_adapter;
    protected $tableGateway;
    protected $payGateway;

    public function __construct($adapter) {
        $this->_adapter = $adapter;
        $this->tableGateway = new TableGateway($this->_tableName, $this->_adapter);
        $this->payGateway = new TableGateway("pay", $this->_adapter);

    }

    public function getByField($field, $value) {
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

    public function fetchWhere($whereCriteria = NULL) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($whereCriteria) {
            if (!empty($whereCriteria)) {
                foreach ($whereCriteria as $cri) {
                    $select->where($cri);
                }
            }
        });
        return $resultSet;
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


    public function update($set, $where) {
        $this->tableGateway->update($set, $where);
    }

    public function insert($set) {        
        $this->tableGateway->insert($set);
        return $this->tableGateway->lastInsertValue;
    }

    public function delete($username) {
        $this->tableGateway->delete(array('username' => $username));
    }

    public function getObject($id) {
        $dbRow = $this->getByField('id', $id);

        return $dbRow;
    }
	public function getlastname($search){
		$dbRow = $this->getByField('last_name', $search);

		return $dbRow;
	}

    public function getByOptionKey($optionKey){
        $dbRow = $this->getByField('option_key', $optionKey);

        return $dbRow;
    }
    
    /**************** References table *****************/
    
    public function getSssTableData(){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM sss";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute();
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function getTaxTableData(){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM tax_table";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute();
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function getHdmfTableData(){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM hdmf";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute();
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function getPhilhealthTableData(){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM philhealth";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute();
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet[0];
    }
    
    /**************** Pay Module *****************/
    
    public function getEmployeeList(){
        $adapter = $this->_adapter;
        $sql = "SELECT id, first_name, last_name FROM employees ORDER BY last_name ASC";
        
        $statement = $adapter->createStatement($sql);
        $results = $statement->execute();
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function createPay($set) {     
        $set['status'] = "pending";
        $this->payGateway->insert($set);
        return $this->payGateway->lastInsertValue;
    }
    
    public function getPayList($params = []){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM employees INNER JOIN pay ON pay.employee_id=employees.id WHERE status='pending' ";
        $input = array();
        if(isset($params['pay_id'])){
            $sql.=" AND pay.id=:payid";
            $input['payid']=$params['pay_id'];
        }       
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($input);
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function getPreviewData($payId){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM pay JOIN employees ON pay.employee_id=employees.id WHERE pay.id=:payid";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("payid"=>$payId));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet[0];
    }
    
    public function getSssContri($monthly_compensation){
        $adapter = $this->_adapter;
        $sql = "SELECT total_contribution FROM sss WHERE compensation_level<:monthly_comp ORDER BY compensation_level DESC";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("monthly_comp"=>$monthly_compensation));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet[0]['total_contribution'];
    }
    
    public function getPhilhealthContri($monthly_compensation){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM philhealth";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("monthly_comp"=>$monthly_compensation));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        
        $row = $resultSet[0];
        $premium = $monthly_compensation*$row['employee_share'];
        
        if($premium<$row['premium_minimum']){
            return $row['premium_minimum'];
        }
        else if($premium>$row['premium_maximum']){
            return $row['premium_maximum'];
        }
        
        return $premium;
    }

    public function getTax($compensation){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM tax_table WHERE compensation_level<:comp ORDER BY compensation_level DESC";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("comp"=>$compensation));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        
        $row = $resultSet[0];
        
        return $row['minimum_tax'] + ($compensation-$row['compensation_level'])*$row['excess_multiplier'];
    }
    
    public function createOvertime($set) {     
        $otGateway = new TableGateway("overtime", $this->_adapter);

        $otGateway->insert($set);
        return $otGateway->lastInsertValue;
    }
    
    public function getOvertime($payId){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM overtime WHERE pay_id=:payid";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("payid"=>$payId));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        
        return $resultSet;                
    }
    
    public function createUndertime($set) {     
        $otGateway = new TableGateway("undertime", $this->_adapter);

        $otGateway->insert($set);
        return $otGateway->lastInsertValue;
    }
    
    public function getUndertime($payId){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM undertime WHERE pay_id=:payid";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("payid"=>$payId));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        
        return $resultSet;                
    }
    
    public function createAllowance($set) {     
        $otGateway = new TableGateway("allowance", $this->_adapter);

        $otGateway->insert($set);
        return $otGateway->lastInsertValue;
    }
    
    public function getAllowance($payId){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM allowance WHERE pay_id=:payid";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("payid"=>$payId));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        
        return $resultSet;                
    }
    
    public function createDeduction($set) {     
        $otGateway = new TableGateway("deduction", $this->_adapter);

        $otGateway->insert($set);
        return $otGateway->lastInsertValue;
    }
    
    public function getDeduction($payId){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM deduction WHERE pay_id=:payid";
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute(array("payid"=>$payId));
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        
        return $resultSet;                
    }
    
    public function createPayslip($set) {     
        $otGateway = new TableGateway("payslip", $this->_adapter);
        $otGateway->insert($set);
        
        $payGateway = new TableGateway("pay", $this->_adapter);
        $data = array("status"=>"generated");
        $payGateway->update($data,"id=".$set['pay_id']);
        
        return $otGateway->lastInsertValue;
    }
    
    public function getPayslipList($params = []){
        $adapter = $this->_adapter;
        $sql = "SELECT * FROM payslip";
        
        $input = array();
        if(isset($params['payslip_id'])){
            $sql.=" WHERE payslip.id=:payslipid";
            $input['payslipid']=$params['payslip_id'];
        }  
        if(isset($params['employee_id'])){
            $sql.=" WHERE payslip.employee_id=:employeeid";
            $input['employeeid']=$params['employee_id'];
        }  
        if(isset($params['pay_period'])){
            $sql.=" WHERE pay_period=:payperiod";
            $input['payperiod']=$params['pay_period'];
        }  
               
        
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($input);
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function getPayPeriods(){
        $adapter = $this->_adapter;
        $sql = "SELECT DISTINCT(pay_period) FROM payslip";
                
        $statement = $adapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($input);
        $resultSet = new ResultSet;
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
}
