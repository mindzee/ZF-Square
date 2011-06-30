<?php

class StaticContentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
	
    /**
     * 
     * Uzkrauti ir rodyti statinius view failus
     */
    public function displayAction()
    {
    	$page = $this->getRequest()->getParam('page');
    	
    	$filePath = $this->view->getScriptPath(null) . DIRECTORY_SEPARATOR .
    				$this->getRequest()->getControllerName() . DIRECTORY_SEPARATOR .
    				$page . '.' . $this->viewSuffix;
    	
    	if (file_exists($filePath))
    	{
    		$this->render($page);
    	}
    	else
    	{
    		throw new Zend_Controller_Action_Exception('Puslapis nerastas', 404);
    	}
    }
    
    public function indexAction()
    {
        // action body
    }


}

