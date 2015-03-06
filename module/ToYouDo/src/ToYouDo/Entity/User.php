<?php

namespace ToYouDo\Entity;

/**
 * Description of User
 *
 * @author Matias Thomsen
 */
class User {
    private $user_id;
    private $username;
    
    
    /** GETTER **/
    
    public function getUser_id() {
        return $this->user_id;
    }

    public function getUsername() {
        return $this->username;
    }

        
    
    /** SETTER **/
    
    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }
    
    public function setUsername($username) {
        $this->username = $username;
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
            'user_id'   => $this->getUser_id(),
            'username'  => $this->getUsername(),
        );
    }
}
