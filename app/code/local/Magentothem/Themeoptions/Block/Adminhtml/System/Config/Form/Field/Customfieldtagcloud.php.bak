<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Dresscodeconfig_Block_Adminhtml_System_Config_Form_Field_Customfieldtagcloud extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
	    $real_val = $element->getValue();
        $many_val = $element->getValues();
        $output = '';
        if ($many_val) {
            foreach ($many_val as $curr_val) {
                $output.= '<div class="f-left"><img src="'.Mage::getDesign()->getSkinUrl('images/etheme/dresscode/tagcloud_type_'.$curr_val['value'].'.gif').'"/>
                <div class="a-center">
                <input type="radio"'.$element->serialize(array( 'style', 'class','name'));
                if ($curr_val instanceof Varien_Object) {
                    $output.= 'id="'.$element->getHtmlId().$curr_val->getValue().'" '.$curr_val->serialize(array('title', 'label', 'style',  'class', 'value'));
                    if (in_array($curr_val->getValue(), $real_val))$output.= ' checked="checked"';
                    $output.= ' /><span>'.$curr_val->getLabel().'</span></div></div>';
                }
                elseif (is_array($curr_val)) {
                    $output.= 'id="'.$element->getHtmlId().$curr_val['value'].'" value="'.htmlspecialchars($curr_val['value'], ENT_COMPAT).'"';
                    if ($curr_val['value'] == $real_val) $output.= ' checked="checked"';
                    $output.= ' /><span>'.$curr_val['label'].'</span></div></div>';
                }
                $output.= '</div>'. "\n";
            }
        }
        return $output.'<div class="clear"></div>';
    }
}