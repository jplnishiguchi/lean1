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
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;

use Utilities\Pages;
use Utilities\CbValidator;
use Database\Model\LogsObject;


class PagesController extends AbstractActionController
{
    public function indexAction()
    {
    }
    
    
    public function searchAction() {       
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);        
                
        $config = $this->_getConfig();
        $pagesModel = new Pages($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);
        
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
        $sortVal = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";
        
        $pagesList = $pagesModel->getPagesList($pg, $search, $sortCol, $sortVal);
                
        echo json_encode($pagesList);
//        
//        echo "<pre>";
//        print_r($transactionList);
        
        die;                           
    }
    
     public function updateAction() {   
//         error_reporting(E_ALL);
//        ini_set('display_errors', 1);  
        $request = new Request();        
        
        $config = $this->_getConfig();
        $pagesClass = new Pages($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);

        $posts = $request->getPost()->toArray();
        
        
        
        $id = $request->getQuery('id');      
        
        $postParam = $request->getPost('id');
        $getParam = $request->getQuery('id');
        if (empty($postParam) && empty($getParam)) {
           $this->redirect()->toRoute('pages');  
        } 
         
        if ($posts == null) {    
            $params = $this->params()->fromQuery(); 
            $pdValidator = new CbValidator();
            $result = $pdValidator->validate($params);
            if(is_array($result)){
                $this->redirect()->toRoute('pages');  
            }            
            
            $data = $pagesClass->getPagesObject($id);
            return (array(
                'data' => $data
            ));
        } else {    
            $pdValidator = new CbValidator();
            $result = $pdValidator->validate($posts);
            if(is_array($result)){
                $view = new ViewModel(array(
                    'data' => array(
                        'msg' => $result['msg']
                    )
                    )
                        );
                $view->setTemplate('application/pages/notice.phtml'); // path to phtml file under view folder
                return $view;
            }
            
            $result = $pagesClass->update($posts);

            $view = new ViewModel(array(
                'data' => array(
                    'pagename' => $posts['pagename'], 'msg' => 'Page <b>' . $posts['pagename'] . '</b> was successfully updated.',
                    'log_type' => LogsObject::LOG_TYPE_PAGE_UPDATE
                )
                )
                    );
            $view->setTemplate('application/pages/notice.phtml'); // path to phtml file under view folder
            return $view;
        }        

        return $result;
    }
    
    public function addAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);   
        $request = new Request();
        $posts = $request->getPost()->toArray();                

        if ($posts == null) {
            
        } else {
            
            $pdValidator = new CbValidator();
            $result = $pdValidator->validate($posts);
            if(is_array($result)){
                $view = new ViewModel(array(
                    'data' => array(
                        'msg' => $result['msg']
                    )
                    ));
                $view->setTemplate('application/pages/notice.phtml'); // path to phtml file under view folder
                return $view;
            }
                                                
            $config = $this->_getConfig();
            $pagesClass = new Pages($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);
            $result = $pagesClass->createPage(array(                                
                "controller" => $posts['controller'],
                "action" => $posts['action'],
                "status" => $posts['status'],
                "route" => $posts['route'],
                "pagename" => $posts['pagename'],               
            ));            

            $view = new ViewModel(array(
                'data' => array(
                    'pagename' => $posts['pagename'], 'msg' => 'Page <b>' . $posts['pagename'] . '</b> was successfully created.',
                    'log_type' => LogsObject::LOG_TYPE_PAGE_ADD
                )
                ));
            $view->setTemplate('application/pages/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }
    
    public function deleteAction(){        
        $request = new Request();
        
        $pdValidator = new CbValidator();        
        $params = $this->params()->fromQuery(); 
        $result = $pdValidator->validate($params);
        if(is_array($result)){
            $view = new ViewModel(array(
                'data' => array(
                    'msg' => $result['msg']
                )
                ));
            $view->setTemplate('application/pages/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
        
        $id = $request->getQuery('id');
        
        if (is_null($id) || strlen(trim($id))==0) {
            $view = new ViewModel(
                    array('data' => array('msg' => 'Unknown page. Deletion Failed.'))
            );
            $view->setTemplate('application/pages/notice.phtml'); // path to phtml file under view folder   
            return $view;
        }
        
        $config = $this->_getConfig();
        $pagesClass = new Pages($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);
        $pagesObj = $pagesClass->getPagesObject($id);                        

        $result = $pagesClass->deletePage($id);

        $view = new ViewModel(array(
            'data' => array(
                'msg' => 'Page <b>' . $pagesObj['pagename'] . '</b> was successfully deleted.',
                'log_type' => LogsObject::LOG_TYPE_PAGE_DELETE
            )
            ));
        $view->setTemplate('application/pages/notice.phtml'); // path to phtml file under view folder
        return $view;
    }
    
    protected function _getConfig(){
        $config = $this->getServiceLocator()->get('Config');
        return $config['pages'];
    }
}
