<?php
 return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Startseite',
                'route' => 'home',
            ),
            array(
                'label' => 'Über das Projekt',
                'route' => 'project',
            ),
            array(
                'label' => 'ToYouDo',
                'route' => 'toyoudo-list',
            ),
            array(
                'label' => 'Listen',
                'route' => 'list',
            ),
            array(
                'label' => 'Freunde',
                'route' => 'friend',
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
);
