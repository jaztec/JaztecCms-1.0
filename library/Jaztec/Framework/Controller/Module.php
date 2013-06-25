<?php
require_once 'Jaztec/Framework/Controller/Action.php';

/**
 * Controller class for module controllers
 * 
 * @author Jasper van Herpt
 * @version 1.1 - Override constructor method<br>
 * 			1.0
 */
class Jaztec_Framework_Controller_Module extends Jaztec_Framework_Controller_Action {

	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct($request, $response, $invokeArgs);
	}
}