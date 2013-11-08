<?php
/*------------------------------------------------------------------------
# Websites: http://www.plazathemes.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Themeoptions_Model_Config_Grid
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'3', 'label'=>Mage::helper('adminhtml')->__('3')),
            array('value'=>'4', 'label'=>Mage::helper('adminhtml')->__('4'))           
        );
    }

}
