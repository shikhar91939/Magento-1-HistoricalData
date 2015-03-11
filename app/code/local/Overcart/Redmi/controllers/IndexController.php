<?php

/**
* Index Controller for Overcart Redmi Registrations
* Created on : 26th Dec, 2014
* @author: Anil Jaiswal
*/

class Overcart_Redmi_IndexController extends Mage_Core_Controller_Front_Action
{

	CONST INSERT_SUCCESS = 0;

	CONST INSERT_FAILED = 1;

	CONST INSERT_DUPLICATE = 2;

	CONST INSERT_EXCEPTION = 3;

    const XML_PATH_EMAIL_SENDER     = 'redmi/email/sender_email_identity';

    const XML_PATH_EMAIL_TEMPLATE   = 'redmi/email/email_template';

    const XML_PATH_ENABLED          = 'redmi/contacts/enabled';

	/**
	* Store ID var
	* @var $_storeId
	*/ 
	protected $_storeId = null;

	/**
	* Website ID
	* @var $_websiteId
	*/
	protected $_websiteId = null;

	/**
	* Stores session information
	* @var $_session
	*/
	protected $_session = null;

	/**
	* IsLoggedIn flag
	* @var $_isLoggedIn
	*/
	protected $_isLoggedIn = false;

	/**
	* Customer Object
	* @var Mage_Customer_Model_Customer
	*/
	protected $_customer = null;

	/**
	* Registration Model Object
	* @var Overcart_Redmi_Model_Redmi
	*/
	protected $_registerModel = null;

	/**
	* Return Website Id
	* @return $_websiteId
	*/
	protected function getWebsiteId()
	{
		if(!isset($this->_websiteId))
		{
			$this->_websiteId = Mage::app()->getWebsite()->getId();
		}
		
		return $this->_websiteId;
	}

	/**
	* Return Store Id
	* @return $_storeId
	*/
	protected function getStore()
	{
		if(!isset($this->_storeId))
		{
			$this->_storeId = Mage::app()->getStore();	
		}
		
		return $this->_storeId;
	}

	/**
	* Return Session Object
	* @return $_session;
	*/
	protected function getSession()
	{
		if(!isset($this->_session))
		{
			$this->_session = Mage::getSingleton('customer/session');
		}

		return $this->_session;
	}

	/**
	* Check if User is Logged in
	* @return $_isLoggedIn
	*/
	protected function isLoggedIn()
	{	
		if($this->getSession()->isLoggedIn())
		{
			$this->_isLoggedIn = true;
		}

		return $this->_isLoggedIn;
	}

	/**
	* Function to get Customer Data
	* @param string $email
	* @return $customer Mage_Customer_Model_Customer
	*/
	protected function getCustomer( $email )
	{
		if(!isset($this->_customer))
		{
			$this->_customer = Mage::getModel('customer/customer');
			$this->_customer->setWebsiteId($this->getWebsiteId());
			$this->_customer->setStore($this->getStore());
			$this->_customer->loadByEmail($email);	
		}

		return $this->_customer;
	}

	/**
	* Function to return Redmi Registration Model Object
	* @return Overcart_Redmi_Model_Redmi
	*/
	protected function getRegistrationModel()
	{
		if(!isset($this->_registerModel))
		{
			$this->_registerModel = Mage::getModel('redmi/redmi');	
		}

		return  $this->_registerModel;
	}

	/**
	* Helper Function to return JSON Response Headers
	* @param response array
	* @return json_encoded string
	*/
	protected function returnResponse($response)
	{
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        
        $this->getResponse()->setBody(json_encode($response));
	}

	/**
	* Helper Function to send Successful Registration Email
	* @param $email Email Address of recipient
	* @return bool
	*/
	protected function sendSuccessMail($email)
	{
		if(!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED))
		{
			return false;
		}
		
		$dataObject = new Varien_Object();
		
		$dataObject->setName($this->getCustomer($email)->getName());

		$mailTemplate = Mage::getModel('core/email_template');

		/* @var $mailTemplate Mage_Core_Model_Email_Template */
		$mailTemplate->setDesignConfig(array('area' => 'frontend'))
			->setReplyTo(Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER))
			->sendTransactional(
				Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
				Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
				$email,
				null,
				array('data' => $dataObject)
			);

		if (!$mailTemplate->getSentSuccess()) 
		{
			throw new Exception();
		}

		return true;
	}

	/**
	* Function to Save Customer Registration Data for sale
	* @param array $data
	* @return insertId
	*/
	protected function saveRegistration($data)
	{
		$dataToSave = array('product_id'    => $data['productId'],
						    'product_name'  => $data['product_name'],
						    'email_id'	    => $data['email'],
						    'created_time'  => date('Y-m-d H:i:s', strtotime("now")),
						    'update_time'   => date('Y-m-d H:i:s', strtotime("now"))
					  );

		try
		{
			$collection = $this->getRegistrationModel()
								->getCollection()
								->addFieldToFilter('product_id', $data['productId'])
								->addFieldToFilter('email_id', $data['email']);
			
			$ids = array();

			foreach ($collection as $row) 
			{
				$ids[] = $row->getId();
			}

			if(! count($ids) > 0 )
			{
				$insertId = $this->getRegistrationModel()->setData($dataToSave)->save()->getId();

				if($insertId)
				{
					/* Send success email to customer */
					$mailSendStatus = $this->sendSuccessMail($data['email']);

					if($mailSendStatus)
					{
						try
						{
							$redmiModel = $this->getRegistrationModel()->load( $insertId, 'entity_id' );
							$redmiModel->addData(array("mailsend_status"=>"Yes"));
							$redmiModel->setId($insertId);
							$redmiModel->save();
						}
						catch(Exception $e)
						{
							Mage::log($e->getMessage());
						}
					}
					return self::INSERT_SUCCESS;
				}
				else
				{
					return self::INSERT_FAILED;
				}	
			}
			else
			{
				return self::INSERT_DUPLICATE;
			}
		}
		catch (Exception $e)
		{
			Mage::logException($e->getMessage());
			return self::INSERT_EXCEPTION;
		}
	}

	/**
	* Controller Action to process Login request
	* and send back ajax response
	*/
	public function loginAction()
	{
		if ($this->getRequest()->isPost())
		{
            if($this->isLoggedIn())
			{
				$response = array('status'=>"error", "msg"=>'You\'re already logged in.');
	            $this->returnResponse($response);
			}
			else
			{
				$login = $this->getRequest()->getPost('login');
            
	            if (!empty($login['username']) && !empty($login['password'])) 
	            {
	                try
	                {
	                    $this->getSession()->login($login['username'], $login['password']);
	                    $this->getSession()->setCustomerAsLoggedIn($this->getCustomer($login['username']));
	                    $customer_id = $this->getSession()->getCustomerId();
						if ( $customer_id > 0 )
						{
							$response = array('status'=>"ok", "msg"=>"Login Successful!");
							$this->returnResponse($response);	
						}
						else
						{
							$response = array('status'=>"error", "msg"=>"Login failed! Please try again");
	                    	$this->returnResponse($response);
						}
	                } 
	                catch (Mage_Core_Exception $e) 
	                {
	                    switch ($e->getCode())
	                    {
	                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
	                            $message = $e->getMessage();
	                            break;
	                        default:
	                            $message = $e->getMessage();
	                    }
	                    $response = array('status'=>"error", "msg"=>$message);
	                    $this->returnResponse($response);
	                } 
	                catch (Exception $e) 
	                {
	                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
	                }
	            } 
	            else 
	            {
	                $response = array('status'=>"error", "msg"=>'Login and password are required.');
	                $this->returnResponse($response);
	            }
			}
        }
        else
        {
            $response = array('status'=>"error", "msg"=>'Method not allowed.');
            $this->returnResponse($response);
        }
    }

    /**
	* Controller Action to process Account Creation request
	* and send back ajax response
	*/
	public function createAction()
	{
		if ($this->getRequest()->isPost())
		{
            if($this->isLoggedIn())
			{
				$response = array('status'=>"error", "msg"=>'You\'re already logged in.');
				$this->returnResponse($response);
			}
			else
			{
				$errors = array();

				if (!$customer = Mage::registry('current_customer')) 
				{
					$customer = Mage::getModel('customer/customer')->setId(null);
				}

				/* @var $customerForm Mage_Customer_Model_Form */
				$customerForm = Mage::getModel('customer/form');
				$customerForm->setFormCode('customer_account_create')
					->setEntity($customer);
				
				$customerData = $customerForm->extractData($this->getRequest());

				if ($this->getRequest()->getParam('is_subscribed', false)) 
				{
					$customer->setIsSubscribed(1);
				}
				
				/**
				 * Initialize customer group id
				 */
				$customer->getGroupId();

				try
				{
					$customerErrors = $customerForm->validateData($customerData);

					if ($customerErrors !== true) 
					{
						$errors = array_merge($customerErrors, $errors);
					}
					else
					{
						$customerForm->compactData($customerData);
						$customer->setPassword($this->getRequest()->getPost('password'));
						$customer->setConfirmation($this->getRequest()->getPost('confirmation'));
						$customerErrors = $customer->validate();
						if (is_array($customerErrors)) 
						{
							$errors = array_merge($customerErrors, $errors);
						}
					}

					$validationResult = count($errors) == 0;

					if (true === $validationResult) 
					{
						$customer->save();

						Mage::dispatchEvent('customer_register_success',
							array('account_controller' => $this, 'customer' => $customer)
						);

						$this->getSession()->setCustomerAsLoggedIn($customer);
						
						$response = array('status'=>"ok", "msg"=>'Your account has been created successfully.');
						$this->returnResponse($response);
					}
					else
					{
						if (is_array($errors)) 
						{
							$response = array('status'=>"multi_error", "msg"=>$errors);
							$this->returnResponse($response);
						}
						else
						{
							$response = array('status'=>"invalid", "msg"=>'Invalid customer data.');
							$this->returnResponse($response);
						}
					}
				}
				catch (Mage_Core_Exception $e) 
				{
					if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) 
					{
						$url = Mage::getUrl('customer/account/forgotpassword');
						$message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
						$response = array('status'=>"customer_exists", "msg"=>$message);
						$this->returnResponse($response);
					} 
					else 
					{
						$message = $e->getMessage();
						$response = array('status'=>"core_exception", "msg"=>$message);
						$this->returnResponse($response);
					}
				}
				catch (Exception $e) 
				{
					$response = array('status'=>"exception", "msg"=>"Cannot save the customer.");
					$this->returnResponse($response);
				}
			} //if not logged in
		}
		else
		{
			$response = array('status'=>"error", "msg"=>'Method not allowed.');
			$this->returnResponse($response);
		}
	}

	/**
	* Process sale registration request and send response
	*/
	public function registerCustomerAction()
	{
		if ($this->getRequest()->isPost())
		{
            if($this->isLoggedIn())
			{
				$request = $this->getRequest()->getParams();

				if( isset($request['email']) && isset($request['productId']) && isset($request['productName']) )
				{
					try
					{
						$customer = $this->getCustomer($request['email']);

						if($customer->getId()>0)
						{
							$data = array('productId'    => $request['productId'],
							  			  'product_name' => $request['productName'],
							  			  'email'	 => $request['email']
							  			 );
							$saveStatus = $this->saveRegistration($data);

							switch ($saveStatus) 
							{
								case self::INSERT_SUCCESS:
									$response = array("status"=>"ok", "msg"=>"Congratulations! You have been successfully registered.");
									$this->returnResponse($response);
									break;
								case self::INSERT_DUPLICATE:
									$response = array("status"=>"error", "msg"=>"You're already registered.");
									$this->returnResponse($response);
									break;
								case self::INSERT_FAILED:
									$response = array("status"=>"error", "msg"=>"Registration failed. Please try again.");
									$this->returnResponse($response);
									break;
								case self::INSERT_EXCEPTION:
									$response = array("status"=>"error", "msg"=>"We're experiencing problems. Please try again later.");
									$this->returnResponse($response);
									break;
								default:
									$response = array("status"=>"error", "msg"=>"Something went wrong. Please try again.");
									$this->returnResponse($response);
									break;
							}
						}
						else
						{
							$response = array("status"=>"error", "msg"=>"Customer email address not found.");
							$this->returnResponse($response);
						}
					}
					catch(Exception $e)
				    {
					    $response = array("status"=>"error", "msg" => $e->getMessage());
					    $this->returnResponse($response);
				    }
				}
				else
				{	
					$response = array("status"=>"error","msg"=>"Bad request. Please provide user and product details.");
					$this->returnResponse($response);
				}
			}
			else
			{
				$response = array('status'=>"error", "msg"=>'You need to log in to register for sale.');
	            $this->returnResponse($response);
			}
		}
		else
		{
			$response = array("status"=>"error","msg"=>"method not allowed.");
			$this->returnResponse($response);
		}
	}
}