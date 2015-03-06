<?php

namespace ToYouDo\Entity;

/**
 * Description of Friend
 *
 * @author Matias Thomsen
 */
class Friend {
    protected $id;
    protected $user_userId;
    protected $friend_userId;
    protected $accepted;
    
    protected $user_username;
    protected $friend_username;
    protected $friendId;




    /** GETTER **/
    
    public function getId() {
        return $this->id;
    }

    public function getUser_userId() {
        return $this->user_userId;
    }

    public function getFriend_userId() {
        return $this->friend_userId;
    }
    
    public function getAccepted() {
        return $this->accepted;
    }
    
    function getUser_username() {
        return $this->user_username;
    }

    function getFriend_username() {
        return $this->friend_username;
    }
    
    function getFriendId() {
        return $this->friendId;
    }

    
        
    /** SETTER **/
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setUser_userId($user_userId) {
        $this->user_userId = $user_userId;
    }

    public function setFriend_userId($friend_userId) {
        $this->friend_userId = $friend_userId;
    }
    
    public function setAccepted($accepted) {
        $this->accepted = $accepted;
    }

    function setUser_username($user_username) {
        $this->user_username = $user_username;
    }

    function setFriend_username($friend_username) {
        $this->friend_username = $friend_username;
    }

    function setFriendId($friendId) {
        $this->friendId = $friendId;
    }

        
        
    public function exchangeArray($array = array()) {
        foreach ($array as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $method = 'set' . ucfirst($key);
            if (!method_exists($this, $method)) {
                continue;
            }
            $this->$method($value);
        }
    }
    
    public function getArrayCopy() {
        return array(
            'id'                => $this->getId(),
            'user_userId'       => $this->getUser_userId(),
            'friend_userId'     => $this->getFriend_userId(),
            'accepted'          => $this->getAccepted(),
            'user_username'     => $this->getUser_username(),
            'friend_username'   => $this->getFriend_username(),
            'friendId'          => $this->getFriendId(),
        );
    }
}
