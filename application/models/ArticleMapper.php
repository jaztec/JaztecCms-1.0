<?php

class Application_Model_ArticleMapper
{
	/**
	 * @param Zend_Db_Table_Abstract $_dbTable
	 */
	protected $_dbTable;
	/**
	 * @param string $_default_Table
	 */
	protected $_tableClass = 'Application_Model_DbTable_Articles';
	/**
	 * Constructor.
	 * 
	 * @return	void
	 */
	public function __construct($options = null) {
		$this->_setDbTable($this->_tableClass);
	}
	/**
	 * Set the database table
	 * 
	 * @param 	string 		$dbTable
	 * @throws	Exception
	 * @return	Application_Model_ArticleMapper
	 */
	protected function _setDbTable($dbTable) {
		// Create a new object
		if(is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		// Check if the object is of the right type
		if(!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Bad table class name received');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}
	/**
	 * Get the dbTable
	 * 
	 * @return Zend_Db_Table_Abstract
	 */
	protected function _getDbTable() {
		return $this->_dbTable;
	}
	/**
	 * Used to perform query's at the database, returns a Zend_Db_Table_Row_Abstract
	 * or a Zend_Db_Table_Rowset_Abstract, if none found it returns null.
	 * 
	 * @param 	string|array	$where	[OPTIONAL]
	 * @param 	string|array	$order	[OPTIONAL]
	 * @param 	int				$limit	[OPTIONAL]
	 * @param 	int				$offset	[OPTIONAL]
	 * @return 	Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract|null
	 */
	protected function _doQuery($where = null, $order = null, $limit = null, $offset = null) {
		$db = $this->_dbTable;
		// If limit is one the function needs to return a row, otherwise it needs to return
		// a rowset
		if(1 === $limit) {
			$method = 'fetchRow';
		} else {
			$method = 'fetchAll';
		}
		return $db->$method($where, $order, $limit, $offset);
	}
	/**
	 * Converts an array to an Application_Model_Article
	 * 
	 * @param 	array $array
	 * @return	Application_Model_Article
	 */
	protected function _convertArray($array) {
		return new Application_Model_Article(
					array(
						'id'					=> $array['id'],
						'title'					=> $array['title'],
						'description'			=> $array['desc'],
						'urlRewrite'			=> $array['urlrewrite'],
						'section'				=> $array['section'],
						'content'				=> $array['content'],
						'user'					=> $array['useradded'],
						'dateTimeAdded'			=> $array['datetimeadded'],
						'dateTimeLastModified'	=> $array['datetimelastmodified']
					)
		);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param 	int		$id
	 * @param	bool	$asArray	[OPTIONAL]
	 * @return	array|Application_Model_Article
	 */
	public function findById($id, $asArray = false) {
		$row = $this->_doQuery('id = ' . $id, null, 1);
		if(!$row){
			return array();
		}
		if(true === $asArray) {
			return $row->toArray();
		} else {
			return $this->_convertArray($row->toArray());
		}	
	}
	/**
	 * Find Article by url
	 * 
	 * @param	string	$url
	 * @param	bool	$asArray	[OPTIONAL]
	 * @return	array|Application_Model_Article
	 */
	public function findByUrl($url, $asArray = false) {
		$row = $this->_doQuery('urlrewrite = "' . $url . '"', null, 1);
		if(!$row){
			return array();
		}
		if(true === $asArray) {
			return $row->toArray();
		} else {
			return $this->_convertArray($row->toArray());
		}	
	}
	/**
	 * Find all articles for a section, returns a multidimensional array
	 * or an array of Application_Model_Article 's
	 * 
	 * @param	string	$section	String specifier to retrieve section
	 * @param	int		$limit		[OPTIONAL] 
	 * @param	int		$offset		[OPTIONAL] 
	 * @param	bool	$asArray	[OPTIONAL]
	 * @return	array|Application_Model_Article array
	 */
	public function findBySection($section, $limit = null, $offset = null, $asArray = false) {
		$rowset = $this->_doQuery('section = "' . $section . '"', 'datetimeadded DESC', $limit, $offset);
		if(!$rowset){
			return array();
		}
		if(true === $asArray) {
			return $rowset->toArray();
		} else {
			$return = array();
			foreach($rowset as $row) {
				$return[] = $this->_convertArray($row->toArray());
			}
			return $return;
		}
	}
	
	/**
	 * Find all articles from a section and its child sections
	 * 
	 * @param	string	$section
	 * @param	array	$options	[OPTIONAL]
	 * $return	Application_Model_Arcticle array
	 */
	public function findAllFromSection($section, $options = null) {
		$return = array();
		
		$rowset = $this->_doQuery('section = "' . $section . '"', 'datetimeadded DESC', null, null);
		foreach($rowset as $row) {
			$return[] = $this->_convertArray($row->toArray());
		}
		
		$sections = new Application_Model_DbTable_Sections();
		$children = $sections->getSectionChildren($section);
		
		foreach($children as $child){
			$rowset = $this->_doQuery('section = "' . $child->alias . '"', 'datetimeadded DESC', null, null);
			foreach($rowset as $row) {
				$return[] = $this->_convertArray($row->toArray());
			}
		}
		
		return $return;
	}
	
	/**
	 * Returns the article placed last on the front page
	 * 
	 * @param	bool	$asArray	[OPTIONAL]
	 * @return	array|Application_Model_Article
	 */
	public function findLastFrontArticle($asArray = false) {
		$row = $this->_doQuery('section = "voorpagina"', 'id DESC', 1);
		if(!$row){
			return array();
		}
		if(true === $asArray) {
			return $row->toArray();
		} else {
			return $this->_convertArray($row->toArray());
		}
	}
	/**
	 * Get all the articles from the database, sorted by creation date
	 * 
	 * @param	bool	$asArray [OPTIONAL]
	 * @return	array|Application_Model_Article array
	 */
	public function findAll($limit = null, $offset = null, $asArray = false) {
		$rowset = $this->_doQuery(null, 'datetimeadded DESC', $limit, $offset);
		if(!$rowset){
			return array();
		}
		if(true === $asArray) {
			return $rowset->toArray();
		} else {
			$return = array();
			foreach($rowset as $row) {
				$return[] = $this->_convertArray($row->toArray());
			}
			return $return;
		}
	}
}

