<?php
require_once 'Zend/Form/Decorator/Abstract.php';

/**
 * Decorator for buttons
 *
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */
class Jaztec_Framework_Form_Decorator_Button extends Zend_Form_Decorator_Abstract
{
    /**
     * @var Jaztec_Framework_Log
     */
    protected $_logger = null;

    public function __construct($options = null)
    {
        $this->_logger = Jaztec_Framework_Log::getInstance();
        parent::__construct($options);
    }

    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element_Submit) {
            $this->_logger->debug('The element failed as instance of Zend_Form_Element_Submit');

            return $content;
        }
        $html = '<div class="form-row form-row-submit">';
        $html .= '<div class="form-value"><input type="submit" class="button" value="' . $element->getLabel() . '" /></div>';
        $html .= '<div class="clearer"></div></div>';

        return $html;
    }
}
