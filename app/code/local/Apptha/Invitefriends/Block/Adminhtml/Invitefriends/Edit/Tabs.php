<?php

class Apptha_Invitefriends_Block_Adminhtml_Invitefriends_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('invitefriends_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('invitefriends')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('invitefriends')->__('Item Information'),
          'title'     => Mage::helper('invitefriends')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('invitefriends/adminhtml_invitefriends_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}