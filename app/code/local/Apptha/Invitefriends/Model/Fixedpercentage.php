<?php
class Apptha_Invitefriends_Model_Fixedpercentage 
{
    
/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Fixed')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Percentage')),
        );
    }

   
}
