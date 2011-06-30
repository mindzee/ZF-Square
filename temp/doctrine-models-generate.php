<?php

// itraukti pagrindini Doctrine klases faila
require_once('/../library/Doctrine/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));

// sukuriamas Doctrine manager'is
$manager = Doctrine_Manager::getInstance();

// sukuriamas duomenu bazes rysys
$connection = Doctrine_Manager::connection('mysql://square:square@localhost/square', 'doctrine')
	->setCharset('utf8');

// auto-generuojami modeliai
Doctrine::generateModelsFromDb('temp/models', array('doctrine'), array(
														'classPrefix' => 'Square_Model_'));

?>
