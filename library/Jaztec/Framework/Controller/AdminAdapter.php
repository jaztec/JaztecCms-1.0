<?php
require_once 'Jaztec/Framework/Controller/Action.php';
require_once 'Jaztec/Framework/Controller/Helper/ModuleLoader.php';
/**
 * Controller class for module controllers to be used
 * in the Administration Central<br>
 * Note that all Admin controllers should implement an ajax
 * response interface
 * 
 * @author Jasper van Herpt
 * @version 1.1 - Disabled view helpers<br>
 * 			1.0
 */
class Jaztec_Framework_Controller_AdminAdapter extends Jaztec_Framework_Controller_Action {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct($request, $response, $invokeArgs);
		// Add the moduleloader helper to the broker
		Zend_Controller_Action_HelperBroker::addHelper(new Jaztec_Framework_Controller_Helper_ModuleLoader());
		
		// Disable rendering for ajax purposes
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
}