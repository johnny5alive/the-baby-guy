<?php
class Apptha_Invitefriends_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml()
    {       
        $this->addTab('invitefriends', array(
            'label'     => Mage::helper('invitefriends')->__('Credits'),
            'content'   => $this->getLayout()->createBlock('invitefriends/adminhtml_customer_edit_tab_invitefriends')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));
        $this->_updateActiveTab();
        Varien_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }
}
