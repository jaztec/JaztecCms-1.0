<?php
require_once 'Zend/Form/Decorator/Abstract.php';

/**
 * Decorator for password containing textboxes
 * 
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */
class Jaztec_Framework_Form_Decorator_Password extends Zend_Form_Decorator_Abstract {
	
	/**
	 * @var Jaztec_Framework_Log
	 */
	protected $_logger = null;
	
	public function __construct($options = null) {
		$this->_logger = Jaztec_Framework_Log::getInstance();
		parent::__construct($options);
	}
	
	public function render($content) {
		$element = $this->getElement();
		if(!$element instanceof Zend_Form_Element_Password) {
			$this->_logger->debug('The element failed as instance of Zend_Form_Element_Password');
			return $content;
		}
		$html = '<div class="form-row">';
		$html .= '<div class="form-property"><b>' . $element->getLabel() . '</b></div>';
		$html .= '<div class="form-value"><input type="password" name="' . $element->getName() . '" class="text" /></div>';
		$html .= '<div class="clearer"></div></div>';
		return $html;
	}
}