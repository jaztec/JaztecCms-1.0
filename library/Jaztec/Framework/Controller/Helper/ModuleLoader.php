<?php
/**
 * Brings functionallity to load all registered
 * modules.
 *
 * @author Jasper van Herpt
 * @version 1.0
 */
class Jaztec_Framework_Controller_Helper_ModuleLoader extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var array
     */
    protected $_modules = null;

    /**
     * Loads the modules
     *
     * @return void
     */
    public function loadModules()
    {
        $front = Zend_Controller_Front::getInstance();
        $options = $front->getControllerDirectory();
        $modules = array();
        foreach (array_keys($options) as $module) {
            if ($module != 'default') {
                $modules[] = ucfirst($module);
            }
        }
        $this->_modules = $modules;
    }

    /**
     * Get array with modulenames
     *
     * @return array $modules;
     */
    public function getModules()
    {
        if ($this->_modules === null) {
            $this->loadModules();
        }

        return $this->_modules;
    }
}
