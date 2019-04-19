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
use Zend\View\Model\ViewModel;
use Utilities\Options;
use Utilities\CbValidator;
use Database\Model\LogsObject;

use Zend\Http\PhpEnvironment\Request;


class OptionsController extends AbstractActionController
{
     protected $_optionsClass = NULL;
     
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function searchAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);

        $config = $this->_getConfig();
        $this->_optionsClass = new Options($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        /**
         * Set paging parameters
         */
        $params = $this->params()->fromQuery();
        
        $pdValidator = new CbValidator();
        $result = $pdValidator->validate($params);
        if(is_array($result)){
            echo json_encode($result);            
            die;
        }
        
        $page = isset($params['pg']) ? $params['pg'] : 1;
        $orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "option_key";
        $sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";        
        $columns = array(
            "id",
            "option_key",
            "option_value",
            "option_type",
            "option_description"
        );
//        $orderBy = "username";
//        $sort = "ASC";
        $limit = $config['limit_per_page'];
        $offset = ($page - 1) * $limit;

        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_optionsClass->getList($search, $columns, $orderBy, $sort, $limit, $offset, $page);

        echo json_encode($resultList);

        die;
    }
    
    public function updateAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);   
        $request = new Request();        
        $optionsClass = new Options($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        $posts = $request->getPost()->toArray();
        
        $validator = new CbValidator();
        $result = $validator->validate($posts);
        if(is_array($result)){
            $view = new ViewModel(array(
                'data' => array(
                    'msg' => $result['msg'])
                )
                );
            $view->setTemplate('application/options/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
        
        $id = $request->getQuery('id');     
        
        $postParam = $request->getPost('id');
        $getParam = $request->getQuery('id');
        if (empty($postParam) && empty($getParam)) {
           $this->redirect()->toRoute('options');  
        } 
        
        if ($posts == null) {                        
            $data = $optionsClass->getOptionsObject($id);
            return (array(
                'data' => $data
            ));
        } else {
            $result = $optionsClass->update($posts);
            
            $view = new ViewModel(array(
                'data' => array(
                    'msg' => 'Value of configuration: <b>' . $posts['option_key_hidden'] . '</b> was successfully updated.',
                    'log_type' => LogsObject::LOG_TYPE_OPTION_UPDATE
                    )
                )
                );
            $view->setTemplate('application/options/notice.phtml'); // path to phtml file under view folder
            return $view;
        }        

        return $result;
    }
    
    protected function _getConfig(){
        $config = $this->getServiceLocator()->get('Config');
        return $config['options'];
    }
}
