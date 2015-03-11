<?php
class Overcart_Nudgespot_Model_Observer 
{
	public function registerSuccess($observer)
	{
		$event = $observer->getEvent();

		$customer = $event->getCustomer();

		Zend_debug::dump($customer->getEmail());
		Zend_debug::dump($customer->getName());
		Zend_debug::dump($customer->getFirstname());
		Zend_debug::dump($customer->getLastname());

		Mage::getModel('core/session')->setCusomerDetails(
			new Varien_Object(array(
		        'cust_email' => $customer->getEmail(),
		        'cust_name' => $customer->getName(),
		        //add more values
		    ))
		);
		// Zend_Debug::dump(Mage::getModel('core/session')->getCusomerDetails());
		// die;


	}
	
	public function orderSuccess($observer)
	{
		 $orderIds = $observer->getData('order_ids');

        foreach($orderIds as $_orderId){
            $order = Mage::getModel('sales/order')->load($_orderId);

        }
        Zend_debug::dump($order);
        Zend_debug::dump($orderIds);
        die;

	}

	private function sendToNudgespot($observer){

	}
}