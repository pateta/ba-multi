<?php

namespace ToYouDo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of ToyoudoController
 *
 * @author Matias Thomsen
 */
class ToyoudoController extends AbstractActionController {
    
    const ROUTE_LOGIN        = 'zfcuser/login';
    
    private $listForm;
    private $itemForm;
    
    private $friendTable;
    private $userTable;
    private $todoListTable;
    private $todoListItemTable;
    
    /**
     * Overview of all lists (created by another User) for the authenticated User.
     * 
     * @return ViewModel
     */
    public function indexAction()
    {   
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get all incoming list that belong to the authenticated User.
        $toyoudoLists = $this->getTodoListTable()->getToyoudoLists($userId);
        
        // Return ViewModel with incoming lists
        return new ViewModel(array(
            'toyoudoLists' => $toyoudoLists
        ));
    }
    
    /**
     * Detailed list view with items (tasks) and the possibility to
     * check/uncheck the list.
     * 
     * @return ViewModel
     */
    public function viewAction()
    {   
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get ListId from route. Redirect to overview if not found.
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('toyoudo-list');
        }
        
        /**
         * Get list and itemsfor the provided ListId and authenticated User.
         *
         * @var $list \ToYouDo\Entity\TodoList
         */
        $list = $this->getTodoListTable()->getFriendList($id, $userId);
        
        $items = $this->getTodoListItemTable()->getItems($list);
        
        // Return ViewModel with list and corresponding items.
        return new ViewModel(array(
            'list' => $list,
            'items' => $items,
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
