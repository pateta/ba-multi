<?php

namespace ToYouDo\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ToYouDo\Form\FriendFormFilter;
use ToYouDo\Form\FriendForm;

/**
 * Description of FriendFormFactory
 *
 * @author Matias Thomsen
 */
class FriendFormFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $inputFilter = new FriendFormFilter();

        $form = new FriendForm();
        $form->setInputFilter($inputFilter);

        return $form;
    }
}