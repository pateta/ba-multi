<?php

namespace ToYouDo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Index controller.
 * 
 * @author Matias Thomsen
 * 
 */
class IndexController extends AbstractActionController
{
    const ROUTE_LOGIN        = 'zfcuser/login';
    
    /**
     * ToYouDo - Main page.
     * Overview with shortcuts to the entire application.
     * 
     * @return ViewModel
     */
    public function homeAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        
        return new ViewModel(array(
            
        ));
    }
    
    /**
     * ToYouDo - Page with a short description about the project.
     * 
     * @return ViewModel
     */
    public function projectAction()
    {
        // Redirect unauthenticated users to login
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        
        return new ViewModel(array(
            
        ));
    }
}
