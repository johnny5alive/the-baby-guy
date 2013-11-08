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
class AdjustWare_Icon_Block_Adminhtml_Icon_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
        //create form structure
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $hlp     = Mage::helper('adjicon');
        $model   = Mage::registry('adjicon_attribute');
        
        $fldInfo = $form->addFieldset('adjicon_info', array('legend'=> $hlp->__('Attribute')));
        if ($model->getAttributeId()){
            $fldInfo->addField('frontend_label', 'text', array(
              'label'     => $hlp->__('Attribute'),
              'required'  => false,
              'name'      => 'frontend_label',
    	      'readonly'  => true,
    	      'disabled'  => true,
            ));
            $fldInfo->addField('attribute_id', 'hidden', array(
              'name'      => 'attribute_id',
            ));
        }
        else {
            $fldInfo->addField('attribute_id', 'select', array( 
                'label'     => $hlp->__('Attribute'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'attribute_id',
                'values'    => $model->getAvailableAttributesAsOptions(),
            ));
        }        
        
        $fldInfo->addField('pos', 'text', array(
          'label'     => $hlp->__('Sorting Order'),
          'class'     => 'validate-number',
          'name'      => 'pos',
        ));
        
        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
        ));

        $fldLayer = $form->addFieldset('adjicon_layer', array('legend'=> $hlp->__('Layered Navigation')));
        $fldLayer->addField('show_images', 'select', array(
          'label'    => $hlp->__('Show Icons'),
          'name'     => 'show_images',
          'values'   => $yesno,
        ));
        $fldLayer->addField('hide_qty', 'select', array(
          'label'    => $hlp->__('Hide Quantities '),
          'name'     => 'hide_qty',
          'values'   => $yesno,
        ));
        $fldLayer->addField('columns_num', 'select', array(
          'label'     => $hlp->__('Display'),
          'name'      => 'columns_num',
          'values'    => array(
            array('value'=>1, 'label'=>$hlp->__('Labels and Icons, One Column')),
            array('value'=>2, 'label'=>$hlp->__('Labels and Icons, Two Columns')),
            array('value'=>3, 'label'=>$hlp->__('Icons Only')),
            ),
        ));
        
        
        
        $options = $model->getOptions();
        if ($options){
            $fldOpt = $form->addFieldset('adjicon_opt', array('legend'=> $hlp->__('Icons')));
            foreach ($options as $info){
                $html = '';
                if (!empty($info['filename'])){
                    $html .= '<p style="margin-top: 5px">';
                    $html .= '<img src="'.Mage::getBaseUrl('media') . 'icons/' . $info['filename'].'" />';
                    $html .= ' &nbsp; ';
                    $html .= '<img src="'.Mage::getBaseUrl('media') . 'icons/' . 's_' . $info['filename'].'" valign="top"/>';
                    $html .= '<br />';
                    $html .= '<a onclick="return confirm(\''.$hlp->__('Are you sure?').'\')" href="'.$this->getUrl('*/*/deleteIcon', array('id'=>$model->getId(), 'icon_id'=>$info['icon_id'])).'">';
                    $html .= $hlp->__('Delete');
                    $html .= '</a></p>';
                }
                
                $fldOpt->addField('option_' . $info['option_id'], 'file', array(
                    'label'     => $info['value'],
                    'name'      => 'option_'. $info['option_id'],
                    'required'  => false,
                    'after_element_html' => $html, 
                ));
            }
           
        }
        
        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if ($data) {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($model) {
            $form->setValues($model->getData());
        }
        
        return parent::_prepareForm();
  }
}