<?php

class Square_Form_Search extends Zend_Form
{
	public $messages = array(
		Zend_Validate_Int::INVALID => '\'%value%\' nėra integer\'is',
		Zend_Validate_Int::NOT_INT => '\'%value%\' nėra integer\'is',
	);
	
	public function init()
	{
		// inicijuojama forma
		$this->setAction('/catalog/item/search')
			->setMethod('get');
			
		// nustatomi formos dekoratoriai
		$this->setDecorators(array(
			array('FormErrors',
				array('markupListItemStart' => '', 'markupListItemEnd' => '')),
			array('FormElements'),
			array('Form')
		));
		
		// sukuriama "text" laukas metu paieskos lauko ivedimui
		$year = new Zend_Form_Element_Text('year');
		$year->setLabel('Metai:')
			->setOptions(array('size' => '6'))
			->addValidator('Int', false, array('messages' => $this->messages))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		$price = new Zend_Form_Element_Text('price');
		$price->setLabel('Kaina:')
			->setOptions(array('size' => '8'))
			->addValidator('Int', false, array('messages' => $this->messages))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim');
			
		// sukuriama "select" pasirinkimo laukai paieskai pagal ivertinima
		$grade = new Zend_Form_Element_Select('grade');
		$grade->setLabel('Įvertinimas:')
			->addValidator('Int', false, array('messages' => $this->messages))
			->addFilter('HtmlEntities')
			->addFilter('StringTrim')
			->addMultiOption('', 'Nesvarbu');
		
		foreach ($this->getGrades() as $g)
		{
			$grade->addMultiOption($g['grade_id'], $g['grade_name']);
		}
		
		// sukuriamas "submit" mygtukas
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Ieškoti')
			->setOptions(array('class' => 'submit'));
			
		// ideti elementus i forma
		$this->addElements(array($year, $price, $grade, $submit));
		
		// nustatomi elemento dekoratoriai
		$this->setElementDecorators(array(
			array('ViewHelper'),
			array('Label', array('tag' => '<span>'))
		));
		
		$submit->setDecorators(array(
			array('ViewHelper')
		));
	}
	
	public function getGrades()
	{
		$query = Doctrine_Query::create()
			->from('Square_Model_Grade grade');
			
		return $query->fetchArray();
	}
}