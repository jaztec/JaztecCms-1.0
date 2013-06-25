<?php
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        // Get page data from database
        $pageSetup = new Application_Model_PageNavigation();
        
        // Get the action
        $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                        
        if(!('sitemap' == $action)) {
        				
        	$this->view->adAllowed = 0x0;
        	
                        $pageAlias = $pageSetup->getPageByAlias($action);
                        
                        // Set the title and meta tags
                        $this->view->headTitle($pageAlias['title']);
                        $this->view->headMeta()->appendName('description', $pageAlias['desc'])
                        					   ->appendName('keywords', $pageAlias['keywords'])
                        					   ->appendHttpEquiv('Content-Type', $pageAlias['httpequiv']);
                        					   
                		$this->view->headScript()
                							->appendFile('/js/script.js')
                							->appendFile('/js/google-ana.js')
                							->appendFile('/js/syntaxhighlighter/scripts/XRegExp.js')
                							->appendFile('/js/syntaxhighlighter/scripts/shCore.js')
                							->appendFile('/js/syntaxhighlighter/scripts/shAutoloader.js')
                							->appendFile('/js/syntaxhighlighter/scripts/shBrushPhp.js')
                							->appendFile('/js/syntaxhighlighter/scripts/shBrushVb.js');
						$this->view->headLink()->appendStylesheet('/js/syntaxhighlighter/styles/shCore.css')
												->appendStylesheet('/js/syntaxhighlighter/styles/shThemeDefault.css');
                		$this->view->jQuery()
                    				   ->addJavascriptFile('/js/jquery/lightbox/js/jquery.lightbox-0.5.min.js')
                    				   ->addJavascriptFile('/js/jquery/jquery-ui-1.8.13.custom.min.js')
                    				   ->addJavascriptFile('/js/lightbox.id.js')
                    				   ->addJavascriptFile('/js/accordion.id.js')
                			   		   ->addStyleSheet('/js/jquery/lightbox/css/jquery.lightbox-0.5.css');
        }
    }

    public function indexAction()
    {
        $articles = new Application_Model_ArticleMapper();
                $this->view->frontArticle = $articles->findLastFrontArticle();
    }

    public function artikelenAction()
    {
        // Return array
                    	$html = array();
                    	
                    	// Get article and section database tables
                    	$dbArticles = new Application_Model_ArticleMapper();
                    	
                   		// Load request and set defaults
                    	$request = $this->getRequest();
                    	$article = $request->getParam('article', null);
                    	$section = $request->getParam('section', null);
                    	
                    	// Begin parameter checking
                    	if($article) { // A { Check if top level 'article' is set 
                    		$reqArticle = $dbArticles->findByUrl($article);
                    		$this->view->multiplicity = false;
                    		// Set new title and description, both values are overwritten
                    		$this->view->adAllowed = 0x1;
                    		$this->view->headTitle(': ' . $reqArticle->getTitle());
                        	$this->view->headMeta()->setName('description', (string) $reqArticle->getDescription());
                        	// Set html value
                    		$html[] = $reqArticle;
                    	} elseif($section) { // A} B{ Article is not set so check if lower level 'section' is set
                    		$requestedSection = new Application_Model_DbTable_Sections();
                    		$requestedSection = $requestedSection->getSectionByAlias($section);
                    		$this->view->multiplicity = true; 
                    		// Set new title and description, both values are overwritten
                    		$this->view->headTitle(': ' . $requestedSection->getName());
                        	$this->view->headMeta()->setName('description', (string) $requestedSection->getDescription());
                    		//$html = $dbArticles->findBySection($section);
                    		$html = $dbArticles->findAllFromSection($section);
                    	} else { // B} C{ 'article' nor 'section' is set so show unfiltered result
                    		$this->view->multiplicity = true; 
                    		$html = $dbArticles->findAll();
                    	} // C}
                    	
                    	$this->view->result = $html;
    }

    public function contactAction()
    {
        $request = $this->getRequest();
                        $form = new Application_Form_Contact();
                    	
                        if($request->isPost()) {
                        	if($form->isValid($request->getPost())) {
                        		return $this->_helper->redirector('index');
                        	}
                        }
                        
                    	$this->view->form = $form;
    }

    public function aboutAction()
    {
        // action body
    }

    public function portfolioAction()
    {
        // action body
    }

    public function sitemapAction()
    {
		$this->_helper->layout->disableLayout();
		//$this->_helper->viewRenderer->setNoRender();
    }


}











