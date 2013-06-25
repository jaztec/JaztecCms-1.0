<?php
require_once 'Jaztec/Framework/Form/Decorator/Textbox.php';
require_once 'Jaztec/Framework/Form/Decorator/Password.php';
require_once 'Jaztec/Framework/Form/Decorator/Button.php';

/**
 *
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */
class Jaztec_Framework_Form_Login extends Zend_Form
{
    /**
     * Decorator array
     *
     * @var array
     */
    protected $_formDecorator = array(
                                    'FormElements',
                                    array('HtmlTag', array('tag' => 'fieldset')),
                                    'Form');

    /**
     * Decorator array
     *
     * @var array
     */
    protected $_textboxDecorator = array(
                                    'ViewHelper',
                                    'Label',
                                    'Errors',
                                        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-value')),
                                    array('Label', array('tag' => 'div', 'class' => 'form-property', 'placement' => 'prepend')),
                                    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row')));

    /**
     * Decorator array
     *
     * @var array
     */
    protected $_buttonDecorator = array(
                                    'ViewHelper',
                                    array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-value')),
                                    array(array('label' => 'HtmlTag'), array('tag' => 'div', 'class' => 'clearer', 'placement' => 'append')),
                                    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-row form-row-submit')),);

    public function init()
    {
        $textbox = new Jaztec_Framework_Form_Decorator_Textbox();
        $button = new Jaztec_Framework_Form_Decorator_Button();

        $this->setMethod('POST');

        $this->setDecorators($this->_formDecorator);

        $username = $this->createElement('text','username',array('label' 			=> 'Username',
                                                                 'required'			=> true));
        $username->addDecorator(new Jaztec_Framework_Form_Decorator_Textbox());
        $this->addElement($username);

        $password = $this->createElement('password', 'password', array('label' 		=> 'Password',
                                                      'required'	=> true));
        $password->addDecorator(new Jaztec_Framework_Form_Decorator_Password());
        $this->addElement($password);

        $button = $this->createElement('submit', 'submit', array('label' => 'Inloggen'));
        $button->addDecorator(new Jaztec_Framework_Form_Decorator_Button());
        $this->addElement($button);
    }
}
