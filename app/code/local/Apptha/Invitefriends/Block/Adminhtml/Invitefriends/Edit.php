<?php

class Apptha_Invitefriends_Block_Adminhtml_Invitefriends_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'invitefriends';
        $this->_controller = 'adminhtml_invitefriends';
        
        $this->_updateButton('save', 'label', Mage::helper('invitefriends')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('invitefriends')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('invitefriends_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'invitefriends_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'invitefriends_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('invitefriends_data') && Mage::registry('invitefriends_data')->getId() ) {
            return Mage::helper('invitefriends')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('invitefriends_data')->getTitle()));
        } else {
            return Mage::helper('invitefriends')->__('Add Item');
        }
    }
}