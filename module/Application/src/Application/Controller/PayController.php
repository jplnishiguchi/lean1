<?php

namespace Application\Controller;

use Utilities\Employee;
use Utilities\EmployeeJap;
use Utilities\Empgroup;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Utilities\Playdough\User;
use Utilities\Playdough\Loggedusers;

use Database\Model\EmployeeJapTable;


class PayController extends AbstractActionController {
    
    public function createpayAction(){
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getEmployeeList();
        
        $request = new Request();
        
        if($request->isPost()) {            
            $posts = $request->getPost()->toArray();
            
            $empTable->createPay($posts);
            
             $view = new ViewModel(
                    array(
                'data' => array(
                    'msg' => 'Pay was successfully created.'
                    ),
                )
            );

            $view->setTemplate('application/pay/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
        else{
            
        }
        $view = new ViewModel(array(
            "employees" => $result
        ));                    
        return $view;
    }

    public function paylistAction(){
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getPayList();
      
        $view = new ViewModel(array(
            "data" => $result
        ));                    
        return $view;     
    }
    
    public function paysliplistAction(){
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getPayslipList();
      
        $view = new ViewModel(array(
            "data" => $result
        ));                    
        return $view;     
    }
    
    public function mypayslipsAction(){
        $auth = $this->getServiceLocator()->get('AuthService');
        $empId = $auth->getIdentity()->employee_id;

        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getPayslipList(array("employee_id"=>$empId));        
        
        $view = new ViewModel(array(
            "data" => $result,
            "mypayslip" => true
        ));             
        $view->setTemplate('application/pay/paysliplist.phtml'); // path to phtml file under view folder
        return $view;     
    }
    
    
    private function computeOvertime($data){
        // hours
        // night
        // rest
        // excess
        // holiday
        
        $night = $data['night']==1 ? true : false;
        $rest = $data['rest']==1 ? true : false;
        $excess = $data['rest']==1 ? true : false;
        $regular = $data['holiday']==0 ? true : false;
        $regularHoliday = $data['holiday']==1 ? true : false;
        $special = $data['holiday']==2 ? true : false;
        
        $hourly_rate = $data['daily_rate']/8;
        $totalPay = $hourly_rate*$data['hours'];
        
        if($excess){
            if($rest && $regularHoliday){
                $totalPay*=3.38;
                if($night){
                    $totalPay*=3.718;
                }
            }else if($rest && $special){
                $totalPay*=1.95;
                if($night){
                    $totalPay*=2.145;
                }
            }else if($regularHoliday){
                $totalPay*=2.6;
                if($night){
                    $totalPay*=2.86;
                }
            }else if($special){
                $totalPay*=1.69;
                if($night){
                    $totalPay*=1.859;
                }
            }else if($rest){
                $totalPay*=1.69;
                if($night){
                    $totalPay*=1.859;
                }
            }else if($regular){
                $totalPay*=1.25;
                if($night){
                    $totalPay*=1.375;
                }
            }
        }else{
            if($rest && $regularHoliday){
                $totalPay*=2.6;
            }else if($rest && $special){
                $totalPay*=1.5;
            }else if($regularHoliday){
                $totalPay*=2;
            }else if($special){
                $totalPay*=1.3;
            }else if($rest){
                $totalPay*=1.3;
            }
            
            if($night){
                $totalPay*=1.1;
            }
        }
        
        return $totalPay;
    }
    
    public function addotAction(){
        $request = new Request();
        if($request->isPost()) {            
            $posts = $request->getPost()->toArray();
            
            $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $result = $empTable->getPayList(array("pay_id"=>$posts['pay_id']))[0];

            $posts['daily_rate'] = $result['salary'];
            
            $otType = "";
            if($posts['holiday']==0){
                $otType.="Regular Work Day";
            }else if($posts['holiday']==1){
                $otType.="Regular Holiday";
            }else{
                $otType.="Special Non-Working Day";
            }
            
            if($posts['night']==0){
                $otType.=", Day Shift";
            }else{
                $otType.=", Night Shift";
            }
            
            if($posts['rest']==1){
                $otType.=" ,Restday";
            }                        
            
            if($posts['excess']==1){
                $otType.=", In excess of 8 hours";
            }                        
          
            $data['pay_id'] = $posts['pay_id'];
            $data['ot_type'] = $otType;
            $data['total_pay'] = $this->computeOvertime($posts);      
            $data['hours'] = $posts['hours'];
            
            $empTable->createOvertime($data);            
             $view = new ViewModel(
                    array(
                'data' => array(
                    'msg' => 'Overtime was successfully created.'
                    ),
                )
            );

            $view->setTemplate('application/pay/notice.phtml'); // path to phtml file under view folder
            return $view;
        }else{
            $payId = $request->getQuery("id");
        
            $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
            $result = $empTable->getPayList(array("pay_id"=>$payId));

            $view = new ViewModel(array(
                'data'=>$result[0]
            ));                    
            return $view;     
        }
        
    }
    
    public function addutAction(){
        $request = new Request();
        if($request->isPost()) {            
            $posts = $request->getPost()->toArray();
            
            $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $result = $empTable->getPayList(array("pay_id"=>$posts['pay_id']))[0];

            $posts['daily_rate'] = $result['salary'];                                                
          
            $data['pay_id'] = $posts['pay_id'];            
            $data['hours'] = $posts['hours'];
            $data['total_ut'] = $posts['hours']*$posts['daily_rate']/8;
            
            $empTable->createUndertime($data);            
             $view = new ViewModel(
                    array(
                'data' => array(
                    'msg' => 'Undertime was successfully created.'
                    ),
                )
            );

            $view->setTemplate('application/pay/notice.phtml'); // path to phtml file under view folder
            return $view;
        }else{
            $payId = $request->getQuery("id");
        
            $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
            $result = $empTable->getPayList(array("pay_id"=>$payId));

            $view = new ViewModel(array(
                'data'=>$result[0]
            ));                    
            return $view;     
        }
    }
    
    public function addallowanceAction(){
        $request = new Request();
        if($request->isPost()) {            
            $posts = $request->getPost()->toArray();
            
            $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $result = $empTable->getPayList(array("pay_id"=>$posts['pay_id']))[0];

            $posts['daily_rate'] = $result['salary'];                                                
          
            $data['pay_id'] = $posts['pay_id'];            
            $data['type'] = $posts['type'];
            $data['amount'] = $posts['amount'];
            
            $empTable->createAllowance($data);            
             $view = new ViewModel(
                    array(
                'data' => array(
                    'msg' => 'Allowance was successfully created.'
                    ),
                )
            );

            $view->setTemplate('application/pay/notice.phtml'); // path to phtml file under view folder
            return $view;
        }else{
            $payId = $request->getQuery("id");
        
            $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
            $result = $empTable->getPayList(array("pay_id"=>$payId));

            $view = new ViewModel(array(
                'data'=>$result[0]
            ));                    
            return $view;     
        }
    }
    
    public function generateAction(){
        $request = new Request();
        $payId = $request->getQuery("id");
        
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getPreviewData($payId);        
        
        $data = array();
        $data['pay_id']=$payId;
        $data['employee_id'] = $result['employee_id'];
        $data['employee_number'] = $result['employee_number'];
        $data['name'] = $result['last_name'].", ".$result['first_name']." ".$result['middle_name'];
        
        $payPeriod = date('F', mktime(0, 0, 0, $result['month'], 10));
        if($result['pay_period']==1){
            $payPeriod.=" 1 to 15";
        }
        else{
            $lastDay = date("d", mktime(0, 0, 0, $result['month']+1,0,date("Y")));

            $payPeriod.= " 16 to ".$lastDay;
        }
        $payPeriod = " ".$payPeriod.", ".$result['year'];
        $data['pay_period'] = $payPeriod;
        $data['days_of_work'] = $result['days_of_work'];
        $data['daily_rate'] = $result['salary'];
        $data['total_regular_wage'] = $data['days_of_work']*$data['daily_rate'];        
        
        $taxable = $data['total_regular_wage'];
        
        $data['overtime'] = $empTable->getOvertime($payId);
        $totalOt = 0;
        foreach($data['overtime'] as $row){
            $totalOt+= $row['total_pay'];
        }
        $data['total_overtime'] = $totalOt;
        $taxable+=$totalOt;
        unset($data['overtime']);
        
        $data['undertime'] = $empTable->getUndertime($payId);
       
        $totalUt = 0;
        foreach($data['undertime'] as $row){
            $totalUt+= $row['total_ut'];
        }
        $data['total_undertime'] = $totalUt;
        $taxable-=$totalUt;
        unset($data['undertime']);
        
        $monthlyComp = $data['daily_rate']*20;
        $data['total_govt'] = 0;
        if($result['pay_period']==2){   
            $data['sss'] = $empTable->getSssContri($monthlyComp)*0.4;
            $data['philhealth'] = $empTable->getPhilhealthContri($monthlyComp);
            $data['hdmf'] = 100;
            $taxable = $taxable - $data['sss'] - $data['philhealth'] - $data['hdmf'];
            $data['total_govt'] = $data['sss']+$data['philhealth']+$data['hdmf'];
        }
        
        $data['taxable'] = $taxable;
        $data['tax'] = $empTable->getTax($taxable);
        $data['income_less_tax'] = $data['taxable']-$data['tax'];               
        
        $data['allowance'] = $empTable->getAllowance($payId);
        $totalAllowance = 0;
        foreach($data['allowance'] as $row){
            $totalAllowance+= $row['amount'];
        }
        $data['total_allowance'] = $totalAllowance;
        unset($data['allowance']);
        
        $data['net_income'] = $data['income_less_tax']+$totalAllowance;
        
        $empTable->createPayslip($data);
        
        $view = new ViewModel(
                array(
            'data' => array(
                'msg' => 'Payslip was successfully generated.'
                ),
            )
        ); 
        $view->setTemplate('application/pay/notice.phtml'); // path to phtml file under view folder
        return $view;
    }
    
    public function previewAction(){
        $request = new Request();
        $payId = $request->getQuery("id");
        
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getPreviewData($payId);        
        
        $data = array();
        $data['employee_number'] = $result['employee_number'];
        $data['name'] = $result['last_name'].", ".$result['first_name']." ".$result['middle_name'];
        
        $payPeriod = date('F', mktime(0, 0, 0, $result['month'], 10));
        if($result['pay_period']==1){
            $payPeriod.=" 1 to 15";
        }
        else{
            $lastDay = date("d", mktime(0, 0, 0, $result['month']+1,0,date("Y")));

            $payPeriod.= " 16 to ".$lastDay;
        }
        $payPeriod = " ".$payPeriod.", ".$result['year'];
        $data['pay_period'] = $payPeriod;
        $data['days_of_work'] = $result['days_of_work'];
        $data['daily_rate'] = $result['salary'];
        $data['total_regular_wage'] = $data['days_of_work']*$data['daily_rate'];        
        
        $taxable = $data['total_regular_wage'];
        
        $data['overtime'] = $empTable->getOvertime($payId);
        $totalOt = 0;
        foreach($data['overtime'] as $row){
            $totalOt+= $row['total_pay'];
        }
        $data['total_overtime'] = $totalOt;
        $taxable+=$totalOt;
        
        $data['undertime'] = $empTable->getUndertime($payId);
        $totalUt = 0;
        foreach($data['undertime'] as $row){
            $totalUt+= $row['total_ut'];
        }
        $data['total_undertime'] = $totalUt;
        $taxable-=$totalUt;                
        
        $monthlyComp = $data['daily_rate']*20;        
        $data['total_govt'] = 0;
        if($result['pay_period']==2){            
            $data['sss'] = $empTable->getSssContri($monthlyComp)*0.4;
            $data['philhealth'] = $empTable->getPhilhealthContri($monthlyComp);
            $data['hdmf'] = 100;
            $taxable = $taxable - $data['sss'] - $data['philhealth'] - $data['hdmf'];
            $data['total_govt'] = $data['sss']+$data['philhealth']+$data['hdmf'];
        }
        
        $data['taxable'] = $taxable;
        $data['tax'] = $empTable->getTax($taxable);
        $data['income_less_tax'] = $data['taxable']-$data['tax'];               
        
        $data['allowance'] = $empTable->getAllowance($payId);
        $totalAllowance = 0;
        foreach($data['allowance'] as $row){
            $totalAllowance+= $row['amount'];
        }
        $data['total_allowance'] = $totalAllowance;
        
        $data['net_income'] = $data['income_less_tax']+$totalAllowance;
                      
        $view = new ViewModel(array(
            'data'=>$data
        ));  
        $view->setTemplate('application/pay/payslip.phtml');
        return $view;     
    }
    
    public function viewpayslipAction(){
        $request = new Request();
        $payslipId = $request->getQuery("id");
        
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getPayslipList(array("payslip_id"=>$payslipId))[0];        
        $payId = $result['pay_id'];
        $result['overtime'] = $empTable->getOvertime($payId);
        $result['undertime'] = $empTable->getUndertime($payId);
        $result['allowance'] = $empTable->getAllowance($payId);
        
        $view = new ViewModel(array(
            'data'=>$result
        ));  
        $view->setTemplate('application/pay/payslip.phtml');
        return $view;     
    }
    
    public function viewmypayslipAction(){
        $request = new Request();
        $payslipId = $request->getQuery("id");
        
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));        
        $result = $empTable->getPayslipList(array("payslip_id"=>$payslipId))[0];        
        $payId = $result['pay_id'];
        $result['overtime'] = $empTable->getOvertime($payId);
        $result['undertime'] = $empTable->getUndertime($payId);
        $result['allowance'] = $empTable->getAllowance($payId);
        
        $view = new ViewModel(array(
            'data'=>$result,
            'mypayslip'=>true
        ));  
        $view->setTemplate('application/pay/payslip.phtml');
        return $view;     
    }
}
