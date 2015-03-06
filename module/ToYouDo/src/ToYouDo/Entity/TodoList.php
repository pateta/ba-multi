<?php

namespace ToYouDo\Entity;


/**
 * Description of List
 *
 * @author Matias Thomsen
 */
class TodoList {
    protected $id;
    protected $userId;
    protected $friendId;
    protected $name;
    protected $deadline;
    protected $date;
    protected $status;
    
    protected $userName;
    protected $friendName;
    
    /** GETTER **/
    
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getFriendId() {
        return $this->friendId;
    }

    public function getName() {
        return $this->name;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function getDate() {
        return $this->date;
    }
    
    function getUserName() {
        return $this->userName;
    }

    function getFriendName() {
        return $this->friendName;
    }

    function getStatus() {
        return $this->status;
    }

        
    
    /** SETTER **/
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setFriendId($friendId) {
        $this->friendId = $friendId;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDeadline($deadline) {
        $this->deadline = $deadline;
    }
    
    public function setDate($date) {
        $this->date = $date;
    }

    function setUserName($userName) {
        $this->userName = $userName;
    }

    function setFriendName($friendName) {
        $this->friendName = $friendName;
    }

    function setStatus($status) {
        $this->status = $status;
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
            'id'            => $this->getId(),
            'userId'        => $this->getUserId(),
            'friendId'      => $this->getFriendId(),
            'name'          => $this->getName(),
            'deadline'      => $this->getDeadline(),
            'date'          => $this->getDate(),
            'status'          => $this->getStatus(),
            'userName'      => $this->getUserName(),
            'friendName'    => $this->getFriendName(),
        );
    }
}
