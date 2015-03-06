<?php

namespace ToYouDo\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ToYouDo\Form\ListFormFilter;
use ToYouDo\Form\ListForm;

/**
 * Description of ListFormFactory
 *
 * @author Matias Thomsen
 */
class ListFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $inputFilter = new ListFormFilter();
        
        $form = new ListForm();
        $form->setInputFilter($inputFilter);


        return $form;
    }
}