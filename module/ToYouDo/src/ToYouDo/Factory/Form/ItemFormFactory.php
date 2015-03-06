<?php

namespace ToYouDo\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ToYouDo\Form\ItemFormFilter;
use ToYouDo\Form\ItemForm;

/**
 * Description of ItemFormFactory
 *
 * @author Matias Thomsen
 */
class ItemFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $inputFilter = new ItemFormFilter();
        
        $form = new ItemForm();
        $form->setInputFilter($inputFilter);


        return $form;
    }
}