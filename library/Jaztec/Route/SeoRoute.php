<?php

class Jaztec_Route_SeoRoute implements Zend_Controller_Router_Route_Interface
{
    /**
     *
     * Enter description here ...
     */
    public function __construct()
    {
    }
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Router_Route_Interface::match()
     */
    public function match($path)
    {
    }
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Router_Route_Interface::assemble()
     */
    public function assemble()
    {
    }
    /**
     *
     * Enter description here ...
     * @param Zend_Config $config
     */
    public static function getInstance(Zend_Config $config)
    {
        return new Jaztec_Route_SeoRoute();
    }
}
