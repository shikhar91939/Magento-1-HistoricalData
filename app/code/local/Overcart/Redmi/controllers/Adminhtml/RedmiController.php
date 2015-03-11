<?php

class Overcart_Redmi_Adminhtml_RedmiController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Flash Sale Registration'));
        $this->_addContent($this->getLayout()->createBlock('redmi/adminhtml_redmi'))
            ->_setActiveMenu('redmi/items');
        $this->renderLayout();
	}

    public function massDeleteAction() 
    {
        $entityIds = $this->getRequest()->getParam('redmi');
        if(!is_array($entityIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } 
        else 
        {
            try 
            {
                foreach ($entityIds as $id) 
                {
                    $entity = Mage::getModel('redmi/redmi')->load($id);
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

    public function massSendInformationalAction()
    {
        $entityIds = $this->getRequest()->getParam('redmi');
        if(!is_array($entityIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } 
        else 
        {
            try 
            {
                foreach ($entityIds as $id) 
                {
                    $entity = Mage::getModel('redmi/redmi')->load($id);
                    $entity->sendMailToUsers("info_email");
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d registered user(s) were emailed successfully', count($entityIds)
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

    public function massSaleNotifyAction()
    {
        $entityIds = $this->getRequest()->getParam('redmi');
        if(!is_array($entityIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } 
        else 
        {
            try 
            {
                foreach ($entityIds as $id)
                {
                    $entity = Mage::getModel('redmi/redmi')->load($id);
                    $entity->sendMailToUsers("sale_notify_email");
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d registered user(s) were emailed successfully', count($entityIds)
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
        $fileName   = 'registered_customers'.date('Y-m-d').'.csv';
        $content    = $this->getLayout()->createBlock('redmi/adminhtml_redmi_grid')
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