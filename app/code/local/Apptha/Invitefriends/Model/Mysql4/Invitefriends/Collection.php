<?php

class Apptha_Invitefriends_Model_Mysql4_Invitefriends_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('invitefriends/invitefriends');
    }
}