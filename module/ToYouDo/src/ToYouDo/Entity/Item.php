<?php

namespace ToYouDo\Entity;

/**
 * Description of Item
 *
 * @author Matias Thomsen
 */
class Item {
    protected $id;
    protected $listId;
    protected $content;
    
    
    /** GETTER **/
    
    public function getId() {
        return $this->id;
    }

    public function getListId() {
        return $this->listId;
    }

    public function getContent() {
        return $this->content;
    }

    
    /** SETTER **/
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setListId($listId) {
        $this->listId = $listId;
    }

    public function setContent($content) {
        $this->content = $content;
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
            'listId'        => $this->getListId(),
            'content'        => $this->getContent(),
        );
    }
}
