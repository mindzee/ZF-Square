<?php

class ContactController extends Zend_Controller_Action
{
	public function init()
	{
		$this->view->doctype('XHTML1_TRANSITIONAL');
	}
	
	public function indexAction()
	{
		$form = new Square_Form_Contact();
		
		$this->view->form = $form;
		
		// tikrinama ar uzklausa yra POST
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				$values = $form->getValues();
				
				$smtpServer = 'smtp.gmail.com';
				$username = 'mindzee2000@gmail.com';
				$password = 'slaptas';
				
				$config = array(
				'ssl' => 'ssl',
                'auth' => 'login',
                'username' => 'mindzee2000@gmail.com',
                'password' => 'slaptas',
				'port' => 465);

				$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
				
				$mail = new Zend_Mail();
				$mail->setBodyText($values['message'])
					->setFrom($values['email'], $values['name'])
					->addTo('mindzee2000@yahoo.com')
					->setSubject('Kontaktinės informacijos duomenys')
					->send($transport);
					
				$this->_helper->getHelper('FlashMessenger')
					->addMessage('Ačių. Jūsų žinutė sėkmingai išsiųsta.');
					
				$this->_redirect('/contact/success');
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
}