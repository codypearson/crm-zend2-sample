<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CRM;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use CRM\Model\User;
use CRM\Model\UserTable;
use CRM\Model\AuthToken;
use CRM\Model\AuthTokenTable;
use CRM\Model\Customer;
use CRM\Model\CustomerTable;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'UserTableGateway' => function($sm)
                {
                    $dbadapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultsetprototype = new ResultSet();
                    $resultsetprototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbadapter, null, $resultsetprototype);
                },
                'CRM\Model\User' => function($sm)
                {
                    return new UserTable($sm->get('UserTableGateway'));
                },
                'AuthTokenTableGateway' => function($sm)
                {
                    $dbadapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultsetprototype = new ResultSet();
                    $resultsetprototype->setArrayObjectPrototype(new AuthToken());
                    return new TableGateway('auth_token', $dbadapter, null, $resultsetprototype);
                },
                'CRM\Model\AuthToken' => function($sm)
                {
                    return new AuthTokenTable($sm->get('AuthTokenTableGateway'));
                },
                'CustomerTableGateway' => function($sm)
                {
                    $dbadapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultsetprototype = new ResultSet();
                    $resultsetprototype->setArrayObjectPrototype(new Customer());
                    return new TableGateway('customer', $dbadapter, null, $resultsetprototype);
                },
                'CRM\Model\Customer' => function($sm)
                {
                    return new CustomerTable($sm->get('CustomerTableGateway'));
                }
            )
        );
    }
}
