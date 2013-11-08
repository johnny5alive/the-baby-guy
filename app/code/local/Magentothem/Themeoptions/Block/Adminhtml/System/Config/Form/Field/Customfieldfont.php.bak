<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class etheme_dresscodeconfig_Block_Adminhtml_System_Config_Form_Field_Customfieldfont extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $output = parent::_getElementHtml($element);

        $output .= '<span id="dresscodeconfig_font_view" style="font-size:30px;line-height: 30px; display:block;padding:8px 0 0 0">Lorem Ipsum Dolor</span>
        <script type="text/javascript" src="'.$this->getJsUrl('etheme/dresscode/jquery-1.6.2.min.js').'"></script>
		<script type="text/javascript">
            jQuery.noConflict();
            jQuery(function(){
                fontSelect=jQuery("#dresscodeconfig_dresscodeconfig_mainfront_font");
                fontUpdate=function(){
                    curFont=jQuery("#dresscodeconfig_dresscodeconfig_mainfront_font").val();
                    jQuery("#dresscodeconfig_font_view").css({ fontFamily: curFont });
                    jQuery("<link />",{href:"http://fonts.googleapis.com/css?family="+curFont,rel:"stylesheet",type:"text/css"}).appendTo("head");
                }
                fontSelect.change(function(){
                    fontUpdate();
                }).keyup(function(){
                    fontUpdate();
                }).keydown(function(){
                    fontUpdate();
                });
                jQuery("#dresscodeconfig_dresscodeconfig_mainfront_font").trigger("change");
            })
		</script>
        ';
        return $output;
    }
}