<?php

namespace ToYouDo\Form;


use Zend\InputFilter\InputFilter;

/**
 * Description of FriendFormFilter
 *
 * @author Matias Thomsen
 */
class FriendFormFilter extends InputFilter {
    public function __construct()
    {
        $this->add(array(
            'name'       => 'search',
            'required'   => true,
        ));
    }
}
