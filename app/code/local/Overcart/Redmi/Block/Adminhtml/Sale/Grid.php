<?php

class Overcart_Redmi_Block_Adminhtml_Sale_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('saleGrid');
      $this->setDefaultSort('entity_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('redmi/sale')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	 $this->addColumn('entity_id', array(
          'header'    => Mage::helper('redmi')->__('ID'),
          'align'     =>'center',
          'width'     => '30px',
          'index'     => 'entity_id',
      ));

     $this->addColumn('created_at', array(
          'header'    => Mage::helper('redmi')->__('Created At'),
          'width'     => '50px',
          'type'      => 'datetime',
          'index'     => 'created_at',
          'align'     =>'left',
      ));

      $this->addColumn('updated_at', array(
          'header'    => Mage::helper('redmi')->__('Updated At'),
          'width'     => '50px',
          'type'      => 'datetime',
          'index'     => 'update_at',
          'align'     =>'left',
      ));

      $this->addColumn('sale_code', array(
          'header'    => Mage::helper('redmi')->__('Sale Code'),
          'align'     =>'center',
          'width'     => '50px',
          'index'     => 'sale_code',
      ));

      $this->addColumn('sale_title', array(
			    'header'    => Mage::helper('redmi')->__('Sale Title'),
        	'align'     =>'left',
			    'width'     => '200px',	
			    'index'     => 'sale_title',
      ));
      
      $this->addColumn('sale_start_time', array(
    			'header'    => Mage::helper('redmi')->__('Sale Starts At'),
    		  'align'     =>'left',
          'type'      => 'datetime',
          'width'     => '80px',
    			'index'     => 'sale_start_time',
      ));

      $this->addColumn('sale_end_time', array(
          'header'    => Mage::helper('redmi')->__('Sale Ends At'),
          'align'     =>'left',
          'type'      => 'datetime',
          'width'     => '80px',
          'index'     => 'sale_end_time',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('redmi')->__('Status'),
          'align'     =>'left',
          'type'      => 'options',
          'width'     => '80px',
          'index'     => 'status',
          'options'   => Mage::getSingleton('redmi/status')->getOptionArray(),
      ));

      /**
         * Finally, we'll add an action column with an edit link.
         */
      $this->addColumn('action', array(
          'header' => Mage::helper('redmi')->__('Action'),
          'width' => '50px',
          'type' => 'action',
          'actions' => array(
              array(
                  'caption' => Mage::helper('redmi')->__('Edit'),
                  'url' => array(
                      'base' => 'redmiadmin'. '/adminhtml_sale/edit',
                  ),
                  'field' => 'id'
              ),
          ),
          'filter' => false,
          'sortable' => false,
          'index' => 'entity_id',
      ));

		$this->addExportType('*/*/exportCsv', Mage::helper('redmi')->__('CSV'));
	  
    return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
      $this->setMassactionIdField('entity_id');
      $this->getMassactionBlock()->setFormFieldName('sale');

      $this->getMassactionBlock()->addItem('delete', array(
           'label'    => Mage::helper('redmi')->__('Delete'),
           'url'      => $this->getUrl('*/*/massDelete'),
           'confirm'  => Mage::helper('redmi')->__('Are you sure?')
      ));

      $statuses = Mage::getSingleton('redmi/status')->getOptionArray();

      array_unshift($statuses, array('label'=>'', 'value'=>''));
     
      return $this;
  }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}