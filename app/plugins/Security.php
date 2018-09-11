<?php

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

class Security extends Plugin
{
    public function __construct($dependencyInjector)
    {
        $this->_dependencyInjector = $dependencyInjector;
    }

    private function getAcl()
    {
        if (!isset($this->persistent->acl)) {
            $acl = new Phalcon\Acl\Adapter\Memory();
            $acl->setDefaultAction(Phalcon\Acl::DENY);
            $roles = [
                'Guest'      => new Role('Guest'),
                'Unverified' => new Role('Unverified'),
                'Verified'   => new Role('Verified'),
                'Admin'      => new Role('Admin'),
            ];
            foreach ($roles as $role) {
                $acl->addRole($role);
            }
            $AllResources = [
                //访客
                'GuestResources'      => [
                    'index' => [
                        'auth',
                        'signin',
                        'signup',
                    ],
                ],
                //未激活的用户
                'UnverifiedResources' => [
                    'index' => [
                        'index',
                        'logout',
                    ],
                ],
                //激活的用户
                'VerifiedResources'   => [
                    'index' => [
                        'index',
                        'logout',
                    ],
                ],
                //超级管理员
                'AdminResources'      => [
                    'index' => [
                        'index',
                        'logout',
                    ],
                ],
            ];
            foreach ($roles as $role) {
                foreach ($AllResources[$role->getName() . 'Resources'] as $resource => $actions) {
                    $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
                    foreach ($actions as $action) {
                        $acl->allow($role->getName(), $resource, $action);
                    }
                }
            }
            $this->persistent->acl = $acl;
        }
        return $this->persistent->acl;
    }

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $controller = $dispatcher->getControllerName();
        $action     = $dispatcher->getActionName();
        $auth       = null;
        if ($this->cookies->has('auth')) {
            $auth = $this->cookies->get('auth');
            $auth = unserialize($auth->getValue());
            $role = $auth['acl_group'];
        } else {
            $role = 'Guest';
            if (!in_array($action, ['auth', 'signin', 'signup'])) {
                header("Location: /auth");
            }
        }
        $acl     = $this->getAcl();
        $allowed = $acl->isAllowed($role, $controller, $action);
        if ($allowed != Acl::ALLOW) {
            $this->flash->error('无权限访问！');die;
            //$dispatcher->forward(array('controller' => 'index', 'action' => 'auth'));
            return false;
        }
    }
}
