<?php
require_once 'Zend/Log.php';
require_once 'Zend/Log/Filter/Priority.php';
require_once 'Zend/Log/Writer/Stream.php';
require_once 'Zend/Log/Formatter/Simple.php';
/**
 * Used for logging purposes
 * 
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 *  		1.0 - Setup the log and the needed writers
 */
class Jaztec_Framework_Log extends Zend_Log {
	
	/**
	 * Used to store the formatter
	 * 
	 * @var Zend_Log_Formatter_Simple
	 */
	protected $_format = '';
	
	/**
	 * Store the path of the error log stream
	 * 
	 * @var string
	 */
	protected $_errorLogStream = '';
	
	/**
	 * Store the path of the system log stream
	 * 
	 * @var string
	 */
	protected $_systemLogStream = '';
	
	/**
	 * Store the path of the debug log stream
	 * 
	 * @var string
	 */
	protected $_debugLogStream = '';
	
	/**
	 * Initiate a singleton so only one logger can be present per request
	 * 
	 *@var Jaztec_Framework_Log
	 */
	protected static $_instance = null;
	
	/**
	 * Setup a writer object and load the parent with variables and writers
	 */
	public function __construct(){
		// Construct its parent
		parent::__construct();
		// Make the format in which it needs to be written
		$format = '%timestamp% (%priority%) %priorityName%: %message%' . PHP_EOL;
		$this->_format = new Zend_Log_Formatter_Simple($format);
		// Fill the internal stream variables with string pointers
		$this->_errorLogStream = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'logs/error.log';
		$this->_systemLogStream = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'logs/system.log';
		$this->_debugLogStream = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'logs/debug.log';
		// Add the needed writers
		$this->addWriter($this->_errorWriter());
		if(APPLICATION_ENV === 'development') {
			$this->addWriter($this->_debugWriter());
		}
		$this->addWriter($this->_systemWriter());
		// Broadcast the log startup
		$this->info('*******************Start Logging*******************');
	}
	
	/**
	 * Make an end logging log entry
	 * 
	 * @see Zend_Log::__destruct()
	 */
	public function __destruct() {
		$this->info('*******************Stop Logging*******************');
	}
	
	/**
	 * Returns the singleton instance
	 *
	 *@return Jaztec_Framework_Log
	 */
	public static function getInstance() {
		if(null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 
	 * @return Zend_Log_Writer_Stream
	 */
	protected function _errorWriter() {
		// Make a writer object
		$writer = new Zend_Log_Writer_Stream($this->_errorLogStream);
		// Add a relevant filter to the writer
		$writer->addFilter(new Zend_Log_Filter_Priority(Zend_Log::WARN));
		$writer->setFormatter($this->_format);
		return $writer;
	}
	
	/**
	 * 
	 * @return Zend_Log_Writer_Stream
	 */
	protected function _debugWriter() {
		// Make a writer object
		$writer = new Zend_Log_Writer_Stream($this->_debugLogStream);
		// Add a relevant filter to the writer
		$writer->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG));
		$writer->setFormatter($this->_format);
		return $writer;
	}
	
	/**
	 * 
	 * @return Zend_Log_Writer_Stream
	 */
	protected function _systemWriter() {
		// Make a writer object
		$writer = new Zend_Log_Writer_Stream($this->_systemLogStream);
		// Add a relevant filter to the writer
		$writer->addFilter(new Zend_Log_Filter_Priority(Zend_Log::INFO));
		$writer->setFormatter($this->_format);
		return $writer;
	}
}