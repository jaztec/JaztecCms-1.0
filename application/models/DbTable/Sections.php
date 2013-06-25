<?php

class Application_Model_DbTable_Sections extends Zend_Db_Table_Abstract
{

    protected $_name = 's_sections';

    /**
     * Returns an array with section names
     * 
     * @param	string	$section
     * @return	array	$result
     */
    public function getSectionChildren($section){
    	$section = $this->fetchAll('alias = "' . $section . '"');
    	if($section) {
    		foreach($section as $sec) {
    			$sectionid = $sec->id;
    		}
    	} else {
    		return null;	
    	}

    	return $this->fetchAll('parentid = ' . $sectionid);
    }
    
    /**
     * 
     * Enter description here ...
     * @param string $section
     * @return Application_Model_Section
     */
    public function getSectionByAlias($section) {
    	$return = new Application_Model_Section();
    	
    	$requestedSection = $this->fetchAll('alias = "' . $section . '"');
    	
    	unset($section);
    	
    	if($requestedSection) {
    		foreach ($requestedSection as $section) {
    			$return->setOptions(array(
    				'id'			=>$section->id,
    				'name'			=>$section->name,
    				'description'	=>$section->desc,
    				'parentId'		=>$section->parentid,
    				'container'		=>$section->container,
    				'alias'			=>$section->alias,
    				'isPage'		=>$section->isPage
    			));
    		}
    	}
    	
    	return $return;
    }
    /**
     * Returns html code for list items, builds child sections
     * accordingly. List properties to be set externally
     * 
     * @deprecated 	Links in list are bad, use getSectionArray() 
     * 				instead
     * @return 		String Html code
     */
	public function getSectionTree() {
		$result = $this->fetchAll();
		$returnVal = '';
		foreach($result as $row) {
			if($row->parentid == 0){
				$returnVal .= '<li><a href="' . $row->name . '">' . $row->name . '</a>';
				$childResult = $this->fetchAll('parentid = ' . $row->id);
				if($childResult) {
					$returnVal .= '<ul>';
					foreach($childResult as $childRow) {
						$returnVal .= '<li><a href="">' . $childRow->name . '</a></li>';
					}
					$returnVal .= '</ul>';
				}
				$returnVal .= '</li>';
			}
		}
		return $returnVal;
	}
	/**
	 * Get a nested array of the sections and their children, also all
	 * the articles are inserted.
	 * 
	 * @param 	int 		$container Page alias of the container page 
	 * @param 	string		$action Specifies the action needed in the url
	 * @param 	string 		$route Specifies the needed route
	 * @param 	string 		$controller Specifies the controller in the url, default = 'index'
	 * @param 	string 		$module Specifies the module, default = 'default'
	 * @return	array	 	containing sections and children
	 */
	public function getSectionArray($container = 1, $action = 'index', $route = 'default', $controller = 'index', $module = 'default') {
		$returnArr = array();
		$container = (int) $container;
		$result = $this->fetchAll('container = ' . $container . ' AND NOT parentid <> 0');
		if($result) {
			foreach($result as $row) {
					$childResult = $this->fetchAll('parentid = ' . $row->id);
					$childPages = array();
					
					if($childResult) {
						foreach($childResult as $child) {
							$childPages[] = array(
													'label'		=> $child->name,
													'controller'=> $controller,
													'action'	=> $action,
													'module'	=> $module,
													'route'		=> $route,
													'params'	=> array(
																			'section'	=> $child->alias),
													'pages'		=> $this->_getArticlesForSection($child->alias, $action, 'article'),
													'title'		=> $child->name);
						}
					}
					
					// Check if this section has any articles attached to it
					foreach($this->_getArticlesForSection($row->alias, 'artikelen', 'article') as $child) {
						$childPages[] = $child;
					}
					
					if(1 == $row->isPage) {
						foreach ($childPages as $child) {
							$returnArr[] = $child;
						}
					} else {
						$returnArr[] = array(
											'label'		=> $row->name,
											'controller'=> $controller,
											'action'	=> $action,
											'module'	=> $module,
											'route'		=> $route,
											'params'	=> array(
																	'section'	=> $row->alias),
											'title'		=> $row->name,
											'pages'		=> $childPages);
					}
				
			}
		}
		//print_r($returnArr);
		return $returnArr;
	}
	
	/**
	 * Returns an array of articles for the navigation
	 * 
	 * @param 	string	$section
	 * @return 	array
	 */
	protected function _getArticlesForSection($section, $action = 'index', $route = 'default', $controller = 'index', $module = 'default') {
		$articleMapper = new Application_Model_ArticleMapper();
		$returnArr = array();
		
		$children = $articleMapper->findBySection($section);
		
		foreach($children as $child) {
			$returnArr[] = array(
							'label'			=> $child->getTitle(),
							'controller'	=> $controller,
							'action'		=> $action,
							'module'		=> $module,
							'route'			=> $route,
							'params'		=> array(
														'section'	=> $section,
														'article' 	=> $child->getUrlRewrite()),
							'title'			=> $child->getTitle(),
							'visible'		=> true
						);
		}
		return $returnArr;
	}
}

