<?php

class Square_Form_ItemUpdate extends Square_Form_ItemCreate
{
	public function init()
	{
		// iskvieciamas tevineje klaseje esantis init metodas
		// tai sukuriama ItemCreate forma, tereikia kaikuriuos elementus
		// isimti
		parent::init();
		
		// nustatomas veiskmas 
		$this->setAction('/admin/catalog/item/update');
		
		// isimami nereikalingi elementai
		$this->removeElement('captcha');
		$this->removeDisplayGroup('verification');
		
		// sukuriamas hidden laukas ID 
		$id = new Zend_Form_Element_Hidden('record_id');
		$id->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas select pasirinkimas vienetu stauso parodymui
		$display = new Zend_Form_Element_Select('display_status', 
			array('onchange' => "handleInputDisplayOnSelect('display_status', 'divDisplayUntil', new Array('1'));"));
			
		$display->setLabel('Rodyti statusą:')
			->setRequired(true)
			->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		$display->addMultiOptions(array(0 => 'Hidden', 1 => 'Visible'));
		
		// sukuriamas hidden laikas vieneto pirmos datos laukui uzfiksuoti
		$displayUntil = new Zend_Form_Element_Hidden('display_until');
		$displayUntil->addValidator('Date')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriamas select pasirinkimu laukas vieneto rodymo datai
		$displayUntilDay = new Zend_Form_Element_Select('display_until_day');
		$displayUntilDay->setLabel('Rodyti iki:')
			->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim')
			->addFilter('StringToUpper')
			->setDecorators(array(
				array('ViewHelper'),
				array('Label', array('tag' => 'dt')),
				array('HtmlTag', array(
					'tag' => 'div',
					'openOnly' => true,
					'id' => 'divDisplayUntil',
					'placement' => 'prepend',
				)
			)
		));
		
		for ($i = 1; $i <= 31; $i++)
		{
			$displayUntilDay->addMultiOption($i, sprintf('%02d', $i));
		}
		
		$displayUntilMonth = new Zend_Form_Element_Select('display_until_month');
		$displayUntilMonth->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim')
			->setDecorators(array(array('ViewHelper')));
			
		for ($i = 1; $i <= 12; $i++)
		{
			$displayUntilMonth->addMultiOption($i, date('M', mktime(1, 1, 1, $i, 1, 1)));
		}
		
		$displayUntilYear = new Zend_Form_Element_Select('display_until_year');
		$displayUntilYear->addValidator('Int')
			->addFilter('HtmlEntities')
			->addFilter('StringTrim')
			->setDecorators(array(
				array('ViewHelper'),
				array('HtmlTag', array('tag' => 'div', 'closeOnly' => true)))
		);
		
		for ($i = 2009; $i <=2012; $i++)
		{
			$displayUntilYear->addMultiOption($i, $i);
		}
		
		//elementai dedami i forma
		$this->addElement($id)
			->addElement($display)
			->addElement($displayUntil)
			->addElement($displayUntilDay)
			->addElement($displayUntilMonth)
			->addElement($displayUntilYear);
			
		// sukuriama lauku grupe statusui
		$this->addDisplayGroup(array(
			'display_status', 
			'display_until_day',
			'display_until_month',
			'display_until_year',
			'display_until'), 'display');
		
		$this->getDisplayGroup('display')
			->setOrder(25)
			->setLegend('Rodyti informaciją');
	}
}