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
use Utilities\Empgroup;


use Zend\Http\PhpEnvironment\Request;


class EmpgroupController extends AbstractActionController
{    
     
    public function groupsAction()
    {
        return new ViewModel();
    }
    
    public function groupsearchAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);        
        $this->_empgroupClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        /**
         * Set paging parameters
         */
        $params = $this->params()->fromQuery();                
        
        $page = isset($params['pg']) ? $params['pg'] : 1;
        $orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "option_key";
        $sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";        
        $columns = array(
            "id",
            "name",
            "description"
        );
//        $orderBy = "username";
//        $sort = "ASC";
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_empgroupClass->getEmpgroupList($search, $columns, $orderBy, $sort, $limit, $offset, $page);

        echo json_encode($resultList);

        die;
    }
    
    public function groupcheckAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);

        $params = $this->params()->fromQuery();
        
        $egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        echo $egClass->groupExists($params['name']);

        die();
    }
    
    public function groupaddAction(){        
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);   
        $request = new Request();
        $posts = $request->getPost()->toArray();                

        if ($posts == null) {
            
        } else {                                                            
            $egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $result = $egClass->addGroup(array(                                
                "name" => $posts['name'],
                "description" => $posts['description'],                            
            ));            

            $view = new ViewModel(array(
                'data' => array(
                    'name' => $posts['name'], 'msg' => 'Employee Group <b>' . $posts['name'] . '</b> was successfully created.',                    
                )
                ));
            $view->setTemplate('application/empgroup/groupnotice.phtml'); // path to phtml file under view folder
            return $view;
        }    
    }
    
    public function groupupdateAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);   
        $request = new Request();
        $posts = $request->getPost()->toArray();   

        $egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));                
        
        $id = $request->getQuery('id');     
        
        $postParam = $request->getPost('id');
        $getParam = $request->getQuery('id');
        if (empty($postParam) && empty($getParam)) {
           $this->redirect()->toRoute('home');  
        } 
        
        if ($posts == null) {                        
            $data = $egClass->getGroupObject($id);
            return (array(
                'data' => $data
            ));
        } else {
            $result = $egClass->updateGroup($posts);
            
            $view = new ViewModel(array(
                'data' => array(
                    'msg' => 'Details of employee group: <b>' . $posts['name'] . '</b> was successfully updated.',                    
                    )
                )
                );
            $view->setTemplate('application/empgroup/groupnotice.phtml'); // path to phtml file under view folder
            return $view;
        }        

        return $result;
    }   
    
    
    
    
    
    
    public function rolesAction()
    {
        return new ViewModel();
    }
    
    public function rolesearchAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);        
        $this->_empgroupClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        /**
         * Set paging parameters
         */
        $params = $this->params()->fromQuery();                
        
        $page = isset($params['pg']) ? $params['pg'] : 1;
        $orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "option_key";
        $sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";        
        $columns = array(
            "id",
            "name",
            "description"
        );
//        $orderBy = "username";
//        $sort = "ASC";
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_empgroupClass->getEmproleList($search, $columns, $orderBy, $sort, $limit, $offset, $page);

        echo json_encode($resultList);

        die;
    }
    
    public function rolecheckAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);

        $params = $this->params()->fromQuery();
        
        $egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));

        echo $egClass->roleExists($params['name']);

        die();
    }
    
    public function roleaddAction(){        
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);   
        $request = new Request();
        $posts = $request->getPost()->toArray();                

        if ($posts == null) {
            
        } else {                                                            
            $egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $result = $egClass->addRole(array(                                
                "name" => $posts['name'],
                "description" => $posts['description'],                            
            ));            

            $view = new ViewModel(array(
                'data' => array(
                    'name' => $posts['name'], 'msg' => 'Employee Role <b>' . $posts['name'] . '</b> was successfully created.',                    
                )
                ));
            $view->setTemplate('application/empgroup/rolenotice.phtml'); // path to phtml file under view folder
            return $view;
        }    
    }
    
    
    public function roleupdateAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);   
        $request = new Request();
        $posts = $request->getPost()->toArray();   

        $egClass = new Empgroup($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));                
        
        $id = $request->getQuery('id');     
        
        $postParam = $request->getPost('id');
        $getParam = $request->getQuery('id');
        if (empty($postParam) && empty($getParam)) {
           $this->redirect()->toRoute('home');  
        } 
        
        if ($posts == null) {                        
            $data = $egClass->getRoleObject($id);
            return (array(
                'data' => $data
            ));
        } else {
            $result = $egClass->updateRole($posts);
            
            $view = new ViewModel(array(
                'data' => array(
                    'msg' => 'Details of employee role: <b>' . $posts['name'] . '</b> was successfully updated.',                    
                    )
                )
                );
            $view->setTemplate('application/empgroup/rolenotice.phtml'); // path to phtml file under view folder
            return $view;
        }        

        return $result;
    }   
}
