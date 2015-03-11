<?php

class Overcart_Redmi_Model_Resource_Redmi extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('redmi/redmi', 'entity_id');
    }
}