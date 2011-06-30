<?php

class Square_Form_Contact extends Zend_Form
{
	public function init()
	{
		//sukuriamas forma ir nustatomi pagrindiniai parametrai
		$this->setAction('/contact/index')
			->setMethod('post');
			
		// sukuriamas text laukas vardui
		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Vardas:')
			->setOptions(array('size' => '35'))
			->setRequired(true)
			->addValidator('NotEmpty', true)
			->addValidator('Alpha', true)
			->addFilter('HTMLEntities')
			->addFilter('StringTrim');
			
		// sukuriamas text laukas e-pastui
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Elektroninis paštas:')
			->setOptions(array('size' => '50'))
			->setRequired(true)
			->addValidator('NotEmpty', true)
			->addValidator('EmailAddress', true)
			->addFilter('HTMLEntities')
			->addFilter('StringToLower')
			->addFilter('StringTrim');
			
		//sukuriamas textarea laukas zinutes tekstui
		$message = new Zend_Form_Element_Textarea('message');
		$message->setLabel('Žinutė')
			->setOptions(array('rows' => '8', 'cols' => '40'))
			->setRequired(true)
			->addValidator('NotEmpty', true)
			->addFilter('HTMLEntities')
			->addFilter('StringTrim');
			
		// sukuriamas CAPTCHA
		$captcha = new Zend_Form_Element_Captcha('captcha', array(
			'captcha' => array(
				'captcha' => 'Image',
				'wordLen' => 6,
				'timeout' => 300,
				'width' => 300,
				'height' => 100,
				'imgUrl' => '/captcha',
				'imgDir' => APPLICATION_PATH . '/../public/captcha',
				'font' => APPLICATION_PATH . '/../public/fonts/LiberationSansRegular.ttf'
		)));
		
		// sukuriamas submit mygtukas
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Siūsti žinutę')
			->setOptions(array('class' => 'submit'));

		// visi elementai idedami i forma
		$this->addElement($name)
			->addElement($email)
			->addElement($message)
			->addElement($captcha)
			->addElement($submit);
	}
}
