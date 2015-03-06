<?php

namespace ToYouDo\Form;


use Zend\Form\Form;

/**
 * Description of FriendForm
 * 
 * @author Matias Thomsen
 */
class FriendForm extends Form {
    
    public function __construct($name = null)
    {
        parent::__construct('friend');
        
        $this->add(array(
            'name' => 'search',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Nickname'
            ),
            'attributes' => array(
                'id' => 'tyd-text-search',
                'placeholder' => 'Search for nickname',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type'  => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'class' => 'btn btn-warning btn-block',
                'id' => 'tyd-btn-search',
                'value' => 'Suchen'
            ),
        ));
    }
}
