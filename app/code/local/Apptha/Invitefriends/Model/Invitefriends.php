<?php

class Apptha_Invitefriends_Model_Invitefriends extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('invitefriends/invitefriends');
    }
}