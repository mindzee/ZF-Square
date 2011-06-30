<?php

class Square_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface
{
	/**
	 * @var array kur patalpinta autebtikuoto vartotojo informacija
	 */
	protected $_resultArray;
	
	/**
	 * Konstruktorius
	 * 
	 * @param string $username vartotojo vardas
	 * @param string $password vartotojo slaptazodis
	 */
	public function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	 * Pagrindinis vartotojo autentifikavimo metodas.
	 * Vykdo duomenu bazes uzklausa jog sulyginti prisijungimo 
	 * duomenis.
	 * 
	 * @return Zend_Auth_Result
	 * 
	 */
	public function authenticate() 
	{
		$query = Doctrine_Query::create()
			->from('Square_Model_User user')
			->where('user.username = ? AND user.password = PASSWORD(?)',
				array($this->username, $this->password));
				
		$result = $query->fetchArray();
		
		if (count($result) == 1)
		{
			return new Zend_Auth_Result(
				Zend_Auth_Result::SUCCESS, $this->username, array()
			);
		}
		else 
		{
			return new Zend_Auth_Result(
				Zend_Auth_Result::FAILURE, null, array('Autentifikavimas nepavyko')
			);
		}
	}
	/**
	 * 
	 * 
	 * @param array $excludeFields
	 */
	public function getResultArray($excludeFields = null)
	{
		if (!$this->_resultArray)
		{
			return false;
		}
		
		if ($excludeFields != null)
		{
			$excludeFields = (array) $excludeFields;
			
			foreach ($this->_resultArray as $key => $value)
			{
				if (!in_array($key, $excludeFields))
				{
					$returnArray[$key] = $value;
				}
			}
			
			return $returnArray;
		}
		else 
		{
			return $this->_resultArray;
		}
	}
}