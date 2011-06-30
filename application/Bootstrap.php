<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDoctrine()
	{
		require_once('Doctrine/Doctrine.php');
		
		$this->getApplication()->getAutoloader()
			->pushAutoloader(array('Doctrine', 'autoload'), 'Doctrine');
			
		$manager = Doctrine_Manager::getInstance();
		$manager->setAttribute(
			Doctrine::ATTR_MODEL_LOADING, 
			Doctrine::MODEL_LOADING_CONSERVATIVE
		);
		
		$config = $this->getOption('doctrine');
		
		$connection = Doctrine_Manager::connection($config['dsn'], 'doctrine')->setCharset('utf8');
	}
}

