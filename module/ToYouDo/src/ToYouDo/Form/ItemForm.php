<?php

namespace ToYouDo\Form;


use Zend\Form\Form;

/**
 * Description of ItemForm
 * 
 * @author Matias Thomsen
 */
class ItemForm extends Form {
    
    public function __construct($name = null)
    {
        parent::__construct('item');

        $this->add(array(
            'name' => 'content',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Aufgabe'
            ),
            'attributes' => array(
                'id' => 'tyd-text-name',
                'placeholder' => 'Beschreiben Sie die Aufgabe',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type'  => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'class' => 'btn btn-warning btn-block',
                'id' => 'tyd-btn-add',
                'value' => 'Hinzufügen'
            ),
        ));
    }
}
