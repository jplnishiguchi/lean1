<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Authhelper extends AbstractHelper implements ServiceLocatorAwareInterface {

    protected $serviceLocator;
    protected $authService;

    public function __invoke() {

        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            return $this->getView()->render('layout/usersetting', array('identity' => $auth->getIdentity()));
        }
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

}
