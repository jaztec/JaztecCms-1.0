<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initViewHelpers()
    {
        // Get view resource for jQuery
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // Add custom helpers
        $view->addHelperPath('Zend/View/Helper/Navigation', 'Zend_View_Helper_Navigation');
        $view->addHelperPath('ZendX/Jquery/View/Helper', 'ZendX_Jquery_View_Helper');
        $view->addHelperPath('Jaztec/View/Helper', 'Jaztec_View_Helper');
        $view->addHelperPath(APPLICATION_PATH . '/view/helpers/', 'Application_View_Helper');

        // Aim jQuery
        ZendX_JQuery::enableView($view);

        // Create new view renderer and add it to the help broker.
        // The earlier added voew helper paths will now be added to the helper broker too
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        // Get the layout resource view
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        // Set (all) pages' doctype
        $view->doctype('XHTML1_STRICT');
        $view->setEncoding('ISO-8859-1');

        // Set base jQuery elements
        $view->jQuery()->setLocalPath('/js/jquery/jquery-1.6.1.min.js')
                       ->setUiLocalPath('/js/jquery/jquery-ui-1.8.13.custom.min.js')
                          ->addStyleSheet('/js/jquery/themes/custom-theme/jquery-ui-1.8.13.custom.css');
    }

    protected function _initRoute()
    {
        // Retreive the default router and initiate the config file
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'bootstrap');

        // Setup the router and add the config to it
        $router->addConfig($config, 'routes');
    }

    protected function _initDatabase()
    {
        // Get database resource
        $this->bootstrap('db');
        $db = $this->getResource('db');

        // Setup default database adapter
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }

    protected function _initCache()
    {
        // Get the cache resource
        $cache = Zend_Cache::factory('File', 'File',
                                    array(	'lifetime'						=> 3600,
                                            'automatic_serialization'		=> true,
                                            'master_files'					=> array(APPLICATION_PATH . '/configs/application.ini'),
                                            'master_files_mode'				=> Zend_Cache_Frontend_File::MODE_OR,
                                            'ignore_missing_master_files'	=> false),
                                    array(	'cache_dir'					=> APPLICATION_PATH . '/../cache'),
                                    false,
                                    false,
                                    false
                                    );

        Zend_Registry::set('Zend_Cache', $cache);
    }

    protected function _initNavigation()
    {
        // Get view resource
        $this->bootstrap('layout');
        $view = $this->getResource('layout')->getView();

        // Get data from the model
        $pages = new Application_Model_PageNavigation();
        $nav = $pages->getNavigation();

        // Insert container into the Navigation object
        $view->navigation(new Zend_Navigation($nav));
    }
}
