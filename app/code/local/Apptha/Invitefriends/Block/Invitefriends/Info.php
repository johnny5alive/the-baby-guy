<?php
class Apptha_Invitefriends_Block_Invitefriends_Info extends Mage_Core_Block_Template
{
	protected function _getCustomer()
	{
		return Mage::getModel('invitefriends/customer')->load(Mage::getSingleton("customer/session")->getCustomer()->getId());
	}
	
	
	
	
}