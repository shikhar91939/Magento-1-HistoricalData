<?php

class Overcart_Redmi_Model_Sale extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('redmi/sale');
    }
}