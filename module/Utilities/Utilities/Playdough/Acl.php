<?php

namespace Utilities\Playdough;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\UserObject;
use Database\Model\UserTable;
use Database\Model\OptionsObject;
use Database\Model\OptionsTable;
use Database\Model\RolesObject;
use Database\Model\RolesTable;
use Database\Model\PagesObject;
use Database\Model\PagesTable;
use Database\Model\RolesPagesObject;
use Database\Model\RolesPagesTable;

class Acl {

    public function initAcl($conf) {

        $acl = new \Zend\Permissions\Acl\Acl();
        //$roles = include __DIR__ . '/config/module.acl.roles.php';


        $adapter = new Adapter($conf);

//        $roleobj = new RolesObject();
//        $roleGateway = new TableGateway($roleobj->getTablename(), $adapter);
        $roleTable = new RolesTable($adapter);


//        $pageobj = new PagesObject();
//        $pageGateway = new TableGateway($pageobj->getTablename(), $adapter);
        $pageTable = new PagesTable($adapter);

//        $rpageobj = new RolesPagesObject();
//        $rpageGateway = new TableGateway($rpageobj->getTablename(), $adapter);
        $rpageTable = new RolesPagesTable($adapter);

        $allRoles = $roleTable->fetch();

        $allResources = array();
        foreach ($allRoles as $roleObj) {

            $role = new \Zend\Permissions\Acl\Role\GenericRole($roleObj['role']);
            if (!$acl->hasRole($role)) {
                $acl->addRole($role);
            }

            if ($roleObj['id'] == 1) {
                // all since this is super admin
                $rolePages = $pageTable->fetch();

                foreach ($rolePages as $rp) {


                    if (isset($rp->pagename)) {
                        //adding resources
                        if (!$acl->hasResource($rp->pagename))
                            $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($rp->pagename));

                        //adding restrictions
                        $acl->allow($role, $rp->pagename);
                    }
                }
            } else {
                $rolePages = $rpageTable->fetchWhere(array(array('role' => $roleObj['id'])));

                foreach ($rolePages as $rp) {

                    $where = new \Zend\Db\Sql\Where();
                    $resource = $pageTable->getObject($rp->page);

                    if (isset($resource->pagename)) {
                        //adding resources
                        if (!$acl->hasResource($resource->pagename))
                            $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource->pagename));

                        //adding restrictions
                        $acl->allow($role, $resource->pagename);
                    }
                }
            }
        }

        return $acl;
    }

}
