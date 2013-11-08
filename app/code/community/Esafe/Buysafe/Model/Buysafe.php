<?php
/**
 * Esafe Buysafe Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so one can be sent to you a copy immediately.
 *
 * @category   Esafe
 * @package    Esafe_Buysafe
 * @author     Jiang Sungjin
 * @author     Jin Kang
 * @copyright  Copyright (c) 2013 Skybear Co. Ltd. (http://www.myskybear.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Buysafe payment model
 *
 * @category   Esafe
 * @package    Esafe_Taiwan
 */
class Esafe_Buysafe_Model_Buysafe extends Mage_Payment_Model_Method_Abstract
{
    const CGI_URL = 'https://www.esafe.com.tw/Service/Etopm.aspx';
    const CGI_URL_TEST = 'https://test.esafe.com.tw/Service/Etopm.aspx';
    const REQUEST_AMOUNT_EDITABLE = 'N';

    protected $_code  = 'buysafe';
    protected $_formBlockType = 'esafe_buysafe_block_form';
    protected $_allowCurrencyCode = array('NTD','USD', 'TWD', 'CNY', 'RMB');
    
    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    
    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Esafe_Taiwan_Model_Payment_Buysafe
     */
    public function assignData($data)
    {
        $details = array();
        if ($this->getUsername())
        {
            $details['username'] = $this->getUsername();
        }
        if (!empty($details)) 
        {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }
        return $this;
    }

    public function getWebcode()
    {
        return $this->getConfigData('webcode');
    }
    
    public function getWebpass()
    {
        return $this->getConfigData('webpass');
    }
    
    public function getUrl()
    {
    	$test = $this->getConfigData('test');
    	
    	$url = self::CGI_URL;
    	
    	if($test) {
    		$url = self::CGI_URL_TEST;
    	}
    	
    	return $url;
    }
    
    /**
     * Get session namespace
     *
     * @return Esafe_Taiwan_Model_Payment_Buysafe_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('buysafe/buysafe_session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    public function getOrder() {
    	$order = Mage::getModel('sales/order')->load($this->getCheckout()->getLastOrderId());
    	
    	return $order;
    }
    
    public function getCheckoutFormFields()
	{
		$order = $this->getOrder();
		$a = $order->getShippingAddress();
		$b = $order->getBillingAddress();
		$currency_code = $order->getCurrencyCode();
		/*$cost = $order->getSubtotal() - $order->getDiscountAmount();
		$shipping = $order->getShippingAmount();

		$_shippingTax = $order->getTaxAmount();
		$_billingTax = $order->getTaxAmount();
		$tax = floor($_shippingTax + $_billingTax);
		$cost = floor($cost + $tax);*/
		
		$total = $order->getGrandTotal();
		
		$OrderInfo = "";
		$items = $order->getAllItems();
		foreach($items as $itemId => $item) {
			$OrderInfo .= $item->getName() . " " . $item->getQtyToInvoice() . "  ";
		}
		
		$chkValue = "";
		$hashValue = $this->getWebcode() . $this->getWebpass() . floor($total);
		//echo $hashValue;
		$chkValue = sha1($hashValue);
		
		$fields = array(
			'web'					=> $this->getWebcode(),
			'MN'					=> floor($total), //floor($cost + $shipping),
			'OrderInfo'				=> $OrderInfo,
			'Td'					=> $this->getCheckout()->getLastRealOrderId(),
			'sna'					=> $b->getFirstname()." ".$b->getLastname(),
			'sdt'					=> $b->getTelephone(),
			'email'					=> $b->getEmail(),
			'note1'					=> "",
			'note2'					=> "",
			'ChkValue'				=> "", //$chkValue,
			//'return'				=> Mage::getUrl('buysafe/buysafe/complete'),
		);
		//print_r($fields);exit;
		// Run through fields and replace any occurrences of & with the word 
		// 'and', as having an ampersand present will conflict with the HTTP
		// request.
		$filtered_fields = array();
        foreach ($fields as $k=>$v) {
            $value = str_replace("&","and",$v);
            $filtered_fields[$k] =  $value;
        }
        
        return $filtered_fields;
	}

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('buysafe/buysafe_form', $name)
            ->setMethod('buysafe')
            ->setPayment($this->getPayment())
            ->setTemplate('esafe/buysafe/form.phtml');

        return $block;
    }

    /*validate the currency code is avaialable to use for paypal or not*/
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('buysafe')->__('Selected currency code ('.$currency_code.') is not compatabile with Esafe'));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('buysafe/buysafe/redirect');
    }
}
