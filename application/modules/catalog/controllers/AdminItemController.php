<?php

class Catalog_AdminItemController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		// patikrinama ar uzklausa neturi /admin
		//nustatomas admin layout'as
		$url = $this->getRequest()->getRequestUri();
		
		$this->_helper->layout->setLayout('admin');
		
		// patikrinama ar vartotojas autentikuotas, jeigu 
		// ne nukreipiama i prisijungimo puslapi
		if (!Zend_Auth::getInstance()->hasIdentity())
		{
			$session = new Zend_Session_Namespace('square.auth');
			$session->requestURL = $url;
			
			$this->_redirect('/admin/login');
		}
	}
	/**
	 * 
	 *  Veiksmo metodas parodantis visus katalogo prekiu vienetus
	 */
	public function indexAction()
	{
		// nustatomi filtrai ir validatoriai GET duomenims
		$filters = array(
			'page' => array('HtmlEntities', 'StripTags', 'StringTrim')
		);
		
		$validators = array(
			'page' => array('Int')
		);
		
		$input = new Zend_Filter_Input($filters, $validators);
		$input->setData($this->_request->getParams());
		
		// patikrinama ar duomenys validus
		// sukuriama duomenu bazes uzklausa
		if ($input->isValid())
		{
			$query = Doctrine_Query::create()
			->from('Square_Model_Item item')
			->leftJoin('item.Square_Model_Grade grade')
			->leftJoin('item.Square_Model_Country country')
			->leftJoin('item.Square_Model_Type type');
			
			$perPage = 5;
			$numPageLinks = 5;
			
			// sukuriamas puslapiavimas(Pager)
			$pager = new Doctrine_Pager($query, $input->page, $perPage);
			
			// ivykdoma uzklausa
			$result = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);
			
			// inicijuojama puslapiavimo layout'as
			$pagerRange = new Doctrine_Pager_Range_Sliding(
				array('chunk' => $numPageLinks), $pager);
				
			$pagerUrlBase = $this->view->url(
				array(), 'admin-catalog-index', 1) . "/{%page}";
				
			$pagerLayout = new Doctrine_Pager_Layout($pager, $pagerRange, $pagerUrlBase);
			
			// nustatomas puslapio nuorodos rodymo templeitas
			$pagerLayout->setTemplate('<a href="{%url}">{%page}</a>');
			$pagerLayout->setSelectedTemplate('<span class="current">{%page}</span>');
			$pagerLayout->setSeparatorTemplate('&nbsp;');
			
			$this->view->records = $result;
			$this->view->pages = $pagerLayout->display(null, true);
		}
		else 
		{
			throw new Zend_Controller_Action_Exception('Netinkami duomenys');
		}
	}
	
	public function deleteAction()
	{
		// nustatomi validatoriai ir filtrai POST ivestiems duomenims
		$filterRules = array(
			'ids' => array('HTMLEntities', 'StripTags', 'StringTrim')
		);
		
		$validatorRules = array(
			'ids' => array('NotEmpty', 'Int')
		);
		// sukuriamas ivestu duomenu tikrinimo pagal filtrus ir validatorius objektas
		// objektui siunciama uzklausos parametrai
		$input = new Zend_Filter_Input($filterRules, $validatorRules);
		$input->setData($this->getRequest()->getParams());
		
		// jeigu visi parametrai islaiko validavimo ir filtru testus ...
		if ($input->isValid())
		{
			$query = Doctrine_Query::create()
				->delete('Square_Model_Item item')
				->whereIn('item.record_id', $input->ids);
				
			$result = $query->execute();
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage('Įrašai sėkmingai ištrinti.');
				
			$this->_redirect('/admin/catalog/item/success');
		}
		else 
		{
			throw new Zend_Controller_Action_Exception('Neteisingai įvesti duomenys');
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
			$this->_redirect('/admin/catalog/item/index');
		}
	}
	
	public function updateAction()
	{
		$form = new Square_Form_ItemUpdate();
		
		$this->view->form = $form;
		
		// jeigu uzklausa "POST" tipo
		if ($this->getRequest()->isPost())
		{
			// 1.Nuskaitomi formos laukai rodantys iki kada sis vienetas turi buti rodomas
			// 2.Visu triju lauku reiksmes sujungiamos ir reiksmes patalpinama 
			// "hidden" tipo lauke display_until
			$postData = $this->getRequest()->getPost();
			$postData['display_until'] = sprintf('%04d-%02d-%02d', 
				$this->getRequest()->getPost('display_until_year'),
				$this->getRequest()->getPost('display_until_month'),
				$this->getRequest()->getPost('display_until_day')
			);
			
			// jeigu forma atitinka reikalvimus ...
			if ($form->isValid($postData))
			{
				$input = $form->getValues();
				
				$item = Doctrine::getTable('Square_Model_Item')
					->find($input['record_id']);
					
				$item->fromArray($input);
				
				$item->display_until = ($item->display_status == 0) ? null : $item->display_until;
				
				$item->save();
				
				$this->_helper->getHelper('FlashMessenger')
					->addMessage('Įrašas sėkmingai atnaujintas.');
					
				$this->_redirect('/admin/catalog/item/success');
			}
		}
		else 
		{
			// jeigu uzklausa ne POST 
			$filterRules = array(
				'id' => array('HtmlEntities', 'StripTags', 'StringTrim'));
			
			$validatorRules = array('id' => array('NotEmpty', 'Int'));
			
			$input = new Zend_Filter_Input($filterRules, $validatorRules);
			$input->setData($this->getRequest()->getParams());
			
			if ($input->isValid())
			{
				$query = Doctrine_Query::create()
					->from('Square_Model_Item item')
					->leftJoin('item.Square_Model_Country country')
					->leftJoin('item.Square_Model_Grade grade')
					->leftJoin('item.Square_Model_Type type')
					->where('item.record_id=?', $input->id);
					
				$result = $query->fetchArray();
				
				if (count($result) == 1)
				{
					$date = $result[0]['display_until'];
					
					$result[0]['display_until_day'] = date('d', strtotime($date));
					$result[0]['display_until_month'] = date('m', strtotime($date));
					$result[0]['display_until_year'] = date('Y', strtotime($date));
					
					// uzpildomi formos laukai duomenimis is duomenu bazes
					$this->view->form->populate($result[0]);
				}
				else 
				{
					throw new Zend_Controller_Action_Exception('Puslapis nerastas', 404);
				}
			}
			else 
			{
				throw new Zend_Controller_Action_Exception('Neteisingai suvesti duomenys');
			}
		}
	}
	
	public function displayAction()
  	{
	    // nustatomi filtrai ir validatoriai
	    $filters = array(
	      	'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
	    ); 
	       
	    $validators = array(
	      	'id' => array('NotEmpty', 'Int')
	    );
	    
	    $input = new Zend_Filter_Input($filters, $validators);
	    $input->setData($this->getRequest()->getParams());
	
	    // patikrinama ar duomenys teisingai suvesti
	    // gaunamas uzklausto id duomenys
	    // pritvirtinama prie view failo
	    if ($input->isValid()) 
	    {
	      	$query = Doctrine_Query::create()
	            ->from('Square_Model_Item item')
	            ->leftJoin('item.Square_Model_Country country')
	            ->leftJoin('item.Square_Model_Grade grade')
	            ->leftJoin('item.Square_Model_Type type')
	            ->where('item.record_id = ?', $input->id);
	            
	      	$result = $query->fetchArray();
	      	
	      	if (count($result) == 1) 
	      	{
	        	$this->view->item = $result[0];               
	      	} 
	      	else 
	      	{
	        	throw new Zend_Controller_Action_Exception('Puslapis neratas', 404);        
	      	}
	    } 
	    else 
	    {
	      throw new Zend_Controller_Action_Exception('Neteisingai įvesti duomenys');              
		}
  	} 

  	public function createFulltextIndexAction()
  	{
  		$query = Doctrine_Query::create()
  			->from('Square_Model_Item item')
  			->leftJoin('item.Square_Model_Country country')
  			->leftJoin('item.Square_Model_Grade grade')
  			->leftJoin('item.Square_Model_Type type')
  			->where('item.display_status = 1')
  			->addWhere('item.display_until >= CURDATE()');
  			
  		$results = $query->fetchArray();
  		
  		// is konfiguracinio failo nuskaitomas index direktorijos kelias
  		$config = $this->getInvokeArg('bootstrap')->getOption('indexes');
  		
  		// sukuriamas index'as
  		$index = Zend_Search_Lucene::create($config['indexPath']);
  		
  		// kiekvienam laukui duomenu bazes lenteleje atitinkanciam uzklausos
  		// reikalavimus sukuriamas index dokumentas
  		foreach ($results as $result)
  		{
  			// naujas dokumentas index'e
  			$doc = new Zend_Search_Lucene_Document();
  			
  			$doc->addField(Zend_Search_Lucene_Field::text('Title', $result['title'], 'utf-8'));
  			$doc->addField(Zend_Search_Lucene_Field::text('Country', 
  														  $result['Square_Model_Country']['country_name'], 'utf-8'));
  			$doc->addField(Zend_Search_Lucene_Field::text('Grade', $result['Square_Model_Grade']['grade_name'], 'utf-8'));
  			$doc->addField(Zend_Search_Lucene_Field::text('Year', $result['year']));
  			
  			$doc->addField(Zend_Search_Lucene_Field::unStored('Description', $result['description'], 'utf-8'));
  			$doc->addField(Zend_Search_Lucene_Field::unStored('Denomination', $result['denomination']));
  			$doc->addField(Zend_Search_Lucene_Field::unStored('Type', $result['Square_Model_Type']['type_name']));
  			
  			$doc->addField(Zend_Search_Lucene_Field::unIndexed('SalePriceMin', $result['sale_price_min']));
  			$doc->addField(Zend_Search_Lucene_Field::unIndexed('SalePriceMax', $result['sale_price_max']));
  			$doc->addField(Zend_Search_Lucene_Field::unIndexed('RecordID', $result['record_id']));
  			
  			// rezulatatas issaugojamas index'e
  			$index->addDocument($doc);
  		}
  		
  		// index'o dokumentu skaicius
  		$count = $index->count();
  			
  		$this->_helper->getHelper('FlashMessenger')
  			->addMessage("Indeksas buvo sėkmingai sukurtas, jį sudaro {$count} dokumentai.");
  		$this->_redirect('/admin/catalog/item/success');
  	}
}