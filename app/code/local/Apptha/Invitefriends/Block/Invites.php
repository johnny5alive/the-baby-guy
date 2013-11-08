<?php

class Apptha_Invitefriends_Block_Invites extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getTitle()
    {
    	return $this->__("Invite Friends Management");
    }

    
}