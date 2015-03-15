<?php
class Overcart_OrderTimeline_Adminhtml_BrandController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Instantiate our grid container block and add to the page content.
     * When accessing this admin index page, we will see a grid of all
     * brands currently available in our Magento instance, along with
     * a button to add a new one if we wish.
     */
    public function indexAction()
    {
        // instantiate the grid container
        $brandBlock = $this->getLayout()
            ->createBlock('overcart_ordertimeline_adminhtml/brand');

        // Add the grid container as the only item on this page
        $this->loadLayout()
            ->_addContent($brandBlock)
            ->renderLayout();
    }

    /**
     * This action handles both viewing and editing existing brands.
     */
    public function editAction()
    {
        /**
         * Retrieve existing brand data if an ID was specified.
         * If not, we will have an empty brand entity ready to be populated.
         */
        $brand = Mage::getModel('overcart_ordertimeline/brand');
        if ($brandId = $this->getRequest()->getParam('id', false)) {
            $brand->load($brandId);

            if ($brand->getId() _getSession()->addError(
                    $this->__('This brand no longer exists.')
                );
                return $this->_redirect(
                    'overcart_ordertimeline_admin/brand/index'
                );
            }
        }

        // process $_POST data if the form was submitted
        if ($postData = $this->getRequest()->getPost('brandData')) {
            try {
                $brand->addData($postData);
                $brand->save();

                $this->_getSession()->addSuccess(
                    $this->__('The brand has been saved.')
                );

                // redirect to remove $_POST data from the request
                return $this->_redirect(
                    'overcart_ordertimeline_admin/brand/edit',
                    array('id' => $brand->getId())
                );
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }

            /**
             * If we get to here, then something went wrong. Continue to
             * render the page as before, the difference this time being
             * that the submitted $_POST data is available.
             */
        }

        // Make the current brand object available to blocks.
        Mage::register('current_brand', $brand);

        // Instantiate the form container.
        $brandEditBlock = $this->getLayout()->createBlock(
            'overcart_ordertimeline_adminhtml/brand_edit'
        );

        // Add the form container as the only item on this page.
        $this->loadLayout()
            ->_addContent($brandEditBlock)
            ->renderLayout();
    }

    public function deleteAction()
    {
        $brand = Mage::getModel('overcart_ordertimeline/brand');

        if ($brandId = $this->getRequest()->getParam('id', false)) {
            $brand->load($brandId);
        }

        if ($brand->getId() _getSession()->addError(
                $this->__('This brand no longer exists.')
            );
            return $this->_redirect(
                'overcart_ordertimeline_admin/brand/index'
            );
        }

        try {
            $brand->delete();

            $this->_getSession()->addSuccess(
                $this->__('The brand has been deleted.')
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            'overcart_ordertimeline_admin/brand/index'
        );
    }

    /**
     * Thanks to Ben for pointing out this method was missing. Without
     * this method the ACL rules configured in adminhtml.xml are ignored.
     */
    protected function _isAllowed()
    {
        /**
         * we include this switch to demonstrate that you can add action
         * level restrictions in your ACL rules. The isAllowed() method will
         * use the ACL rule we have configured in our adminhtml.xml file:
         * - acl
         * - - resources
         * - - - admin
         * - - - - children
         * - - - - - smashingmagazine_branddirectory
         * - - - - - - children
         * - - - - - - - brand
         *
         * eg. you could add more rules inside brand for edit and delete.
         */
        $actionName = $this->getRequest()->getActionName();
        switch ($actionName) {
            case 'index':
            case 'edit':
            case 'delete':
                // intentionally no break
            default:
                $adminSession = Mage::getSingleton('admin/session');
                $isAllowed = $adminSession
                    ->isAllowed('overcart_ordertimeline/brand');
                break;
        }

        return $isAllowed;
    }
}