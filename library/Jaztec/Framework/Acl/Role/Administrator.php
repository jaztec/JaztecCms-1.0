<?php

class Jaztec_Framework_Acl_Role_Administrator extends Jaztec_Framework_Acl_Role {
	
	/**
	 * @return void
	 */
	public function __construct() {
		parent::__construct('administrator');
	}
}