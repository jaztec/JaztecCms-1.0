<?php
require_once 'Jaztec/Framework/Log.php';

class Jaztec_Framework_View_Helper_AdminNavigation
{
    public function __construct()
    {
        // TODO escape this debug line
        Jaztec_Framework_Log::getInstance()->info(get_class() . ': Building AdminNavigation');
    }

    public function adminNavigation()
    {
        $menu = '<div class="menu">';
        $menu .= $this->_insertStandardMenu();
        $menu .= $this->_insertModulesMenu();
        $menu .= '</div>';

        return $menu;
    }

    /**
     * Add regular menu functionallity
     */
    protected function _insertStandardMenu()
    {
        $menu .= '<ul>';
        $menu .= '<li><a href="/">Home</a></li>';
        $menu .= '<li><a href="/access/logout">Logout</a></li>';
        $menu .= '</ul>';

        return $menu;
    }

    /**
     * Loads the modules
     */
    protected function _insertModulesMenu()
    {
        $menu .= '<ul>';
        $modules = $this->_loadModules();
        foreach ($modules as $module) {
            $menu .= '<li><a href="">' . $module . '</a></li>';
        }
        $menu .= '</ul>';

        return $menu;
    }

    /**
     * Loads the modules
     *
     * @return array
     */
    protected function _loadModules()
    {
        $front = Zend_Controller_Front::getInstance();
        $options = $front->getControllerDirectory();
        $modules = array();
        foreach (array_keys($options) as $module) {
            if ($module != 'default') {
                $modules[] = ucfirst($module);
            }
        }

        return $modules;
    }
}
