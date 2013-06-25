<?php

class Application_Model_PageNavigation
{
    protected $_pageTable;
    protected $_pageTableClass = 'Application_Model_DbTable_PageSetup';
    protected $_sectionTable;
    protected $_sectionTableClass = 'Application_Model_DbTable_Sections';

    const USE_CACHE_TRUE	=	true;
    const USE_CACHE_FALSE	=	false;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->setTable(array('page'	=> $this->_pageTableClass,
                               'section'	=> $this->_sectionTableClass));
    }

    /**
     * Initializes a database table
     *
     * @param  string                 $dbTable
     * @throws Exception
     * @return Zend_Db_Table_Abstract
     */
    protected function _initTable($dbTable)
    {
        // Create a new object
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        // Check if the object is of the right type
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Bad table class name received');
        }

        return $dbTable;
    }

    /**
     * Set the database tables: identifier => string
     *
     * name value pairs:
     * 'page'		=> string
     * 'section'	=> string
     *
     * @param array $options
     */
    protected function setTable($options)
    {
        foreach ($options as $key => $value) {
            $tblName = '_' . $key . 'Table';
            $this->$tblName = $this->_initTable($value);
        }

        return $this;
    }

    /**
     * Get the dbTable
     *
     * @return Zend_Db_Table_Abstract
     */
    protected function _getPageTable()
    {
        return $this->_pageTable;
    }

    /**
     * Get the dbTable
     *
     * @return Zend_Db_Table_Abstract
     */
    protected function _getSectionTable()
    {
        return $this->_sectionTable;
    }

    /**
     * Get array with page meta data with a numeric id
     *
     * @param  int       $pageId
     * @throws Exception
     * @return array
     */
    public function findPageById($pageId)
    {
        $row = $this->_getPageTable()->find($pageId);
        if (!$row) {
            throw new Exception('No page found with id ' . $pageId);
        }

        return $row->toArray();
    }

    /**
     * Returns array with page meta data with a textual identifier
     *
     * @param  string    $pageAlias
     * @throws Exception
     * @return array
     */
    public function getPageByAlias($pageAlias)
    {
        if (!is_string($pageAlias)) {
            throw new Exception('please input a string value');
        }
        $row = $this->_getPageTable()->fetchRow("alias = '" . $pageAlias . "'");
        if (!$row) {
            throw new Exception('No page found with alias ' . $pageAlias);
        }

        return $row->toArray();
    }

    /**
     * Return an Array object with all page data and links
     *
     * @param  bool            $useCache [OPTIONAL]
     * @return Zend_Navigation
     */
    public function getNavigation($useCache = self::USE_CACHE_TRUE)
    {
        if (self::USE_CACHE_TRUE === $useCache) {

            $cache = $this->_getCacheVar('site');

            if (!$container = $cache->load('sitenav')) {

                $container = $this->getNavigation(self::USE_CACHE_FALSE);
                $cache->save($container, 'sitenav');

            }

            return $container;
        }

        return $this->_buildNavigationChain();
    }

    /**
     *
     * Enter description here ...
     * @throws Exception
     * @return
     */
    protected function _buildNavigationChain()
    {
        $returnArr = Array();

        // Get all the pages from the database
        $pages = $this->_getPageTable()->fetchAll(null, 'sortorder ASC');

        if (!$pages) {
            throw new Exception("Geen pagina's gevonden!");
        }

        // Iterate the result and start checking the database for containing sections
        // if it is a container page
        foreach ($pages as $page) {
            $children = Array();
            if ($page->isContainer) {
                $children = $this->_getSectionTable()->getSectionArray($page->id, $page->alias, 'section');
            }
            switch ($page->handleMethod) {
                case 'action':
                    $returnArr[] = Array(
                                    'label'		=> $page->caption,
                                    'action'	=> $page->alias,
                                    'controller'=> $page->controller,
                                    'route'		=> $page->route,
                                    'pages'		=> $children);
                    break;
                case 'section':
                    $returnArr[] = Array(
                                    'label'		=> $page->caption,
                                    'action'	=> 'artikelen',
                                    'controller'=> $page->controller,
                                    'route'		=> $page->route,
                                    'pages'		=> $children,
                                    'params'	=> array(
                                                            'section'	=> $page->alias
                                                   )
                                    );
                    break;
                default:
                        break;
            }
        }

        $defReturn = array(array(
                            'label'		=> 'Home',
                            'action'	=> 'index',
                            'controller'=> 'index',
                            'module'	=> 'default',
                            'route'		=> 'action',
                            'pages'		=> $returnArr
        ));

        return $defReturn;
    }

    /**
     *
     *
     * @param  string     $cacheName
     * @return Zend_Cache
     */
    protected function _getCacheVar()
    {
        return Zend_Registry::get('Zend_Cache');
    }

    //protected function _initCache() {
    //	// Get the cache resource
    //	$this->bootstrap('cachemanager');
    //	$cacheManager = $this->getResource('cachemanager');
    //
    //	Zend_Controller_Front::getInstance()->get
    //}

}
