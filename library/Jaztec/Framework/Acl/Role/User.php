<?php

class Jaztec_Framework_Acl_Role_User extends Jaztec_Framework_Acl_Role {
	
	/**
	 * @return void
	 */
	public function __construct() {
		parent::__construct('user');
	}
}