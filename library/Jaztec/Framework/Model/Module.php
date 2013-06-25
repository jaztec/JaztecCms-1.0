<?php

/**
 * Model to represent a Module for the application
 *
 * @author Jasper van Herpt
 * @version 1.0
 */
class Jaztec_Framework_Model_Module
{
    /**
     * @var string $_title
     */
    protected $_title = "";

    /**
     * @var string $_label
     */
    protected $_label = "";

    /**
     * @param  string                        $title
     * @return Jaztec_Framework_Model_Module
     */
    public function setTitle($title)
    {
        $this->_title = (string) $title;

        return $this;
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }
    /**
     * @param  string                        $label
     * @return Jaztec_Framework_Model_Module
     */
    public function setLabel($label)
    {
        $this->_label = (string) $label;

        return $this;
    }
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }
}
