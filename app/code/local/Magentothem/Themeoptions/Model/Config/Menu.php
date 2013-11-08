<?php
/*------------------------------------------------------------------------
# Websites: http://www.plazathemes.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Themeoptions_Model_Config_Menu
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'wine_menu', 'label'=>Mage::helper('adminhtml')->__('Wide Menu')),
            array('value'=>'fish_menu', 'label'=>Mage::helper('adminhtml')->__('Fish Menu'))           
        );
    }

}
