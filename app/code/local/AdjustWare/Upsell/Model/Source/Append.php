<?php
/**
 * Customers Who Purchased
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Upsell
 * @version      2.1.2
 * @license:     2FAsxXJzc5JeHBuQEqczWVkN1VAQ4c4jbOwkjabuwA
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Upsell_Model_Source_Append extends Mage_Adminhtml_Model_System_Config_Source_Yesno
{
    
    public function toOptionArray()
    {
        return array(
            array('value'=>2, 'label'=>Mage::helper('adminhtml')->__('Append')),
            array('value'=>1, 'label'=>Mage::helper('adminhtml')->__('Prepend')),
            array('value'=>0, 'label'=>Mage::helper('adminhtml')->__('Disabled')),
        );
    }
    
}