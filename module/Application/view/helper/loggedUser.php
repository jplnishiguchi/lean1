<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

class LoggedUserhelper extends AbstractHelper{

    protected $serviceLocator;
    protected $authService;

    public function __invoke(){

        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            return $this->serviceLocator->get('AuthService')->getStorage()->read()->loggedUser;
        }
    }

	public function setServiceLocator($serviceLocator){
        $this->serviceLocator = $serviceLocator;
    }
}