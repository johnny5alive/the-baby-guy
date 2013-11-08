<?php
class Orchid_CouponFix_Model_Observer
{
    public function cancel($observer)
    {
    	$event = $observer->getEvent();
      	$order = $event->getPayment()->getOrder();
      	
        if ($order->canCancel()) {
			if ($code = $order->getCouponCode()) {
				$coupon = Mage::getModel('salesrule/rule')->load($code, 'coupon_code');
				$coupon->setTimesUsed($coupon->getTimesUsed()-1);
				$coupon->save();
				
				$writer = new Zend_Log_Writer_Stream(Mage::getBaseDir() . '/var/log/cancel_coupon.log');
				$logger = new Zend_Log($writer);
				$logger->info("total-coupon: " . $coupon->getTimesUsed()-1);
				
				if($customerId = $order->getCustomerId()) {
					if ($customerCoupon = Mage::getModel('salesrule/rule_customer')->loadByCustomerRule($customerId, $coupon->getId())) {
						$customerCoupon->setTimesUsed($customerCoupon->getTimesUsed()-1);
						$customerCoupon->save();
						
						$logger->info("customer id,coupon: {$customerId}," . $customerCoupon->getTimesUsed()-1);
					}
				}
			}
		}
    }
    
    public function cancel2($observer)
    {
    $event = $observer->getEvent();
        $order = $event->getPayment()->getOrder();
        if ($order->canCancel()) {
        if ($code = $order->getCouponCode()) {
            $coupon = Mage::getModel('salesrule/rule')->load($code, 'coupon_code');
            $coupon->setTimesUsed($coupon->getTimesUsed()-1);
            $coupon->save();
            if($customerId = $order->getCustomerId()) {
                if ($customerCoupon = Mage::getModel('salesrule/rule_customer')->loadByCustomerRule($customerId, $coupon->getId())) {
                    $customerCoupon->setTimesUsed($customerCoupon->getTimesUsed()-1);
                    $customerCoupon->save();
                }
            }
        }
//below is added by franky
    if ($rules = $order->getAppliedRuleIds()) {
    foreach(explode(",", $rules) as $rule_id){
            $rule = Mage::getModel('salesrule/rule')->load($rule_id);
            $rule->setTimesUsed($rule->getTimesUsed()-1);
            $rule->save();
 
            if($customerId = $order->getCustomerId()) {
                if ($customerCoupon = Mage::getModel('salesrule/rule_customer')->loadByCustomerRule($customerId, $rule_id)) {
                    $customerCoupon->setTimesUsed($customerCoupon->getTimesUsed()-1);
                    $customerCoupon->save();
                }
            }
        }
    }
    }
    }
    
    public function cancel3($observer)
	{
	  $event = $observer->getEvent();
	  $order = $event->getPayment()->getOrder();
	  if ($order->canCancel()) {
	    if ($code = $order->getCouponCode()) {
	      $coupon = mage::getModel('salesrule/coupon')->load($code, 'code');
	      if ($coupon->getTimesUsed() > 0) {
	        $coupon->setTimesUsed($coupon->getTimesUsed()-1);
	        $coupon->save();
	      }
	 
	      $rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
	      error_log("\nrule times used=" . $rule->getTimesUsed(),3,"var/log/debug.log");
	      if ($rule->getTimesUsed() > 0) {
	        $rule->setTimesUsed($rule->getTimesUsed()-1);
	        $rule->save();
	      }
	      if($customerId = $order->getCustomerId()) {
	        if ($customerCoupon = Mage::getModel('salesrule/rule_customer')->loadByCustomerRule($customerId, $rule->getId())) {
	          $couponUsage = new Varien_Object();
	          Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon($couponUsage, $customerId, $coupon->getId());
	 
	          if ($couponUsage->getTimesUsed() > 0) {
	 
	            /* I can't find any #@$!@$ interface to do anything but increment a coupon_usage record */
	            $resource = Mage::getSingleton('core/resource');
	            $writeConnection = $resource->getConnection('core_write');
	            $tableName = $resource->getTableName('salesrule_coupon_usage');
	 
	            $query = "UPDATE {$tableName} SET times_used = times_used-1 " .
	              " WHERE coupon_id = {$coupon->getId()} AND customer_id = {$customerId} AND times_used > 0";
	 
	            $writeConnection->query($query);
	          }
	          if ($customerCoupon->getTimesUsed() > 0) {
	            $customerCoupon->setTimesUsed($customerCoupon->getTimesUsed()-1);
	            $customerCoupon->save();
	          }
	        }
	      }
	    }
	  }
  	}
  	
  	public function retrieveCoupon($observer)
	{
    	$payment = $observer->getEvent()->getPayment();
    	$order = $payment->getOrder();
 
	    if ($code = $order->getCouponCode()) {
	        $coupon = Mage::getModel('salesrule/coupon')->load($code, 'code');
	        $coupon->setTimesUsed($coupon->getTimesUsed()-1);
	        $coupon->save();
	         
	        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
	        $query = "UPDATE salesrule_coupon_usage SET times_used = times_used-1 WHERE customer_id='".$order->getCustomerId()."' AND coupon_id='".$coupon->getId()."'";
	        $result = $write->query($query);
	         
	        if($customerId = $order->getCustomerId()) {
	            if ($customerCoupon = Mage::getModel('salesrule/rule_customer')->loadByCustomerRule($customerId, $coupon->getRuleId())) {
	                $customerCoupon->setTimesUsed($customerCoupon->getTimesUsed()-1);
	                $customerCoupon->save();
	            }
	        }
	    }
	}
}