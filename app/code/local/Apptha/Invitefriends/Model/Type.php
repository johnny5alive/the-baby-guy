<?php

class Apptha_Invitefriends_Model_Type extends Varien_Object
{
   
	const FRIEND_REGISTERING	= 1;
        const INVITE_FRIEND_BONUS	= 2;
	const FRIEND_PURCHASE		= 3;
        const USE_TO_CHECKOUT		= 4;	
	

    static public function getOptionArray()
    {
        return array(
            self::FRIEND_REGISTERING    	=> Mage::helper('invitefriends')->__('Invited Friend Registering'),
            self::INVITE_FRIEND_BONUS   	=> Mage::helper('invitefriends')->__('Invite Friends Bonus'),
            self::FRIEND_PURCHASE		=> Mage::helper('invitefriends')->__('Invited Friend Purchase'),
            self::USE_TO_CHECKOUT		=> Mage::helper('invitefriends')->__('Use To Checkout'),
           
           
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
    
    static public function getTransactionDetail($type, $detail = null, $status=null,$is_admin= false)
    {
    	$result = "";
    	switch($type)
    	{	
    			
    		case self::FRIEND_REGISTERING:
    			$object = Mage::getModel('customer/customer')->load($detail);
    			$result = Mage::helper('invitefriends')->__("Earned for friend (<b>%s</b>) registering",$object->getEmail());
    			break;
               case self::INVITE_FRIEND_BONUS:
    			$result = Mage::helper('invitefriends')->__("Earned bonus for inviting (<b>%s</b>) friends",$detail);
    			break;
    		case self::FRIEND_PURCHASE:
    			$detail = explode('|',$detail);
    			$object = Mage::getModel('customer/customer')->load($detail[0]);
    			$result = Mage::helper('invitefriends')->__("Earned for friend purchase (<b>%s</b>)",$object->getEmail());
    			break;   		
    		
    		case self::USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$url = Mage::getUrl('sales/order/view',array('order_id'=>$order->getId()));
    			if($is_admin) $url = Mage::getUrl('adminhtml/sales_order/view',array('order_id'=>$order->getId()));
    			$result = Mage::helper('invitefriends')->__("Use to purchase order <b><a href='%s'>#%s</a></b>",$url,$detail);
    			break;    		
    	}
    	if($is_admin)
    	{
    		$result = str_replace('You','Customer',$result);
    		$result = str_replace('Your','Customer\'s',$result);
    	}
    	return $result;
    }
    
    static public function getAmountWithSign($amount, $type)
    {
    	$result = $amount;
    	switch ($type)
    	{
                case self::FRIEND_REGISTERING:
    		case self::INVITE_FRIEND_BONUS:
    		case self::FRIEND_PURCHASE:
				$result = "+".$amount;
				break;    		
    		case self::USE_TO_CHECKOUT:    		
    			$result = -$amount;
    		break;
    	}
    	return $result;
    }
}