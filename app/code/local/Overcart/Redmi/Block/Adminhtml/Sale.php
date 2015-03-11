<?php

class Overcart_Redmi_Block_Adminhtml_Sale extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_sale';
	    $this->_blockGroup = 'redmi';
	    $this->_headerText = Mage::helper('redmi')->__('Manage Flash Sales');
	    parent::__construct();
	}
}