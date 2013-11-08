<?php

class Apptha_Invitefriends_Model_Mysql4_Customer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('invitefriends/customer');
    }

    public function getTokenId($customerEmail) {
        echo $this->getSelect()->columns(array('alias'=>'token_id'))->where("customer_email = ?", "$customerEmail");
	return $this;
    }
}