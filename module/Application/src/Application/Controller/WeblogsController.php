<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Utilities\Weblogs;
use Utilities\CbValidator;
use Zend\View\Model\ViewModel;

class WeblogsController extends AbstractActionController {

    public function indexAction() {
        $request = $this->getRequest();
        $showTab = $request->getQuery('show');
        
        return array('data' => array(
                'showTab' => $showTab
        ));
    }

    public function searchAction() {
//        $config = $this->getServiceLocator()->get('Config');
//        $model = new Weblogs($config);
        
        $config = $this->getServiceLocator()->get('Config');        
        $model = new Weblogs($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config['weblogs']);

        $params = $this->params()->fromQuery();
        
        $validator = new CbValidator();
        $result = $validator->validate($params);
        if(is_array($result)){
            echo json_encode($result);            
            die;
        }

        $pg = isset($params['pg']) ? $params['pg'] : 1;
        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : null;
        $sortCol = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "id";
        $sortVal = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "DESC";
        $logType = isset($params['logtype']) && !empty($params['logtype']) ? $params['logtype'] : NULL;

        $results = $model->getResults($pg, $search, $sortCol, $sortVal, $logType);

        echo json_encode($results);

        die;
    }

//    protected function _setAdapter(){
//        if ($this->_adapter == null) {
//                $this->_adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
//        }
//    }
}
