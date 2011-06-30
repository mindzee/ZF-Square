<?php

class Square_Form_Search extends Zend_Form
{
	public function init()
	{
		// inicijuojama forma (nustatomi "action" ir "method" parametrai)
		$this->setAction('/catalog/item/search')
			->setMethod('get');
			
		// sukuriamas "text" laukas paieskos zodziu ivedimui
		$query = new Zend_Form_Element_Text('query');
		$query->setLabel('Raktiniai žodžiai:')
			->setOptions(array('size' => '20'))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		$query->setDecorators(array(
			array('ViewHelper'),
			array('Errors'),
			array('Label', array('tag' => '<span>'))
		));
		
		// sukuriamas "submit" mygtukas
		$submit= new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Ieškoti')
			->setOptions(array('class' => 'submit'));
		
		$submit->setDecorators(array(
			array('ViewHelper'),
		));
		
		// elementai idedami i forma
		$this->addElement($query)
			->addElement($submit);
	}
}