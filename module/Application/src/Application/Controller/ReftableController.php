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


class ReftableController extends AbstractActionController {

    public function sssAction() {      
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        
        $result = $empTable->getSssTableData();
        
        $view = new ViewModel(array(
            "data" => $result
        ));                    
        return $view;        
    }
    
    public function taxAction() {      
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        
        $result = $empTable->getTaxTableData();
        
        $view = new ViewModel(array(
            "data" => $result
        ));                    
        return $view;        
    }
    
    public function hdmfAction() {      
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        
        $result = $empTable->getHdmfTableData();
        
        $view = new ViewModel(array(
            "data" => $result
        ));                    
        return $view;        
    }
    
    public function philhealthAction() {      
        $empTable = new EmployeeJapTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        
        $result = $empTable->getPhilhealthTableData();
        
        $view = new ViewModel(array(
            "data" => $result
        ));                    
        return $view;        
    }
}
