<?php

class Overcart_Redmi_Model_Redmi extends Mage_Core_Model_Abstract
{

	const XML_PATH_EMAIL_SENDER                  = 'redmi/email/sender_email_identity';

    const XML_PATH_SIGNUP_EMAIL_TEMPLATE         = 'redmi/email/email_template';

    const XML_PATH_INFO_EMAIL_TEMPLATE           = 'redmi/email/info_email_template';

    const XML_PATH_SALE_NOTIFY_TEMPLATE          = 'redmi/email/sale_notify_template';

    const XML_PATH_ENABLED                       = 'redmi/contacts/enabled';

	public function _construct()
    {
        parent::_construct();
        $this->_init('redmi/redmi');
    }

    protected function getWebsiteId()
	{
		return Mage::app()->getWebsite()->getId();
	}

	protected function getStore()
	{
		return Mage::app()->getStore();
	}

    protected function _getCustomerName( $email )
	{
		return Mage::getModel('customer/customer')
				->setWebsiteId($this->getWebsiteId())
				->setStore($this->getStore())
				->loadByEmail($email)
				->getName();
	}

    protected function _sendMail($sender, $template, $email, $data)
    {
    	if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) )
		{
			return false;
		}

    	$mailTemplate = Mage::getModel('core/email_template');

		/* @var $mailTemplate Mage_Core_Model_Email_Template */
		$mailTemplate->setDesignConfig(array('area' => 'frontend'))
			->setReplyTo($sender)
			->sendTransactional(
				$template,
				$sender,
				$email,
				null,
				array('name' => $data)
			);
		
		if ($mailTemplate->getSentSuccess()) 
		{
			return true;
		}
		else
		{
			return false;
		}
    }


    public function updateRegistration($orderId)
    {
        if(isset($orderId))
        {
            $this->setData('purchase_status','YES');
            $this->setData('order_id', $orderId);
            $this->save();

            return true;
        }
        else
        {
            return false;
        }
    }

    public function sendMailToUsers($type)
    {
    	if(isset($type))
    	{
    		$email = $this->getEmailId();

    		$name = $this->_getCustomerName($email);

    		switch ($type) {
    			case 'info_email':
    				$mailSendStatus = $this->_sendMail(
    					Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
    					Mage::getStoreConfig(self::XML_PATH_INFO_EMAIL_TEMPLATE),
    					$email,
    					$name
    				);

    				if($mailSendStatus)
    				{
    					$this->setData('info_mail_send','Yes');
    					$this->save();
    				}
    				break;

    			case 'sale_notify_email':
    				$mailSendStatus = $this->_sendMail(
    					Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
    					Mage::getStoreConfig(self::XML_PATH_SALE_NOTIFY_TEMPLATE),
    					$email,
    					$name
    				);
    				if($mailSendStatus)
    				{
    					$this->setData('sale_email_send','Yes');
    					$this->save();
    				}
    				break;
    			
    			default:
    				break;
    		}
    	}
    }
}