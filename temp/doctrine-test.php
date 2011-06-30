<?php

// itraukti pagrindini Doctrine klases faila
require_once('library/Doctrineghj/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));

// sukuriamas Doctrine manager'is
$manager = Doctrine_Manager::getInstance();

// sukuriamas duomenu bazes rysys
$connection = Doctrine_Manager::connection('mysql://square:square@localhost/square', 'doctrine');

// nuskaitomas ir atspausdinamas duomenu baziu sarasas
$databases = $connection->import->listDatabases();
var_dump($databases);

?>
