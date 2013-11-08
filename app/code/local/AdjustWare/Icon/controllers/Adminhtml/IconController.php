<?php
/**
 * Visualize Your Attributes
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Icon
 * @version      2.0.18
 * @license:     GPC7g2VHtpIP7j623srVjJmuippj4X9BeOkIuhMsJs
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Icon_Adminhtml_IconController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction(){
	    $this->loadLayout(); 
        $this->_setActiveMenu('catalog/attributes/adjicon');
        $this->_addBreadcrumb($this->__('Attribute Icons'), $this->__('Attribute Icons')); 
        $this->_addContent($this->getLayout()->createBlock('adjicon/adminhtml_icon')); 	    
 	    $this->renderLayout();
    }
    
	public function newAction() {
		$this->editAction();
	}
	
    public function editAction() {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('adjicon/attribute')->load($id);

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adjicon')->__('Attribute does not exist'));
			$this->_redirect('*/*/');
			return;
		}
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		
        //load attribute title
        if ($model->getId()){
            $attrModel = Mage::getModel('eav/entity_attribute')->load($model->getAttributeId());
            $model->setFrontendLabel($attrModel->getFrontendLabel());
        }

		Mage::register('adjicon_attribute', $model);

		$this->loadLayout();
		$this->_setActiveMenu('catalog/attributes/adjicon');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('adjicon/adminhtml_icon_edit'));
		$this->renderLayout();
	}

	public function saveAction()
	{
	    $id     = $this->getRequest()->getParam('id');
	    $model  = Mage::getModel('adjicon/attribute');
	    $data = $this->getRequest()->getPost();
        
        if ($data) {
            $model->setData($data)->setId($id);
			    
			try {
				$model->save();
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				$msg = Mage::helper('adjicon')->__('Attribute icons have been successfully saved');
                if (!$id) {
                    $msg = Mage::helper('adjicon')->__('Attribute has been successfully saved, feel free to upload icons');
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                
                if (!$id || $this->getRequest()->getParam('continue'))
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                else 
                    $this->_redirect('*/*/');
                                    
                return;
				
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adjicon')->__('Unable to find an item to save'));
        $this->_redirect('*/*/');
	} 
		
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('adjicon/attribute')->load($id);
        if (!$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adjicon')->__('Unable to find an item to delete'));
            $this->_redirect('*/*/');
            return;
        }
        
        try {
            $path = Mage::getBaseDir('media') . DS . 'icons' . DS;
            foreach ($model->getOptions() as $info){
    			$icon = Mage::getModel('adjicon/icon');
    			$icon->load($info['icon_id']);
    			$oldFile = $icon->getFilename();
    			if ($oldFile)
    			    unlink($path . $oldFile);
    			$icon->delete();
            }
            $model->delete();
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adjicon')->__('All attribute icons have been deleted'));
            $this->_redirect('*/*/');
           
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        }
    }

    public function deleteIconAction()
    {
        $id = $this->getRequest()->getParam('icon_id');
        $model = Mage::getModel('adjicon/icon')->load($id);
        if (!$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adjicon')->__('Unable to find an item to delete'));
            $this->_redirect('*/*/');
            return;
        }
        
        try {
            $path = Mage::getBaseDir('media') . DS . 'icons' . DS;
            $oldFile = $model->getFilename();
            if ($oldFile)
                unlink($path . $oldFile);
            $model->delete();
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adjicon')->__('Attribute icon has been deleted'));
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
    }

    
    
}