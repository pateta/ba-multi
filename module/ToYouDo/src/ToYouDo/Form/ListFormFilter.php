<?php

namespace ToYouDo\Form;


use Zend\InputFilter\InputFilter;

/**
 * Description of ListFormFilter
 *
 * @author Matias Thomsen
 */
class ListFormFilter extends InputFilter {
    public function __construct()
    {
        $this->add(array(
            'name'       => 'name',
            'required'   => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 5,
                        'max'      => 50,
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name'       => 'friendId',
            'required'   => true,
        ));
        
        $this->add(array(
            'name'       => 'deadline',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'Date',
                    'options' => array(
                        'format' => 'Y-m-d',
                    ),
                ),
            ),
        ));
    }
}
