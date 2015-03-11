<?php

class Overcart_Redmi_Model_Resource_Redmi_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('redmi/redmi');
    }
}