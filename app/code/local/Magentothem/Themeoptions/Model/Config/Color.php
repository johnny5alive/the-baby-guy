<?php
/*------------------------------------------------------------------------
# Websites: http://www.plazathemes.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Themeoptions_Model_Config_Color
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'skin1', 'label'=>Mage::helper('adminhtml')->__('Skin 1')),
            array('value'=>'skin2', 'label'=>Mage::helper('adminhtml')->__('Skin 2')),
            array('value'=>'skin3', 'label'=>Mage::helper('adminhtml')->__('Skin 3')),
            array('value'=>'skin4', 'label'=>Mage::helper('adminhtml')->__('Skin 4')),
            array('value'=>'skin5', 'label'=>Mage::helper('adminhtml')->__('Skin 5')),
            array('value'=>'skin6', 'label'=>Mage::helper('adminhtml')->__('Skin 6'))
        );
    }

}
