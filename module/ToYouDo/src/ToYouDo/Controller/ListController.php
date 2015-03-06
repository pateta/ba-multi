<?php

namespace ToYouDo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ToYouDo\Form\ListForm;
use ToYouDo\Form\ItemForm;
use ToYouDo\Entity\TodoList;
use ToYouDo\Entity\Item;

/**
 * List controller for list and item manipulations.
 *
 * @author Matias Thomsen
 */
class ListController extends AbstractActionController {
    
    const ROUTE_LOGIN        = 'zfcuser/login';
    
    private $listForm;
    private $itemForm;
    
    private $friendTable;
    private $userTable;
    private $todoListTable;
    private $todoListItemTable;
    
    /**
     * Overview of self-created lists with action to manipulation.
     * 
     * @return ViewModel
     */
    public function indexAction()
    {   
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get all lists belonging to the authenticated User
        $toyoudoLists = $this->getTodoListTable()->getCreatedLists($userId);
        
        /*
         * - show all lists
         * - edit/delete list button
         * - add list button
         */
        return new ViewModel(array(
            'toyoudoLists' => $toyoudoLists
        ));
    }
    
    /**
     * Detail view for a selected list.
     * 
     * @return ViewModel
     */
    public function viewAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request ListId from route. If missing redirect to list overview.
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('list');
        }
        
        /**
         * Get TodoList based on found id, if it belongs
         * to the authenticated User
         * 
         * @var $list \ToYouDo\Entity\TodoList
         */
        $list = $this->getTodoListTable()->getEditList($id, $userId);
        
        // Fill the list with items
        $items = $this->getTodoListItemTable()->getItems($list);
        
        // Return ViewModel with list and items
        return new ViewModel(array(
            'list' => $list,
            'items' => $items,
        ));
    }
    
    /**
     * ADD list view.
     * 
     * @return ViewModel
     */
    public function addAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        /**
         * Get List, attach Users' and friends. If no frinds were found,
         * redirect to friends page.
         * 
         * @var $form ListForm
         */
        $form = $this->getListForm();
        $friends = $this->getFriendTable()->getFriends($userId);
        if ($friends->count() == 0) {
            $this->flashMessenger()->addInfoMessage('Sie haben noch keine Freunde. Bitte suchen Sie sich welche!');
            return $this->redirect()->toRoute('friend');
        }
        $form->setFriends($friends);
        
        $request = $this->getRequest();
        
        /**
         * On POST:
         *    Create a TodoList with provided data and redirect to lists' overview.
         */
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $list = new TodoList();
                $list->setUserId($userId);
                $list->setStatus(0);
                $list->exchangeArray($request->getPost()->toArray());
                $this->getTodoListTable()->save($list);
                $this->flashMessenger()->addInfoMessage('ToYouDo List wurde erzeugt!');
                
                return $this->redirect()->toRoute('list');
            }
        }
        
        // Return ViewModel with list creation form
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
    /**
     * EDIT list view
     * 
     * @return ViewModel
     */
    public function editAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request ListId from route. If missing redirect to list add action.
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('list', array(
                'action' => 'add'
            ));
        }
        
        /**
         * Get List, attach Users' friends, bind lists' data for edit op. and
         * change buttons' label.
         * If no frinds were found, redirect to friends page.
         * 
         * @var $list \ToYouDo\Entity\TodoList
         * @var $form ListForm
         */
        $list = $this->getTodoListTable()->getEditList($id, $userId);
        $form = $this->getListForm();
        $friends = $this->getFriendTable()->getFriends($userId);
        $form->setFriends($friends);
        $form->bind($list);
        $form->get('submit')->setAttribute('value', 'Bearbeiten');
        
        /**
         * On POST:
         *    Save (Update) TodoList with provided data and redirect
         *    to lists' overview.
         */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $list->exchangeArray($request->getPost()->toArray());
                $this->getTodoListTable()->save($list);
                $this->flashMessenger()->addInfoMessage('ToYouDo List wurde bearbeitet!');
                
                return $this->redirect()->toRoute('list');
            }
        }
        
        // Return ViewModel with editable list form.
        return new ViewModel(array(
            'form' => $form
        ));
    }
    
    /**
     * DELETE list action
     */
    public function deleteAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request ListId from route. If missing redirect to list add action.
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('list');
        }
        
        /*
         * Get list based on provided id and delete it.
         * 
         * @var $list \ToYouDo\Entity\TodoList
         */
        $list = $this->getTodoListTable()->getEditList($id, $userId);
        
        if ($list->getUserId() == $userId) {
            try {
                $this->getTodoListTable()->delete($list);
                $this->flashMessenger()->addInfoMessage('Aufgabe wurde entfernt!');
            } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
                $this->flashMessenger()->addInfoMessage('Die Aufgabe kann nicht entfernt werden. Ein unbekannter Fehler ist aufgetreten.');
            }
        } else {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
        }
        
        // Redirect to list overview
        return $this->redirect()->toRoute('list');
    }
    
    /**
     * CHECK list action
     * 
     */
    public function checkListAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request ListId from route. If missing redirect to list add action.
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('toyoudo');
        }
        
        /*
         * Get list based on provided id and mark it as done.
         * 
         * @var $list \ToYouDo\Entity\TodoList
         */
        $list = $this->getTodoListTable()->getFriendList($id, $userId);
        
        if ($list->getFriendId() == $userId) {
            try {
                $this->getTodoListTable()->check($list);
                $this->flashMessenger()->addInfoMessage('Aufgabe wurde erledigt!');
            } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
                $this->flashMessenger()->addInfoMessage('Die Aufgabe kann nicht erledigt werden. Ein unbekannter Fehler ist aufgetreten.');
            }
        } else {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
        }
        
        // Redirect to list detailed view
        return $this->redirect()->toRoute('toyoudo-list', array('action' => 'view', 'id' => $id));
    }
    
    
    
    public function uncheckListAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request ListId from route. If missing redirect to list add action.
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('toyoudo');
        }
        
        /*
         * Get list based on provided id and mark it as UNdone.
         * 
         * @var $list \ToYouDo\Entity\TodoList
         */
        $list = $this->getTodoListTable()->getFriendList($id, $userId);
        
        if ($list->getFriendId() == $userId) {
            try {
                $this->getTodoListTable()->uncheck($list);
                $this->flashMessenger()->addInfoMessage('Aufgabe rückgängig gemacht!');
            } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
                $this->flashMessenger()->addInfoMessage('Die Aufgabe kann nicht rückgängig gemacht werden. Ein unbekannter Fehler ist aufgetreten.');
            }
        } else {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
        }
        
        // Redirect to list detailed view
        return $this->redirect()->toRoute('toyoudo-list', array('action' => 'view', 'id' => $id));
    }
    
    
    public function addItemAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request ListId from route.
        $id = $this->params()->fromRoute('id', 0);
        
        /*
         * Get list based on provided id and get item form.
         * 
         * @var $list \ToYouDo\Entity\TodoList
         * @var $form \ToYouDo\Form\ItemForm
         */
        $list = $this->getTodoListTable()->getEditList($id, $userId);
        $form = $this->getItemForm();
        
        $request = $this->getRequest();
        
        /**
         * On POST:
         *    create a new Item and save it to the list
         *    mark list as UNchecke/UNdone
         *    redirect to list detailed view
         */
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $item = new Item();
                $item->setListId($list->getId());
                $item->exchangeArray($request->getPost()->toArray());
                $this->getTodoListItemTable()->save($item);
                
                $list->setStatus(0);
                $this->getTodoListTable()->save($list);
            
                $this->flashMessenger()->addInfoMessage('Aufgabe wurde hinzugefügt!');
                
                return $this->redirect()->toRoute('list', array('action' => 'view', 'id' => $list->getId()));
            }
        }
        
        return new ViewModel(array(
            'listId' => $id,
            'form' => $form
        ));
    }
    
    public function deleteItemAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request ItemId from route.
        $id = $this->params()->fromRoute('id', 0);
        
        /**
         * Get Item based on provided id and list for wich item belongs to.
         * If list belongs to authenticated user delete item from list.
         * 
         * @var $item \ToYouDo\Entity\Item
         * @var $list \ToYouDo\Entity\TodoList
         */
        $item = $this->getTodoListItemTable()->getListItem($id);
        $list = $this->getTodoListTable()->getEditList($item->getListId(), $userId);
        
        if ($list->getUserId() == $userId) {
            try {
                $this->getTodoListItemTable()->delete($item->getId());
                $this->flashMessenger()->addInfoMessage('Aufgabe wurde entfernt!');
            } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
                $this->flashMessenger()->addInfoMessage('Die Aufgabe kann nicht entfernt werden. Ein unbekannter Fehler ist aufgetreten.');
            }
        } else {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
        }
        
        // Redirect to list detailed overview
        return $this->redirect()->toRoute('list', array('action' => 'view', 'id' => $list->getId()));
    }
    
    public function editItemAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get ItemId from route
        $id = $this->params()->fromRoute('id', 0);
        
        /** 
         * Get Item entity based on provided id
         * @var $item \ToYouDo\Entity\Item
         */
        $item = $this->getTodoListItemTable()->getListItem($id);
        
        /**
         * Get List in wich found Item is in
         * @var $list \ToYouDo\Entity\TodoList
         */
        $list = $this->getTodoListTable()->getEditList($item->getListId(), $userId);
        
        /**
         * Get Form for Items' edit op., bind Item and change buttons' label
         * @var $form \ToYouDo\Form\ItemForm
         */
        $form = $this->getItemForm();
        
        $form->bind($item);
        $form->get('submit')->setAttribute('value', 'Bearbeiten');
        
        $request = $this->getRequest();
        
        /**
         * On POST:
         *    Save Items' attributes to the ItemList
         */
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $item->exchangeArray($request->getPost()->toArray());
                $this->getTodoListItemTable()->save($item);
                $this->flashMessenger()->addInfoMessage('Aufgabe wurde bearbeitet!');
                
                return $this->redirect()->toRoute('list', array('action' => 'view', 'id' => $item->getListId()));
            }
        }
        
        // Return ViewModel with Form for Items' edit op.
        return new ViewModel(array(
            'item' => $item,
            'form' => $form,
        ));
    }
    
    /**
     * 
     * @return \ToYouDo\Model\TodoListTable
     */
    public function getTodoListTable()
    {
        if (!$this->todoListTable) {
            $sm = $this->getServiceLocator();
            $this->todoListTable = $sm->get('ToYouDo\Model\TodoListTable');
        }
        return $this->todoListTable;
    }
    
    /**
     * 
     * @return \ToYouDo\Model\TodoListItemTable
     */
    public function getTodoListItemTable()
    {
        if (!$this->todoListItemTable) {
            $sm = $this->getServiceLocator();
            $this->todoListItemTable = $sm->get('ToYouDo\Model\TodoListItemTable');
        }
        return $this->todoListItemTable;
    }
    
    /**
     * 
     * @return \ToYouDo\Model\FriendTable
     */
    public function getFriendTable()
    {
        if (!$this->friendTable) {
            $sm = $this->getServiceLocator();
            $this->friendTable = $sm->get('ToYouDo\Model\FriendTable');
        }
        return $this->friendTable;
    }
    
    /**
     * 
     * @return \ToYouDo\Model\UserTable
     */
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('ToYouDo\Model\UserTable');
        }
        return $this->userTable;
    }
    
    /**
     * 
     * @return \ToYouDo\Factory\Form\ListForm
     */
    public function getListForm()
    {
        if (!$this->listForm) {
            $this->setListForm($this->getServiceLocator()->get('toyoudo_list_form'));
        }
        return $this->listForm;
    }
    
    /**
     * 
     * @param \ToYouDo\Factory\Form\ListForm $listForm
     */
    public function setListForm(ListForm $listForm) {
        $this->listForm = $listForm;
    }
    
    /**
     * 
     * @return \ToYouDo\Factory\Form\ItemForm
     */
    public function getItemForm()
    {
        if (!$this->itemForm) {
            $this->setItemForm($this->getServiceLocator()->get('toyoudo_item_form'));
        }
        return $this->itemForm;
    }
    
    /**
     * 
     * @param \ToYouDo\Factory\Form\ItemForm $itemForm
     */
    public function setItemForm(ItemForm $itemForm) {
        $this->itemForm = $itemForm;
    }

}
