<?php
require_once 'Jaztec/Framework/Log.php';
/**
 * Class originally written by Matthijs van den Bos<br>
 * Extended for compatibility with Jaztec Framework
 *
 * @link http://blog.vandenbos.org/2009/07/07/zend-framework-module-config/
 * @author Jasper van Herpt<br>
 * 		   Matthijs van den Bos
 * @version <b>1.1</b><br>
 * 			1.0 - Implementing original class<br>
 * 			1.1 - Build in logging function
 */
class Jaztec_Framework_Resource_Modulesetup extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Holds the internal logging object
     *
     * @var Jaztec_Framework_Log
     */
    protected $_logger;

    public function init()
    {
        $this->_logger = Jaztec_Framework_Log::getInstance();
        $this->_logger->info(get_class() . ' started');
        try {
            $this->_getModuleSetup();
        } catch (Exception $e) {
            $this->_logger->err($e);
        }
    }

    /**
     * Load the module's ini files
     *
     * @return void
     * @throws Zend_Application_Exception
     */
    protected function _getModuleSetup()
    {
        $bootstrap = $this->getBootstrap();
        if (!($bootstrap instanceof Zend_Application_Bootstrap_Bootstrap)) {
            throw new Zend_Application_Exception('Invalid bootstrap class');
        }

        // Get the froncontroller and load the modules as array keys
        $front = $bootstrap->getResource('frontController');
        $modules = $front->getControllerDirectory();

        // Loop the array keys
        foreach (array_keys($modules) as $module) {
            // Ignore default module, it needs to be setup in the application config
            if ($module !== 'default') {
                $configPath  = $front->getModuleDirectory($module)
                             . DIRECTORY_SEPARATOR . 'configs';
                if (file_exists($configPath)) {
                    $cfgdir = new DirectoryIterator($configPath);
                    $appOptions = $this->getBootstrap()->getOptions();
                    // Loop through all the files
                    foreach ($cfgdir as $file) {
                        if ($file->isFile()) {
                            $filename = $file->getFilename();
                            try {
                                $options = $this->_loadOptions($configPath . DIRECTORY_SEPARATOR . $filename);
                            } catch (Exception $e) {
                                $this->_logger->warn(get_class($e) . ': ' . $e->getMessage());
                                continue;
                            }
                            if (($len = strpos($filename, '.')) !== false) {
                                $cfgtype = substr($filename, 0, $len);
                            } else {
                                $cfgtype = $filename;
                            }

                            if (strtolower($cfgtype) == 'module') {
                                if (array_key_exists($module, $appOptions)) {
                                    if (is_array($appOptions[$module])) {
                                        $appOptions[$module] =
                                            array_merge($appOptions[$module], $options);
                                    } else {
                                        $appOptions[$module] = $options;
                                    }
                                } else {
                                    $appOptions[$module] = $options;
                                }
                            } else {
                                $appOptions[$module]['resources'][$cfgtype] = $options;
                            }
                        }
                    }
                    $this->getBootstrap()->setOptions($appOptions);
                } else {
                    continue;
                }
            } else {
                continue;
            }
        }
    }

    /**
     * Load the config file
     *
     * @param  string                                $fullpath
     * @throws Zend_Config_Exception
     * @throws Jaztec_Framework_Resource_Exception::
     * @return array
     */
    protected function _loadOptions($fullpath)
    {
        if (file_exists($fullpath)) {
            switch (substr(trim(strtolower($fullpath)), -3)) {
                case 'ini':
                    $cfg = new Zend_Config_Ini($fullpath, $this->getBootstrap()
                                                    ->getEnvironment());
                    break;
                case 'xml':
                    $cfg = new Zend_Config_Xml($fullpath, $this->getBootstrap()
                                                    ->getEnvironment());
                    break;
                default:
                    $this->_logger->crit('Invalid config file, please use a .ini or .xml format');
                    throw new Zend_Config_Exception('Invalid config file, please use a .ini or .xml format');
                    break;
            }
        } else {
            $this->_logger->crit('File does not exist');
            throw new Jaztec_Framework_Resource_Exception('File does not exist');
        }

        return $cfg->toArray();
    }
}
