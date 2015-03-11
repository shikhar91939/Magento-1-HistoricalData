<?php

class Overcart_Redmi_Block_Adminhtml_Redmi_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('redmiGrid');
      $this->setDefaultSort('entity_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('redmi/redmi')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	 $this->addColumn('entity_id', array(
          'header'    => Mage::helper('redmi')->__('ID'),
          'align'     =>'center',
          'width'     => '50px',
          'index'     => 'entity_id',
      ));

      $this->addColumn('product_id', array(
          'header'    => Mage::helper('redmi')->__('Product ID'),
          'align'     =>'center',
          'width'     => '50px',
          'index'     => 'product_id',
      ));

      $this->addColumn('product_name', array(
			    'header'    => Mage::helper('redmi')->__('Product Name'),
        	'align'     =>'left',
			    'width'     => '200px',	
			    'index'     => 'product_name',
      ));

      $this->addColumn('sale_code', array(
			    'header'    => Mage::helper('redmi')->__('Sale Code'),
        		'align'     =>'left',
			    'width'     => '80px',	
			    'index'     => 'sale_code',
      ));
      
      $this->addColumn('email_id', array(
    			'header'    => Mage::helper('redmi')->__('Customer Email Id'),
    		  'align'     =>'left',
          'width'     => '50px',
    			'index'     => 'email_id',
      ));

      $this->addColumn('mailsend_status', array(
    			'header'    => Mage::helper('redmi')->__('Register Mail Sent'),
    			'width'     => '50px',
    			'index'     => 'mailsend_status',
  	   		'align'     =>'center',
      ));

      $this->addColumn('info_mail_send', array(
    			'header'    => Mage::helper('redmi')->__('Info Mail Sent'),
    			'width'     => '50px',
    			'index'     => 'info_mail_send',
  	   		'align'     =>'center',
      ));

      $this->addColumn('sale_email_send', array(
    			'header'    => Mage::helper('redmi')->__('Sale Notified'),
    			'width'     => '50px',
    			'index'     => 'sale_email_send',
  	   		'align'     =>'center',
      ));

      $this->addColumn('Registration Added', array(
    			'header'    => Mage::helper('redmi')->__('Registration Added'),
    			'width'     => '50px',
          'type' => 'datetime',
    			'index'     => 'created_time',
    	   	'align'     =>'left',
      ));

      $this->addColumn('purchase_status', array(
    			'header'    => Mage::helper('redmi')->__('Purchased'),
    			'width'     => '50px',
    			'index'     => 'purchase_status',
  	   		'align'     =>'center',
      ));

      $this->addColumn('order_id', array(
          'header'    => Mage::helper('redmi')->__('Order #'),
          'width'     => '50px',
          'index'     => 'order_id',
          'align'     =>'center',
      ));

		$this->addExportType('*/*/exportCsv', Mage::helper('redmi')->__('CSV'));
	  
    return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
      $this->setMassactionIdField('entity_id');
      $this->getMassactionBlock()->setFormFieldName('redmi');

      $this->getMassactionBlock()->addItem('delete', array(
           'label'    => Mage::helper('redmi')->__('Delete'),
           'url'      => $this->getUrl('*/*/massDelete'),
           'confirm'  => Mage::helper('redmi')->__('Are you sure?')
      ));

      $this->getMassactionBlock()->addItem('send_info_email', array(
           'label'    => Mage::helper('redmi')->__('Send informational email'),
           'url'      => $this->getUrl('*/*/massSendInformational'),
           'confirm'  => Mage::helper('redmi')->__('Are you sure?')
      ));

      $this->getMassactionBlock()->addItem('send_sale_email', array(
           'label'    => Mage::helper('redmi')->__('Send Sale Notification'),
           'url'      => $this->getUrl('*/*/massSaleNotify'),
           'confirm'  => Mage::helper('redmi')->__('Are you sure?')
      ));

      $statuses = Mage::getSingleton('redmi/status')->getOptionArray();

      array_unshift($statuses, array('label'=>'', 'value'=>''));
     
      return $this;
  }

  public function getRowUrl($row)
  {
    //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}