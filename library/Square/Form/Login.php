<?php

class Square_Form_Login extends Zend_Form
{
	public function init()
	{
		$this->setAction('/admin/login')
			->setMethod('post');
			
		// sukuriamas "text" vartotojo vardo ivedimo laukas
		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Vartotojo vardas:')
			->setOptions(array('size' => '30'))
			->setRequired(true)
			->addValidator('Alnum')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');

		// sukuriamas "password" vartotojo slaptazodzio ivedimo laukas
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Slaptažodis:')
			->setRequired(true)
			->setOptions(array('size' => '30'))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas "submit" mygtukas
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Prisijungti')
			->setOptions(array('class' => 'submit'));
			
		// elementai idedami i forma
		$this->addElements(array($username, $password, $submit));
	}
}