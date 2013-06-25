<?php
require_once('Jaztec/Framework/Controller/Exception.php');
require_once('Jaztec/Framework/Log.php');
require_once('Jaztec/Framework/Site/DbTable/Requests.php');

class Jaztec_Framework_Controller_Plugin_RequestLogger extends Zend_Controller_Plugin_Abstract {
    
    /**
     *@var array
     */
    protected $_settings = array();
    
    /**
     *@var Zend_Db_Table
     */
    protected $_dbTable = null;
    
    /**
     * @var Jaztec_Framework_Log 
     */
    protected $_logger = null;
    
    /**
     * Constructor
     * 
     * @param array|Zend_Config $options
     */
    public function __construct($options = array()){
        if($options instanceof Zend_Config) 
            $options->toArray();
        
        $this->setOptions($options);
        
        $this->_logger = Jaztec_Framework_Log::getInstance();
    }
    
    /**
     * @param array $options 
     */
    public function setOptions($options) {
        if(!is_array($options))
            throw new Jaztec_Framework_Controller_Exception('$options is geen array.');
        
        foreach($options as $name => $option)
            if(method_exists($this, $method =  '_set' . ucfirst($name)))
                $this->$method($option);

            $this->_settings = $options;
    }
    
    /**
     * @param string|Zend_Db_Table_Abstract $dbTable
     * @throws Jaztec_Framework_Controller_Exception 
     */
    protected function _setDbTable($dbTable) {
        if(is_string($dbTable))
            $dbTable = new $dbTable;
        
        if($dbTable instanceof Zend_Db_Table_Abstract)
            $this->_dbTable = $dbTable;
        else        
            throw new Jaztec_Framework_Controller_Exception('$dbTable is geen geldige Zend_Db_Table');
    }
    
    protected function _getDbTable() {
        if($this->_dbTable instanceof Jaztec_Framework_Site_DbTable_Requests)
            return $this->_dbTable;
        else
            $this->_logger->notice('Er is geen geldige dbTable ingeladen, het loggen van requests gaat niet lukken.');
    }
    
    /**
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        $string = '';
        
        // Haal de niet interessante verzoeken eruit
        if($request->getControllerName() === 'img')
            exit;
        if($request->getControllerName() === 'js')
            exit;
        
        foreach($request->getParams() as $name => $value){
            $string .= $name .': '. $value . '; ';
        }
        
        $now = getdate();
        $now = $now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.$now['hours'].':'.$now['minutes'].':'.$now['seconds'];
        
        
        $insert = array(
            'Url'           => $string,
            'LMT'           => $now,
            'RequestTypeID' => $request->getParam('_dc', false) ? 2 : 3
        );
        
        $this->_dbTable->insert($insert);
        
        parent::routeStartup($request);
    }
}