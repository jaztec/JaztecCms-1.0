<?php
require_once 'Zend/Db/Table/Abstract.php';
/**
 * Defines the users table
 * 
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */

class Jaztec_Framework_Site_DbTable_Requests extends Zend_Db_Table_Abstract {
	protected $_name = 'Site_Requests';
        protected $_primary = 'RequestID';
}