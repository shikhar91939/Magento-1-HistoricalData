<?php

class Overcart_Redmi_Model_Resource_Sale extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('redmi/sale', 'entity_id');
    }
}