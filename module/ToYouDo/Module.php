<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ToYouDo;

use Zend\Mvc\MvcEvent;
use ToYouDo\Entity\Friend;
use ToYouDo\Entity\User;
use ToYouDo\Entity\TodoList;
use ToYouDo\Entity\Item;
use ToYouDo\Model\FriendTable;
use ToYouDo\Model\UserTable;
use ToYouDo\Model\TodoListTable;
use ToYouDo\Model\TodoListItemTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        
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
                'toyoudo_friend_form' => 'ToYouDo\Factory\Form\FriendFormFactory',
                'toyoudo_list_form' => 'ToYouDo\Factory\Form\ListFormFactory',
                'toyoudo_item_form' => 'ToYouDo\Factory\Form\ItemFormFactory',
                'ToYouDo\Model\FriendTable' =>  function($sm) {
                    $tableGateway = $sm->get('FriendTableGateway');
                    $table = new FriendTable($tableGateway);
                    return $table;
                },
                'FriendTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Friend());
                    return new TableGateway('friend', $dbAdapter, null, $resultSetPrototype);
                },
                'ToYouDo\Model\UserTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'ToYouDo\Model\TodoListTable' =>  function($sm) {
                    $tableGateway = $sm->get('TodoListTableGateway');
                    $table = new TodoListTable($tableGateway);
                    return $table;
                },
                'TodoListTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TodoList());
                    return new TableGateway('todo_list', $dbAdapter, null, $resultSetPrototype);
                },
                'ToYouDo\Model\TodoListItemTable' =>  function($sm) {
                    $tableGateway = $sm->get('TodoListItemTableGateway');
                    $table = new TodoListItemTable($tableGateway);
                    return $table;
                },
                'TodoListItemTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Item());
                    return new TableGateway('item', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
