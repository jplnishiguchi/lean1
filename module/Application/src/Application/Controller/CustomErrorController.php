<?php


namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CustomErrorController extends AbstractActionController
{
    public function indexAction()
    {
    }
    
    public function notAllowedAction() {
        //echo "test"; //die();
    }
}
