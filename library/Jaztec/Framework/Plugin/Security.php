<?php
/**
 * Standard pluging to regulate authentication
 * and identification
 * 
 * @version 1.0
 */

class Jaztec_Framework_Plugin_Security extends Zend_Controller_Plugin_Abstract {
	
	
	/**
	 * Specify the initial settings for this plugin
	 * 
	 * @var unsigned char
	 */
	protected $_settings		= null;
	
	/**
	 * Specify the table used to identify
	 * users
	 * 
	 * @var string
	 */
	protected $_securityTable	= null;
	
	/**
	 * Column holding the identity of the user
	 * 
	 * @var string
	 */
	protected $_securityUser	= null;
	
	/**
	 * Column holding the password of the user
	 * 
	 * @var string
	 */
	protected $_securityPass	= null;
	
	/**
	 * Instance of Zend_Auth to perform actions on
	 * 
	 * @var Zend_Auth
	 */
	protected $_auth			= null;
	
	/**
	 * @var string
	 */
	protected $_TREATMENT		= 'MD5(CONCAT(?,signature))';
	
	/**
	 * Setup the security plugin with its needs, it also 
	 * dynamicly loads a Zend_Acl according to the requested
	 * module
	 * 
	 * @param	array|Zend_Config $options
	 * @throws	Jaztec_Plugin_Exception
	 * @return	void
	 */
	public function __construct() {
		$this->_auth = Zend_Auth::getInstance();
		$this->_setDb();
		
		/**
		 * Start indexing all the roles known in the database
		 * as well as the resources connected to the current
		 * module
		 */
		$roles = Jaztec_Framework_Acl_DbTable_Roles::fetchAll();
		
	}
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$user = $this->_getUser();
	}
		
	/**
	 * @throws	Jaztec_Plugin_Exception
	 * @return	bool
	 */
	protected function _setDb() {
		$this->_securityPass = 'password';
		$this->_securityUser = 'username';
		$this->_securityTable = 'users';
		
		return true;
	}
	
	/**
	 * Takes an array with values and checks it internally against 
	 * the loaded values
	 * 
	 * @param array $values
	 * @return bool
	 */
	protected function _checkIdentity($values) {
		$adapter = $this->_getAuthAdapter();
		$adapter->setIdentity($values['username']);
		$adapter->setCredential($values['password']);
		
		$result = $this->_auth->authenticate($adapter);
		if($result->isValid()) {
			Zend_Auth::getInstance()->getStorage()->write(serialize($adapter->getResultRowObject()));
			return true;
		}
		return false;
	}
	
	
	
	/**
	 * Get the Authitenticate database table, makes a little check whether a database is loaded
	 * 
	 * @return Zend_Auth_Adapter_DbTable
	 */
	protected function _getAuthAdapter() {
		
		$adapter = new Zend_Auth_Adapter_DbTable();
		
		$adapter->setTableName($this->_securityTable)
			->setIdentityColumn($this->_securityUser)
			->setCredentialColumn($this->_securityPass)
			->setCredentialTreatment($this->_TREATMENT);
				
		return $adapter;
		
	}
	
	/**
	 * Returns an user, if one is stored it returns that one
	 * otherwise it defaults to guest. 
	 * 
	 * @return Jaztec_Framework_User $user
	 */
	protected function _getUser() {
		if($this->_auth->hasIdentity()) {
			$options = unserialize($this->_auth->getStorage()->read());
			$user = new Jaztec_Framework_User($options);
		} else {
			$options = array('id'			=> 0,
							 'username'		=> 'Guest',
							 'password'		=> '',
							 'email'		=> '',
							 'signature'	=> '',
							 'group'		=> 1,
							 'role'			=> 1
			);
			$user = new Jaztec_Framework_User($options);
		}
		return $user;
	}
	
	/**
	 * @param	string $function
	 * @param	string $message
	 * @throws	Jaztec_Plugin_Exception
	 */
	protected function _throwException($function, $message) {
		throw new Jaztec_Plugin_Exception(__CLASS__ . ': ' . $function . ' => ' . $message);
	}
}