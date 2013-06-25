<?php
require_once 'Jaztec/Framework/Log.php';
/**
 * Class original from Matthijs van den Bos<br>
 * Extended for compatibility with Jaztec Framework
 * 
 * @link http://blog.vandenbos.org/2009/07/07/zend-framework-module-config/ 
 * @author Matthijs van den Bos<br>
 * 		   Jasper van Herpt
 * @version <b>1.1</b><br>
 * 			1.0 - Implementing original class<br>
 * 			1.1 - Build in logging function
 */
class Jaztec_Framework_Controller_Plugin_RequestedModuleLayoutLoader extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Internal options
	 * 
	 * @var array
	 */
	protected $_options;
	
	/**
	 * Internal logger
	 * 
	 * @var Jaztec_Framework_Log
	 */
	protected $_logger;
	
	public function __construct(array $options) {
		$this->_options = $options;
		$this->_logger = Jaztec_Framework_Log::getInstance();
		$this->_logger->info(get_class() . ' started');
	}
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$this->_logger->debug("Start " . get_class() . "'s preDispatch routine");
    	$config     = Zend_Controller_Front::getInstance()
                            ->getParam('bootstrap')->getOptions();
        $moduleName = $request->getModuleName();
        if (isset($config[$moduleName]['resources']['layout']['layout'])) {
            $layoutScript = $config[$moduleName]['resources']['layout']['layout'];
            Zend_Layout::getMvcInstance()->setLayout($layoutScript);
        } elseif(isset($config['resources']['layout']['layout'])) {
        	$layoutScript = $config['resources']['layout']['layout'];
            Zend_Layout::getMvcInstance()->setLayout($layoutScript);
        }
 
        if (isset($config[$moduleName]['resources']['layout']['layoutPath'])) {
            $layoutPath = $config[$moduleName]['resources']['layout']['layoutPath'];
            $moduleDir = Zend_Controller_Front::getInstance()->getModuleDirectory();
            Zend_Layout::getMvcInstance()->setLayoutPath(
                $moduleDir. DIRECTORY_SEPARATOR .$layoutPath
            );
        } elseif(isset($config['resources']['layout']['layoutPath'])) {
            $layoutPath = $config['resources']['layout']['layoutPath'];
            Zend_Layout::getMvcInstance()->setLayoutPath($layoutPath);
        }
    }
}