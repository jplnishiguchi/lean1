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

            $view->setTemplate('application/index/notice.phtml'); // path to phtml file under view folder
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

            $view->setTemplate('application/index/notice.phtml'); // path to phtml file under view folder
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
        
        $monthlyComp = $data['daily_rate']*20;
        if($result['pay_period']==2){            
            $data['sss'] = $empTable->getSssContri($monthlyComp)*0.4;
            $data['philhealth'] = $empTable->getPhilhealthContri($monthlyComp);
            $data['hdmf'] = 100;
            $taxable = $taxable - $data['sss'] - $data['philhealth'] - $data['hdmf'];
        }
        
        $data['taxable'] = $taxable;
        $data['tax'] = $empTable->getTax($taxable);
        $data['net_income'] = $data['taxable']-$data['tax'];               
                      
        $view = new ViewModel(array(
            'data'=>$data
        ));  
        $view->setTemplate('application/pay/payslip.phtml');
        return $view;     
    }
}
