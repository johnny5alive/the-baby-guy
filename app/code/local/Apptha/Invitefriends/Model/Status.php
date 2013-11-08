<?php

class Apptha_Invitefriends_Model_Status extends Varien_Object
{
    const UNCOMPLETE			= 0;
    const PENDING			= 1;		//haven't change points yet
    const COMPLETE			= 2;    
    const PROCESSING                    = 3;


    static public function getOptionArray()
    {
        return array(
            self::PENDING    		=> Mage::helper('invitefriends')->__('Pending'),
            self::COMPLETE  		=> Mage::helper('invitefriends')->__('Complete'),
            self::UNCOMPLETE	    	=> Mage::helper('invitefriends')->__('Uncomplete'),
            self::PROCESSING	    	=> Mage::helper('invitefriends')->__('Processing'),
        );
    }

    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
}