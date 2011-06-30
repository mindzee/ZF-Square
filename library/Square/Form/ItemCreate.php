<?php

class Square_Form_ItemCreate extends Zend_Form
{
	public function init()
	{
		// sukuriama forma
		$this->setAction('/catalog/item/create')
			->setMethod('post');
			
		// sukuriamas text tipo laukas vardui ivesti
		$name = new Zend_Form_Element_Text('seller_name');
		$name->setLabel('Vardas:')
			->setOptions(array('size' => '35'))
			->setRequired(true)
			->addValidator('Regex', false, array(
				'pattern' => '/^[a-zA-Z]+[A-Za-z\'\-\. ]{1,50}$/'))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas text tipo e-pasto ivedimo laukas
		$email = new Zend_Form_Element_Text('seller_email');
		$email->setLabel('Elektroninis paštas:')
			->setOptions(array('size' => '50'))
			->setRequired(true)
			->addValidator('EmailAddress', false)
			->addFilter('HtmlEntities')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
			
		// sukuriamas text tipo laukas telefono numeriui ivesti
		$telephone = new Zend_Form_Element_Text('seller_telephone');
		$telephone->setLabel('Telefono numeris:')
			->setOptions(array('size' => '50'))
			->addValidator('StringLength', false, array('min' => '8'))
			->addValidator('Regex', false, array(
				'pattern' => '/^\+[1-9][0-9]{6,30}$/',
				'messages' => array(
					Zend_Validate_Regex::INVALID => '\'%value%\' neatitinka tarptautinio numerių standarto +XXYYZZZZ',
					Zend_Validate_Regex::NOT_MATCH => '\'%value%\' neatitinka tarptautinio numerių standarto +XXYYZZZZ',
				)))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas textarea tipo laukas adresui ivesti
		$address = new Zend_Form_Element_Textarea('seller_address');
		$address->setLabel('Pašto adresas:')
			->setOptions(array('rows' => '6', 'cols' => '36'))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas text tipo laukas prekes pavadinimui ivesti
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Pavadinimas:')
			->setOptions(array('size' => '60'))
			->setRequired(true)
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas text tipo laukas metams ivesti
		$year = new Zend_Form_Element_Text('year');
		$year->setLabel('Metai:')
			->setOptions(array('size' => '8'))
			->setRequired(true)
			->addValidator('Between', false, array('min' => 1700, 'max' => 2015))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriams select tipo laukas saliai pasirinkti
		$country = new Zend_Form_Element_Select('country_id');
		$country->setLabel('Šalis:')
			->setRequired(true)
			->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim')
			->addFilter('StringToUpper');
			
		foreach ($this->getCountries() as $c)
		{
			$country->addMultiOption($c['country_id'], $c['country_name']);
		}
		
		// sukuriamas text laukas prekes vieneto nominaliai vertei ivesti
		$denomination = new Zend_Form_Element_Text('denomination');
		$denomination->setLabel('Nominali vertė:')
			->setOptions(array('size' => '8'))
			->setRequired(true)
			->addValidator('Float')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriami radio tipo laukai prekes vieneto tipui pasirinkti
		$type = new Zend_Form_Element_Radio('type_id');
		$type->setLabel('Tipas:')
			->setRequired(true)
			->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		foreach ($this->getTypes() as $t)
		{
			$type->addMultiOption($t['type_id'], $t['type_name']);
		}
		
		$type->setValue(1);
		
		// sukuriamas select tipo laukas prekes vienetu ivertinimui pasirinkti
		$grade = new Zend_Form_Element_Select('grade_id');
		$grade->setLabel('Įvertinimas:')
			->setRequired(true)
			->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
		
		foreach ($this->getGrades() as $g)
		{
			$grade->addMultiOption($g['grade_id'], $g['grade_name']);
		}
		
		// sukuriamas text laukas pardavimo kainai(min) ivesti
		$minPrice = new Zend_Form_Element_Text('sale_price_min');
		$minPrice->setLabel('Pardavimo kaina (min):')
			->setOptions(array('size' => '8'))
			->setRequired(true)
			->addValidator('Float')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas text laukas pardavimo kainai(max) ivesti
		$maxPrice = new Zend_Form_Element_Text('sale_price_max');
		$maxPrice->setLabel('Pardavimo kaina (max):')
			->setOptions(array('size' => '8'))
			->setRequired(true)
			->addValidator('Float')
			->addFilter('HTMLEntities')
			->addFilter('StringTrim');
			
		// sukuriamas textarea laukas apibudinimui ivesti
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Apibūdinimas:')
			->setOptions(array('rows' => '15', 'cols' => '60'))
			->setRequired(true)
			->addFilter('HTMLEntities')
			->addFilter('StripTags')
			->addFilter('StringTrim');
			
		// sukuriamas CAPTCHA patikrinimas
		$captcha = new Zend_Form_Element_Captcha('captcha', array(
			'captcha' => array(
				'captcha' => 'Image',
				'wordLen' => 6,
				'timeout' => 300,
		 		'width' => 300,
				'height' => 100,
				'imgUrl' => '/captcha',
				'imgDir' => APPLICATION_PATH . '/../public/captcha',
				'font' => APPLICATION_PATH . '/../public/fonts/LiberationSansRegular.ttf',
		)));
		
		// sukuriamas submit mygtukas
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Įvesti įrašą')
			->setOrder(100)
			->setOptions(array('class' => 'submit'));
			
		// konatktinex informacijos elementai idedami i forma
		$this->addElement($name)
			->addElement($email)
			->addElement($telephone)
			->addElement($address);
			
		// sukuriama lauku grupe pardavejo informacijai
		$this->addDisplayGroup(
			array('seller_name', 'seller_email', 'seller_telephone', 'seller_address'),
			'contact');
			
		$this->getDisplayGroup('contact')
			->setOrder(10)
			->setLegend('Pardavėjo informacija');
			
		// idedami i forma like vieneto informacijos elementai
		$this->addElement($title)
			->addElement($year)
			->addElement($country)
			->addElement($denomination)
			->addElement($type)
			->addElement($grade)
			->addElement($minPrice)
			->addElement($maxPrice)
			->addElement($description);

		// sukuriama lauku grupe parduodamo vieneto informacijai
		$this->addDisplayGroup(
			array('title', 'year', 'country_id', 'denomination', 'type_id',
					'grade_id', 'sale_price_min', 'sale_price_max', 'description'), 
			'item');
			
		$this->getDisplayGroup('item')
			->setOrder(20)
			->setLegend('Vieneto informacija');
			
		// ideamas CAPTCHA laukas i forma
		$this->addElement($captcha);
		
		// sukuriama CAPTCHA lauku grupe
		$this->addDisplayGroup(array('captcha'), 'verification');
		$this->getDisplayGroup('verification')
			->setOrder(30)
			->setLegend('Patvirtinimo kodas');
			
		// i forma idedamas submit mygtukas
		$this->addElement($submit);
	}
	
	public function getCountries()
	{
		$query = Doctrine_Query::create()->from('Square_Model_Country country');
			
		return $query->fetchArray();
	}
	
	public function getGrades()
	{
		$query = Doctrine_Query::create()->from('Square_Model_Grade grade');
		
		return $query->fetchArray();
	}
	
	public function getTypes()
	{
		$query = Doctrine_Query::create()->from('Square_Model_Type type');
		
		return $query->fetchArray();
	}
}
