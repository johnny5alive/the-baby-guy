<?php
class Apptha_Invitefriends_Block_Adminhtml_Invitefriends extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_invitefriends';
    $this->_blockGroup = 'invitefriends';
    $this->_headerText = Mage::helper('invitefriends')->__('Manage Invite Friends');
    $this->_addButtonLabel = Mage::helper('invitefriends')->__('Add Item');
    parent::__construct();
    $this->_removeButton('add');
  }
}