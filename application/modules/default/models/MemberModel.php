<?php

class MemberModel
{
	protected $_db;
	
	public $id;
	public $name;
	public $age;
	public $type;
	public $rate;
	
	public function __construct()
	{
		$this->_db = new PDO('mysql:dbname=db;host=localhost', 'root', '');
		
		$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function fetch($id)
	{
		$id = $this->_db->quote($this->id);
		
		$result = $this->_db->query("SELECT * FROM member WHERE id={$id}");
		
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function fetchAll()
	{
		$result = $this->_db->query('SELECT * FROM member');
		
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function save()
	{
		$data = array();
		
		$data['name'] = htmlentities($this->name);
		$data['age'] = htmlentities($this->age);
		$data['type'] = htmlentities($this->type);
		
		//validating age
		if ($data['age'] < 18)
		{
			throw new Exception('Member under 18 years');
		}
		
		// auto-calculation of discount based on
		// membership type
		switch ($data['type'])
		{
			case 'silver':
				$data['rate'] = 0;
				break;
			
			case 'gold':
				$data['rate'] = 0.10;
				break;
				
			case 'platinum':
				$data['rate'] = 0.25;
				break;
		}
		
		$query = "INSERT INTO member(name, age, type, discount_rate) VALUES(
			'{$this->_db->quote($data['name'])}', 
			'{$this->_db->quote($data['age'])}', 
			'{$this->_db->quote($data['type'])}', 
			'{$this->_db->quote($data['rate'])}')";
		$this->_db->exec($query);
		
		return $this->_db->lastInsertId();
	}
}