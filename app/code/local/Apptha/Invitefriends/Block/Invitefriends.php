<?php

class Apptha_Invitefriends_Block_Invitefriends extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getTitle()
    {
    	return $this->__("Invite Friends Management");
    }

    public function addInvitefriends() {

        //Create an object for getParentBlock() method
        $parentBlock = $this->getParentBlock();

        //verify the module is enabled in the backend
        if ($parentBlock && Mage::helper('invitefriends')->isInvitefriendsEnabled()) {

            $text = $this->__('Invite Friends');//Top link Display Text
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {  // if not logged in
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getBaseurl() . "invitefriends");
                $url = 'customer/account/login';
            } else {
            $url  = 'invitefriends';
            }
            $position = 5;
            /**
             * @param string $text
             * @param string $url
             * @param string $text
             * @param boolean $prepare
             * @param array $urlParams
             * @param int $position
             * @return Mage_Page_Block_Template_Links
             */
            $parentBlock->addLink($text, $url , $text, $prepare=true, $urlParams=array(), $position , null, 'class="top-link-invitefriends"');
        }
        return $this;
    }
    
     public function getInvitefriends()     
     { 
        if (!$this->hasData('invitefriends')) {
            $this->setData('invitefriends', Mage::registry('invitefriends'));
        }
        return $this->getData('invitefriends');
        
    }
}