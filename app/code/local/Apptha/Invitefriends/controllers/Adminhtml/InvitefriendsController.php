<?php

class Apptha_Invitefriends_Adminhtml_InvitefriendsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('invitefriends/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}	
 
	public function newAction() {
		$this->_forward('edit');
	}

        protected function _initCustomer($idFieldName = 'id')
    {
        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

        public function transactionAction()
	{
		$this->_initCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber')
            ->loadByCustomer(Mage::registry('current_customer'));

        Mage::register('subscriber', $subscriber);
        $this->getResponse()->setBody($this->getLayout()->createBlock('invitefriends/adminhtml_customer_edit_tab_invitefriends_grid')->toHtml());
	}
	
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('invitefriends/customer');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $invitefriendsIds = $this->getRequest()->getParam('invitefriends');
        if(!is_array($invitefriendsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($invitefriendsIds as $invitefriendsId) {
                    $invitefriends = Mage::getModel('invitefriends/customer')->load($invitefriendsId);
                    $invitefriends->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($invitefriendsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
   
  
    public function exportCsvAction()
    {
        $fileName   = 'invitefriends.csv';
        $content    = $this->getLayout()->createBlock('invitefriends/adminhtml_invitefriends_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'invitefriends.xml';
        $content    = $this->getLayout()->createBlock('invitefriends/adminhtml_invitefriends_grid')
            ->getXml();

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