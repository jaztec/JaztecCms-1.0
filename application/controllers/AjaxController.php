<?php

/**
 * AjaxController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class AjaxController extends Zend_Controller_Action {
	
	public function init() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function indexAction() {
		// TODO Redirect back to indexcontroller
	}
	
	public function getarticlesAction() {
		$db = new Application_Model_DbTable_Articles();
		$result = $db->getArticleRangeDesc();
		echo Zend_Json::encode($result);
	}

}
