<?php

class Application_Form_Contact extends ZendX_JQuery_Form
{

    public function init()
    {
    	// Set method to POST
    	$this->setMethod('post');
    	
    	// Add the name inputbox 
        $this->addElement('text',
        				  'name',
        				  array(
						  		'required'	 => true,
        				  		'filters'	 => array('StringTrim'),
        				  		'label'		 => 'Naam'        				  
        				  ));
        				  
    	// Add the email address inputbox 
        $this->addElement('text',
        				  'emailaddress',
        				  array('validators' => array('emailAddress'),
						  		'required'	 => true,
        				  		'filters'	 => array('StringTrim'),
        				  		'label'		 => 'Emailadres'        				  
        				  ));
        				  
        // Add the message area 
        $this->addElement('textarea',
        				  'message',
        				  array('required'	 => true,
        				  		'label'		 => 'Bericht'    ,
        				  		'cols'		 => 40    				  
        				  ));
        				  
		// Add a captcha element to prevent spamming
        //$this->addElement('captcha',
        //				  'captcha',
        //				  array('required'	 => true,
        //				  		'label'		 => 'Typ a.u.b. de volgende 5 letters over',
        //				  		'captcha'	 => array('captcha'	=> 'Figlet',
        //				  							  'wordlen'	=> 5,
        //				  							  'timeout'	=> 600
        //				  		)        				  
        //				  ));
        				  
    	// Add the submit button 
        $this->addElement('submit',
        				  'submit',
        				  array('required'	 => true,
        				  		'label'		 => 'Verstuur'        				  
        				  ));
        				  
		// And finally some CSRF protection 
        $this->addElement('hash',
        				  'csrf',
        				  array('ignore'	 => true      				  
        				  ));
    }


}

