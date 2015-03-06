<?php
return array(
    // Routes
    'router' => array(
        'routes' => array(
            /*
             *  This route overrides the home route provided by ZFSkeleton
             *  Application module
             */
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'ToYouDo\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'toyoudo' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller'=> 'ToYouDo\Controller\Index',
                        'action'    => 'home',
                    ),
                ),
            ),
            'project' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/project[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller'=> 'ToYouDo\Controller\Index',
                        'action'    => 'project',
                    ),
                ),
            ),
            'friend' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/friend[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller'=> 'ToYouDo\Controller\Friend',
                        'action'    => 'index',
                    ),
                ),
            ),
            'list' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/list[/:action][/:id][/:secid]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'secid' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller'=> 'ToYouDo\Controller\List',
                        'action'    => 'index',
                    ),
                ),
            ),
            'toyoudo-list' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/toyoudo-list[/:action][/:id][/:secid]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'secid' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller'=> 'ToYouDo\Controller\Toyoudo',
                        'action'    => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ToYouDo\Controller\Friend '=> 'ToYouDo\Controller\FriendController',
            'ToYouDo\Controller\List' => 'ToYouDo\Controller\ListController',
            'ToYouDo\Controller\Toyoudo' => 'ToYouDo\Controller\ToyoudoController',
            'ToYouDo\Controller\Index' => 'ToYouDo\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        'zfcuser' => __DIR__ . '/../view',
        ),
    ),
);
