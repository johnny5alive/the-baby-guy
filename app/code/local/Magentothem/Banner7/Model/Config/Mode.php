<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner7_Model_Config_Mode
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'fade', 'label'=>Mage::helper('adminhtml')->__('Fade')),
            array('value'=>'slide', 'label'=>Mage::helper('adminhtml')->__('Slide')),           
        );
    }

}
