<?php

class Apptha_Invitefriends_Block_Adminhtml_Invitefriends_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('invitefriends_form', array('legend'=>Mage::helper('invitefriends')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('invitefriends')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('invitefriends')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('invitefriends')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('invitefriends')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('invitefriends')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('invitefriends')->__('Content'),
          'title'     => Mage::helper('invitefriends')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getInvitefriendsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getInvitefriendsData());
          Mage::getSingleton('adminhtml/session')->setInvitefriendsData(null);
      } elseif ( Mage::registry('invitefriends_data') ) {
          $form->setValues(Mage::registry('invitefriends_data')->getData());
      }
      return parent::_prepareForm();
  }
}