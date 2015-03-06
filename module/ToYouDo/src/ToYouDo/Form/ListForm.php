<?php

namespace ToYouDo\Form;


use Zend\Form\Form;

/**
 * Description of ListForm
 * 
 * @author Matias Thomsen
 */
class ListForm extends Form {
    
    public function __construct($name = null)
    {
        parent::__construct('list');

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Bezeichnung'
            ),
            'attributes' => array(
                'id' => 'tyd-text-name',
                'placeholder' => 'Tragen Sie hier einen Namen ein',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'deadline',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Deadline',
                'format' => 'Y-m-d',
            ),
            'attributes' => array(
                'id' => 'tyd-text-name',
                'placeholder' => 'jjjj-mm-tt',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'friendId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Aufgaben-Bearbeiter'
            ),
            'attributes' => array(
                'id' => 'tyd-select-friend',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type'  => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'class' => 'btn btn-warning btn-block',
                'id' => 'tyd-btn-search',
                'value' => 'Speichern'
            ),
        ));
    }
    
    public function setFriends($friends = array()) {
        /* @var $friendSelect Zend\Form\Element\Select */
        $friendSelect = $this->get('friendId');
        
        foreach ($friends as $friend) {
            /* @var $friend \ToYouDo\Entity\Friend */
            $options[$friend->getFriendId()] = $friend->getFriend_username();
        }
        
        if (isset($options)) {
            $friendSelect->setAttribute('options', $options);
        }
    }
}
