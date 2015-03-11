<?php

class Overcart_Redmi_Block_Adminhtml_Redmi extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
	    $this->_controller = 'adminhtml_redmi';
	    $this->_blockGroup = 'redmi';
	    $this->_headerText = Mage::helper('redmi')->__('Registered Customers for Redmi Sale');
	    parent::__construct();
	    $this->_removeButton('add');
	}
}