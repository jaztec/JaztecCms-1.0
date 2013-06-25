<?php
require_once('Jaztec/Framework/Controller/Module.php');
/**
 * Class which will load the Ext JS javascript files
 * 
 * @author Jasper van Herpt
 * @version 1.0
 */
class Jaztec_Framework_Controller_ExtjsApp extends Jaztec_Framework_Controller_Module {
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct($request, $response, $invokeArgs);
		
		// Add Ext js lib to page
		$this->view->headScript()
                				->offsetSetFile(1,'/jazteccms/js/extjs/ext-all.js');
		$this->view->headLink()->appendStylesheet('/jazteccms/js/extjs/resources/css/ext-all.css');
	}
}