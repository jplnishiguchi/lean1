<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Confhelper extends AbstractHelper implements ServiceLocatorAwareInterface {

    protected $serviceLocator;

    public function __invoke() {

        $config = array_merge($this->getServiceLocator()->getServiceLocator()->get('Config'), $this->getServiceLocator()->getServiceLocator()->get('Options'));
        // $config is the object you need
        return $config;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

}
