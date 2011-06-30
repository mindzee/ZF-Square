<?php

class Catalog_ItemController extends Zend_Controller_Action
{
	public function init()
	{
		// nustamo veiksmo metodu kontekstai(kokie failai bus uzkraunami)
		$contextSwitch = $this->_helper->getHelper('contextSwitch');
		$contextSwitch->addActionContext('search', 'xml')
			->initContext();
	}
	/**
	 * 
	 * Veiksmo metodas parodantis tam tikra katalogo
	 * elementa.
	 */
	public function displayAction()
	{
		// nustatomi filtrai ir validatoriai GET tipo
		// informacijai 
		$filterRules = array(
			'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
		);
		
		$validatorRules = array(
			'id' => array('NotEmpty', 'Int',)
		);
		
		//1.patikrinama ar duomenys teisingi
		//2.gaunami uzklausti duomenys
		//3.duomenys siunciami i view
		$input = new Zend_Filter_Input($filterRules, $validatorRules);
		$input->setData($this->getRequest()->getParams());
		
		if ($input->isValid())
		{
			$query = Doctrine_Query::create()
				->from('Square_Model_Item item')
				->leftJoin('item.Square_Model_Country country')
				->leftJoin('item.Square_Model_Grade grade')
				->leftJoin('item.Square_Model_Type type')
				->where('item.record_id=?', $input->id)
				->addWhere('item.display_status = 1')
				->addWhere('item.display_until >= CURDATE()');
				
			$result = $query->fetchArray();
			
			if (count($result) == 1)
			{
				$this->view->item = $result[0];
			}
			else
			{
				throw new Zend_Controller_Action_Exception('Puslapis nerastas', 404);
			}
		}
		else 
		{
			throw new Zend_Controller_Action_Exception('Neteisingas užklausos parametras');
		}
	}
	
	public function createAction()
	{
		// sukuriama forma ir formos sukuriama view faile
		$form = new Square_Form_ItemCreate();
		
		$this->view->form = $form;
		
		// 1. Patikrinama ar duomenys suvesti teisingai
		// 2. Jeigu viskas gerai, modelis uzpildomas
		// 3. Kaikuriems laukams suteikiamos default reiksmes
		// 4. Issaugojama i duomenu baze
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				$item = new Square_Model_Item();
				$item->fromArray($form->getValues());
				$item->record_date = date('Y-m-d', mktime());
				$item->display_status = 0;
				$item->display_until = null;
				$item->save();
				
				$id = $item->record_id;
				
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					'Jūsų pateikti duomenys priimti ir išsaugoti #' . $id .
					'. Mūsų administratorius peržiūrės ir jeigu neras nieko taisytino jūsų
					skelbimas bus matomas mūsų puslapyje 48 valandų bėgyje.');
				
				$this->_redirect('/catalog/item/success');
			}
		}
	}
	
	public function successAction()
	{
		$messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
		
		if ($messages)
		{
			$this->view->messages = $messages;
		}
		else 
		{
			$this->_redirect('/');
		}
	}
	
	public function searchAction()
	{
		// sugeneruojama paieskos lauku ivedimo forma
		$form = new Square_Form_Search();
		
		$this->view->form = $form;
		
		// jeigu forma pereina validacija
		if ($form->isValid($this->_request->getParams()))
		{
			// istraukiamos formos lauku reiksmes 
			$input = $form->getValues();
			
			// jeigu paieskos laukas nera tuscias ...
			if (!empty($input['query']))
			{
				// is konfiguracinio failo nuskaitomas index direktorijos kelias
				$config = $this->getInvokeArg('bootstrap')->getOption('indexes');
				
				$index = Zend_Search_Lucene::open($config['indexPath']);
				
				$this->view->results = $index->find(
					Zend_Search_Lucene_Search_QueryParser::parse($input['query']));
			}
		}
	}
}