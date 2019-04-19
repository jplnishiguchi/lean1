<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

class Breadcrumbhelper extends AbstractHelper{

    protected $serviceLocator;
    protected $authService;

    public function __invoke(){
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            return $this->getView()->render('layout/breadcrumb');
        }
        
    }
}