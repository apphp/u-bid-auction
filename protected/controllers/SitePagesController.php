<?php
/**
 * SitePages controller
 *
 * PUBLIC:                  PRIVATE
 * ---------------          ---------------
 * __construct              
 *
 */

class SitePagesController extends CController
{
	
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();
		
		// 1. COMMENT THIS LINE OF CODE
		// 	  TO ACTIVATE THIS CONTROLLER
		// 2. Add new URL rules to config/main.php file:
		//    Ex.: 'about-us' => 'sitePages/show/name/about-us',

		// REMOVE THIS BLOCKER
		// if you want to start using this controller
		$this->redirect(Website::getDefaultPage());
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect(Website::getDefaultPage());
    }
    
    /**
     * Manage action handler
	 * Ex.: sitepages/show/page/page-name-1
     * @param string $name
     */
    public function showAction($name = '')
    {
		if($name == 'page-name-1'){
			$this->_view->render('sitePages/example-page-1');
		}elseif($name == 'page-name-2'){
			$this->_view->render('sitePages/example-page-2');
		}else{
			$this->redirect(Website::getDefaultPage());	
		}	
    }
 
}
