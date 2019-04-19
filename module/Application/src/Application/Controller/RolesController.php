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
use Utilities\Roles;
use Utilities\CbValidator;
use Utilities\Playdough\User;
use Database\Model\LogsObject;

class RolesController extends AbstractActionController {

    public function indexAction() {
        
    }

    public function searchAction() {
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);                
        $config = $this->_getConfig();
        $rolesModel = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);

        $params = $this->params()->fromQuery();
        
        $pdValidator = new CbValidator();
        $result = $pdValidator->validate($params);
        if(is_array($result)){
            echo json_encode($result);            
            die;
        }

        $pg = isset($params['pg']) ? $params['pg'] : 1;
        $search = isset($params['search']) && !empty($params['search']) ? $params['search'] : null;
        $sortCol = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "id";
        $sortVal = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "ASC";

        $rolesList = $rolesModel->getRolesList($pg, $search, $sortCol, $sortVal);

        echo json_encode($rolesList);

        die;
    }

    public function updateAction() {       
        $request = new Request();        
        
        $config = $this->_getConfig();
        $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);

        $posts = $request->getPost()->toArray();
        $id = $request->getQuery('id');
        
        $postParam = $request->getPost('id');
        $getParam = $request->getQuery('id');
        if (empty($postParam) && empty($getParam)) {
           $this->redirect()->toRoute('roles');  
        } 
        
        if ($posts == null) {     
            
            $params = $this->params()->fromQuery();
        
            $pdValidator = new CbValidator();
            $result = $pdValidator->validate($params);
            if(is_array($result)){
                $this->redirect()->toRoute('roles');
            }
            
            $data = $rolesClass->getRolesObject($id);            
            $pagesClass = new Pages($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);

            $pages = $rolesClass->getRolesPages($id);
            $allpages = $pagesClass->fetch(array(
                'order' => 'controller ASC'
            ));


            return (array(
                'data' => $data,
                'pages' => $pages,
                'allpages' => $allpages
            ));
        } else {                                    
        
            $pdValidator = new CbValidator();
            $result = $pdValidator->validate($posts);
            if(is_array($result)){
                echo json_encode($result);            
                die;
            }

            $result = $rolesClass->update($posts);            
            $rolesClass->updateRolesPages($posts['access'], $posts['id']);

            $view = new ViewModel(array(
                'data' => array(
                    'rolename' => $posts['role'], 
                    'msg' => 'Role <b>' . $posts['role'] . '</b> was successfully updated.',
                    'log_type' => LogsObject::LOG_TYPE_ROLE_UPDATE)
            ));
            $view->setTemplate('application/roles/notice.phtml'); // path to phtml file under view folder
            return $view;
        }                

        return $result;
    }

    public function addAction() {
        $request = new Request();
        $posts = $request->getPost()->toArray();

        $config = $this->_getConfig();
        
        $pagesClass = new Pages($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);
        
        if ($posts == null) {                                    
            

            $allpages = $pagesClass->fetch(array(
                'order' => 'controller ASC'
            ));


            return (array(
                'allpages' => $allpages
            ));
        } else {            
            $pdValidator = new CbValidator('roles-add');
            $result = $pdValidator->validate($posts);
            if(is_array($result)){
                $view = new ViewModel(
                    array('data' =>
                        array('msg' => $result['msg']))
                    );
                $view->setTemplate('application/roles/notice.phtml'); // path to phtml file under view folder
                return $view;
            }            

            $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);
            $newRoleId = $rolesClass->createRole(
                    array(
                        'role' => $posts['role'],
                        'status' => $posts['status'],
                        'dateCreated' => $posts['date_created'],
                    ), 
                    $posts['access']
                );
            
            $view = new ViewModel(
                    array(
                'data' => array(
                    'rolename' => $posts['role'], 
                    'msg' => 'Role of <b>' . $posts['role'] . '</b> was successfully created.',
                    'log_type' => LogsObject::LOG_TYPE_ROLE_ADD
                    ))
            );
            
//            var_dump(array(
//                    'rolename' => $posts['role'], 
//                    'msg' => 'Role of <b>' . $posts['role'] . '</b> was successfully created.',
//                    'log_type' => LogsObject::LOG_TYPE_ROLE_ADD
//                    ));
//            die;
//            
            $view->setTemplate('application/roles/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }

    public function deleteAction() {         
//        error_reporting(E_ALL);
//        ini_set('display_errors', false);
        
        $config = $this->getServiceLocator()->get('Config');
        
        $params = $this->params()->fromQuery();        
        $pdValidator = new CbValidator();
        $result = $pdValidator->validate($params);
        if(is_array($result)){
            $view = new ViewModel(
                array(
                    'data' => array('msg' => $result['msg']),
                )
                );
                $view->setTemplate('application/roles/notice.phtml'); // path to phtml file under view folder
                return $view;
        }

        $request = new Request();
        $id = $request->getQuery('id');
                       
        $rolesClass = new Roles($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), $config);
        $rolesObj = $rolesClass->getRolesObject($id);
        
        if($rolesObj){            
            $userClass = new User($config);
            $userList = $userClass->getUsersByRole($rolesObj->role);
            
            if(count($userList)>0){
                $msg = "There are existing users with this role. Unable to proceed.";
            }else{
                $result = $rolesClass->deleteRole($id);
                $msg = 'Role of <b>' . $rolesObj['role'] . '</b> was successfully deleted.';
            }                                   
            
        } else{
            $msg = "Unknown Role. Deletion Failed.";
        }
        

        $view = new ViewModel(
                array(
                    'data' => array(
                        'msg' => $msg, 
                        'log_type' => LogsObject::LOG_TYPE_ROLE_DELETE)
                )
                );
        $view->setTemplate('application/roles/notice.phtml'); // path to phtml file under view folder
        return $view;
    }
    
    protected function _getConfig(){
        $config = $this->getServiceLocator()->get('Config');
        return $config['roles'];
    }

}
