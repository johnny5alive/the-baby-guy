<?php

class Apptha_Invitefriends_Model_Mysql4_Invitefriends extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the invitefriends_id refers to the key field in your database table.
        $this->_init('invitefriends/invitefriends', 'history_id');
    }
}