<?php
require_once 'Zend/Controller/Action.php';

require_once 'Jaztec/Framework/Log.php';

/**
 * Defines multiple attributes which should be set in
 * the deriving controller's init() function.
 *
 * @author Jasper van Herpt
 * @version 1.0
 */
class Jaztec_Framework_Controller_Action extends Zend_Controller_Action
{
    /**
     * Log object
     *
     * @var Jaztec_Framework_Log
     */
    protected $_logger = null;

    /**
     * @var string Title to be used in the Administration Central
     */
    protected $_title = "";

    /**
     * @var string Label to be used in the Administration Central
     */
    protected $_label = "";

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->_logger = Jaztec_Framework_Log::getInstance();
        parent::__construct($request, $response, $invokeArgs);
        // TODO escape this debug line
        $this->_logger->info(get_class() . ': Action started');
        // Enable jQuery
        // $this->_helper->jQuery->enable;
    }

    /*
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param  array                                 $options
     * @throws Jaztec_Framework_Controller_Exception
     * @return void
     */
    protected function _setOptions($options)
    {
        if (is_array($options)) {
            foreach ($options as $option => $value) {
                // TODO Reconfigure to utilize setter functions
                switch (ucfirst($option)) {
                    case 'Label':
                        $this->_label = is_string($value) ? $value : "";
                        break;
                    case 'Title':
                        $this->_title = is_string($value) ? $value : "";
                        break;
                    default:
                        break;
                }
            }
        } else {
            throw new Jaztec_Framework_Controller_Exception("Did not receive an array as parameter");
        }
    }
}
