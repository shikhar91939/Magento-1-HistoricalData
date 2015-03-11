<?php

class Overcart_Redmi_Adminhtml_SaleController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Flash Sale Management'));
        $this->_addContent($this->getLayout()->createBlock('redmi/adminhtml_sale'))
            ->_setActiveMenu('redmi/sale');
        $this->renderLayout();
	}

    public function editAction()
    {
        $saleId     = $this->getRequest()->getParam('id');

        $saleModel  = Mage::getModel('redmi/sale')->load($saleId);

        if ($saleModel->getId() || $saleId == 0)
        {
            Mage::register('current_sale', $saleModel);

            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Edit Sale Page'));
            $this->_addContent($this->getLayout()->createBlock('redmi/adminhtml_sale_edit'));
            $this->renderLayout();
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('redmi')->__('Sale does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        $saleId = $this->getRequest()->getParam('id');

        if ($data = $this->getRequest()->getPost()) 
        {
            try
            {
                $saleModel = Mage::getModel('redmi/sale');

                if(!$saleId)
                {
                    $data['created_at'] = date('Y-m-d h:i:s');
                    $data['update_at'] = date('Y-m-d h:i:s');
                    $saleModel->setData($data);
                    $saleModel->save();
                }
                else
                {
                    $data['update_at'] = date('Y-m-d h:i:s');
                    $saleModel->load($saleId);
                    $saleModel->addData($data);
                    $saleModel->save();
                }
               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('redmi')->__('Sale was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                if ($this->getRequest()->getParam('back')) 
                {
                    $this->_redirect('*/*/edit', array('id' => $saleId));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            }

            catch (Mage_Core_Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCurrentSale($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) 
            {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('redmi')->__('There was a problem saving the sale.'));
                Mage::getSingleton('adminhtml/session')->setCurrentSale($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('redmi')->__('Unable to find sale to save.'));
        $this->_redirect('*/*/');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function massDeleteAction() 
    {
        $entityIds = $this->getRequest()->getParam('sale');
        if(!is_array($entityIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } 
        else 
        {
            try 
            {
                foreach ($entityIds as $id) 
                {
                    $entity = Mage::getModel('redmi/sale')->load($id);
                    $entity->delete();
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($entityIds)
                    )
                );
            } 
            catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

	public function exportCsvAction()
    {
        $fileName   = 'flash_sales'.date('Y-m-d').'.csv';
        $content    = $this->getLayout()->createBlock('redmi/adminhtml_sale_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}