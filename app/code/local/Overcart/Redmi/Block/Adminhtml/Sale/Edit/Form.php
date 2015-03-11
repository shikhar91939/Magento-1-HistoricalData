<?php

class Overcart_Redmi_Block_Adminhtml_Sale_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
        // Instantiate a new form to display our brand for editing.
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        // Define a new fieldset. We need only one for our simple entity.
        $fieldset = $form->addFieldset('general', array('legend' => $this->__('Sale Details')));

        // Add the fields that we want to be editable.
        $fieldset->addField('sale_code', 'text', array(
                'label'     => $this->__('Sale Code'),
                'input'     => 'text',
                'required'  => true,
                'name'      => 'sale_code',
            )
        );

        $fieldset->addField('sale_title', 'text', array(
                'label'     => $this->__('Sale Title'),
                'input'     => 'text',
                'required'  => true,
                'name'      => 'sale_title',
            )
        );

        $fieldset->addField('sale_start_time', 'date', array(
                'label'     =>  $this->__('Sale Starts At'),
                'time'      =>  true,
                'class'     =>  'required-entry',
                'required'  =>  true,        
                'format'    =>  $this->escDates(),
                'image'     =>  $this->getSkinUrl('images/grid-cal.gif'),
                'name'      => 'sale_start_time',
            )
        );

        $fieldset->addField('sale_end_time', 'date', array(
                'label'     =>  $this->__('Sale Ends At'),
                'time'      =>  true,
                'class'     =>  'required-entry',
                'required'  =>  true,        
                'format'    =>  $this->escDates(),
                'image'     =>  $this->getSkinUrl('images/grid-cal.gif'),
                'name'      => 'sale_end_time',
            )
        );

        $fieldset->addField('status', 'select', array(
                'label' => $this->__('Status'),
                'name' => 'status',
                'required' => true,
                'values' => Mage::getSingleton('redmi/status')->getOptionArray(),
            )
        );
        
        if (Mage::getSingleton('adminhtml/session')->getCurrentSale()) 
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCurrentSale());
            Mage::getSingleton('adminhtml/session')->setCurrentSale(null);
        }
        elseif (Mage::registry('current_sale')) 
        {
            $form->setValues(Mage::registry('current_sale')->getData());
        }
        
        return parent::_prepareForm();
    }

    private function escDates() 
    {
        return 'yyyy-MM-dd HH:mm:ss';   
    }
}