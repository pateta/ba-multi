<?php

namespace ToYouDo\Form;


use Zend\InputFilter\InputFilter;

/**
 * Description of ItemFormFilter
 *
 * @author Matias Thomsen
 */
class ItemFormFilter extends InputFilter {
    public function __construct()
    {
        $this->add(array(
            'name'       => 'content',
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
                        'max'      => 255,
                    ),
                ),
            ),
        ));
    }
}
