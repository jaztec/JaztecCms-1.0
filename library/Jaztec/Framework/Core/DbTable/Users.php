<?php
require_once 'Zend/Db/Table/Abstract.php';
/**
 * Defines the users table
 * 
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */

class Jaztec_Framework_Core_DbTable_Users extends Zend_Db_Table_Abstract {
	protected $_name = 'cor_users';
        protected $_primary = 'id';
}