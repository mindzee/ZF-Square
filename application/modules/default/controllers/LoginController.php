<?php

class LoginController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->layout->setLayout('admin');	
	}
	
	public function loginAction()
	{
		$loginForm = new Square_Form_Login();
		
		$this->view->loginForm = $loginForm;

		// 1.Patikrinama ar prisijungimo duomenys teisingi
		// 2.Autentifikuojama naudojantis adpater'i
		// 3.Ikelti vartotojo informacija i sesija
		// 4. Nukreipiama i orginaliai uzklausta puslapi jeigu toks yra
		if ($this->getRequest()->isPost())
		{
			if ($loginForm->isValid($this->getRequest()->getPost()))
			{
				$values = $loginForm->getValues();
				
				$adapter = new Square_Auth_Adapter_Doctrine($values['username'], $values['password']);
				
				$auth = Zend_Auth::getInstance();
				
				$result = $auth->authenticate($adapter);
				
				if ($result->isValid())
				{
					$session = new Zend_Session_Namespace('square.auth');
					$session->user = $adapter->getResultArray('password');
					
					if (isset($session->requestURL))
					{
						$url = $session->requestURL;
						
						unset($session->requestURL);
						
						$this->_redirect($url);
					}
					else 
					{
						$this->_helper->getHelper('FlashMessenger')
							->addMessage('Jūs sėkmingai prisijungėte.');
							
						$this->_redirect('/admin/login/success');
					}
				}
				else 
				{
					$this->view->message = 'Nepavyko prisijungti. Bandykite dar kartą.';
				}
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
	
	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		
		Zend_Session::destroy();
		
		$this->_redirect('/admin/login');
	}
}