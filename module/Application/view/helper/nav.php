<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

class Navhelper extends AbstractHelper{

    protected $serviceLocator;
    protected $authService;

    public function __invoke(){
        $auth = new AuthenticationService();
        // check page resources ACL
        $acl = $this->serviceLocator->get('acl');  

        if ($auth->hasIdentity()) {
            return $this->getView()->render('layout/nav', 
                array(
                    'loggedUser' => $this->serviceLocator->get('AuthService')->getStorage()->read()->loggedUser,
                    'acl' => $acl,
                    ));
        }
        
    }

    public function setServiceLocator($serviceLocator){
        $this->serviceLocator = $serviceLocator;
    }
}