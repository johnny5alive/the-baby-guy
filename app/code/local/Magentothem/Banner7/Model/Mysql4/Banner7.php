<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner7_Model_Mysql4_Banner7 extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the banner7_id refers to the key field in your database table.
        $this->_init('banner7/banner7', 'banner7_id');
    }
}