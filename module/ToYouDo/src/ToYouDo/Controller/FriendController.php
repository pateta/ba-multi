<?php

namespace ToYouDo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ToYouDo\Entity\User;
use ToYouDo\Entity\Friend;

/**
 * Friends controller for friends Management.
 *
 * @author Matias Thomsen
 */
class FriendController extends AbstractActionController {
    
    const ROUTE_LOGIN        = 'zfcuser/login';
    
    protected $friendTable;
    protected $userTable;
    protected $todoListTable;
    protected $friendForm;
    
    /**
     * Friends main page. Overview for friends, pending requests and
     * invitations.
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        // UserId from authenticated user
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        // Resultset: Users accepted as friend
        $friends = $this->getFriendTable()->getFriends($userId);
        // Resultset: Users waiting to be accepted as friends - outgoing
        $pending = $this->getFriendTable()->getOutgoingInvitations($userId);
        // Resultset: Users waiting to be accepted as friends - incoming
        $invitations = $this->getFriendTable()->getInvitations($userId);
        
        return new ViewModel(array(
            'friends' => $friends,
            'pending' => $pending,
            'invitations' => $invitations,
        ));
    }
    
    /**
     * Request a friendship and redirect indexAction
     */
    public function requestFriendshipAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request UserId from route. If missing redirect to overview.
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
            return $this->redirect()->toRoute('friend');
        }
        
        /**
         *  Get User by UserId.
         *  Save found User as Friend with status 0 (waiting for confirmation).
         *  This Request will be displayed as "waiting for confirmation"
         *  at users overview
         */
        $user = $this->getUserTable()->getUser($id);
        if ($user instanceof User) {
            /* @var $friend Friend */
            $friend = new Friend();
            $friend->setUser_userId($userId);
            $friend->setFriend_userId($user->getUser_id());
            $friend->setAccepted(0);
            $this->getFriendTable()->save($friend);
            
            $this->flashMessenger()->addInfoMessage('Die Einladung wurde versendet!');
        }
        
        // Redirect back to friends overview
        return $this->redirect()->toRoute('friend');
    }
    
    public function acceptFriendshipAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request FriendId from route. If missing redirect to overview.
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
            return $this->redirect()->toRoute('friend');
        }
        
        /**
         * Get Friend by FriendId
         * If Friend is found and requested confirmation belongs to the
         * authenticated User, change Friend status to 1 (accepted)
         * 
         * @var $friend Friend
         */
        $friend = $this->getFriendTable()->getFriend($id);
        if ($friend) {
            if ($friend->getFriend_userId() != $userId) {
                $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
                return $this->redirect()->toRoute('friend');
            }
            $friend->setAccepted(1);
            $this->getFriendTable()->save($friend);
            
            $this->flashMessenger()->addInfoMessage('Die Anfrage wurde bestätigt!');
        }
        
        // Redirect back to friends overview
        return $this->redirect()->toRoute('friend');
    }
    
    public function rejectFriendshipAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request FriendId from route. If missing redirect to overview.
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
            return $this->redirect()->toRoute('friend');
        }
        
        /**
         * Get Friend by FriendId
         * If Friend is found and requested rejection belongs to the
         * authenticated User, delete Friend.
         * 
         * @var $friend Friend
         */
        $friend = $this->getFriendTable()->getFriend($id);
        if ($friend) {
            if ($friend->getFriend_userId() != $userId) {
                $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
                return $this->redirect()->toRoute('friend');
            }
            $this->getFriendTable()->delete($friend->getId());
            
            $this->flashMessenger()->addInfoMessage('Die Anfrage wurde abgelehnt!');
        }
        
        // Redirect back to friends overview
        return $this->redirect()->toRoute('friend');
    }
    
    public function revokeFriendshipAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        // Get request FriendId from route. If missing redirect to overview.
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
            return $this->redirect()->toRoute('friend');
        }
        
        /**
         * Get Friend by FriendId
         * If Friend is found and requested revocation belongs to the
         * authenticated User, delete Friend.
         * 
         * @var $friend Friend
         */
        $friend = $this->getFriendTable()->getFriend($id);
        if ($friend) {
            if ($friend->getFriend_userId() != $userId && $friend->getUser_userId() != $userId) {
                $this->flashMessenger()->addInfoMessage('Ungültige Abfrage!');
                return $this->redirect()->toRoute('friend');
            }
            
            try {
                $this->getFriendTable()->delete($friend->getId());
                $this->getTodoListTable()->deleteAll($friend->getUser_userId(), $friend->getFriend_userId());
                $this->flashMessenger()->addInfoMessage('Die Freundschaft wurde gekündigt!');
            } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
                $this->flashMessenger()->addInfoMessage('Die Freundschaft kann nicht gekündigt werden. Ein unbekannter Fehler ist aufgetreten.');
            }
        }
        
        // Redirect back to friends overview
        return $this->redirect()->toRoute('friend');
    }
    
    public function searchAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        $matchedFriends = false;
        $form = $this->getFriendForm();

        $request = $this->getRequest();
        
        /**
         * On POST:
         *    Get users without a friendship relation to the authenticated User
         *    and return them to the ViewModel
         */
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $matchedFriends = $this->getUserTable()->searchInvitableUser($request->getPost()->get('search'), $userId);
                return new ViewModel(array(
                    'form' => $form,
                    'result' => $matchedFriends,
                ));
            }
        }
        
        // Return ViewModel with the friends' form for searching
        return new ViewModel(array(
            'form' => $form,
        ));
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
    
    public function getFriendForm()
    {
        if (!$this->friendForm) {
            $this->setFriendForm($this->getServiceLocator()->get('toyoudo_friend_form'));
        }
        return $this->friendForm;
    }
    
    public function setFriendForm($friendForm) {
        $this->friendForm = $friendForm;
    }
}
