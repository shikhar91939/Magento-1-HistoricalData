<?php

class Overcart_Redmi_Block_Adminhtml_Sale_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected function _construct()
    {
        $this->_blockGroup = 'redmi';
        $this->_controller = 'adminhtml_sale';

        $this->_mode = 'edit';

        $newOrEdit = $this->getRequest()->getParam('id')
            ? $this->__('Edit')
            : $this->__('New');
        $this->_headerText =  $newOrEdit . ' ' . $this->__('Sale');
    }

    protected function _prepareLayout()
    {
        if ($this->_blockGroup && $this->_controller && $this->_mode)
        {
            $this->setChild('form', 
                    $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form')
                );
        }
        return parent::_prepareLayout();
    }
}