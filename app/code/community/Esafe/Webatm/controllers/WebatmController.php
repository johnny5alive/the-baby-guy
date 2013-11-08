<?php
/**
 * Esafe Webatm Extension
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
 * @package    Esafe_Webatm
 * @author     Jiang Sungjin
 * @author     Jin Kang
 * @copyright  Copyright (c) 2013 Skybear Co. Ltd. (http://www.myskybear.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webatm Checkout Controller
 *
 */
class Esafe_Webatm_WebatmController extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * When a customer chooses Webatm on Checkout/Payment page
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setWebatmQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('webatm/redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * When a customer cancels payment from Webatm.
     * Currently this never actually occurs as Webatm does not provide a way
     * to cancel the order from their interface.
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getWebatmQuoteId(true));
        $this->_redirect('checkout/cart');
     }

    /**
     * Where Webatm returns.
     * Webatm currently always returns the same code so there is little point
     * in attempting to process it.
     */
    public function completeAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->norouteAction();
            return;
        }
        
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getWebatmQuoteId(true));
        
        $response = $this->getRequest()->getPost();
        
        $res_codes = array(
        	'buysafeno',
        	'web',
        	'Td',
        	'MN',
        	'webname',
        	'Name',
        	'note1',
        	'note2',
        	//'ApproveCode',
        	//'Card_NO',
        	'SendType',
        	'errcode',
        	'errmsg',
        	'ChkValue'
        );
        
        $log_result = "\r\n"; //json_encode($response)."\r\n";
        foreach($res_codes as $res_code) {
        	$log_result .= "{$res_code}: {$response[$res_code]}\r\n";
        }
        
        $writer = new Zend_Log_Writer_Stream(Mage::getBaseDir() . '/var/log/webatm.log');
		$logger = new Zend_Log($writer);
		$logger->info($log_result);
		
        $responseText = '';

        // Check the response code
        if($response['errcode'] == '00') {
            // Pending
            $responseText = 'Payment processing';
            
        } else {
            // Declined
            $this->_cancelOrder($response);
            $this->_redirect('checkout/onepage/failure');
            return;
        }
        
        // Set the quote as inactive after returning from Webatm
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        // Send a confirmation email to customer
        $order = Mage::getModel('sales/order');
        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        if( $order->getId() )
		{
			//$paymentInst = $order->getPayment()->getMethodInstance();
		    $paymentInst = $order->getPayment();
			$paymentInst->setLastTransId($response['buysafeno']);
			
			$order->sendNewOrderEmail();
			$order->setEmailSent( true );

            if($response['errcode'] == '00') {
                // Only if the payment is fully authorised should it be invoiced
			    if( $order->canInvoice() )
			    {
				    $invoice = $order->prepareInvoice();
				    $invoice->register()->capture();
				    $order->addRelatedObject( $invoice );
			    }
			}
			
			$text = 'Order returned from Esafe-WebATM<br />';
			$text .= 'Response: ' . $responseText . '<br />';
			$text .= 'Transaction ID: ' . $response['buysafeno'] . '<br />';
			$text .= 'Web code: ' . $response['web'] . '<br />';
			$text .= 'Amount Paid: ' . $response['MN'] . '<br />';
			
			$order->addStatusToHistory($order->getStatus(), $text);
			$order->save();
		}
		

        Mage::getSingleton('checkout/session')->unsQuoteId();

        $this->_redirect('checkout/onepage/success');
    }

	protected function _cancelOrder($response)
	{
	    $session = Mage::getSingleton('checkout/session');
	    $order = Mage::getModel('sales/order');
        $order->load($session->getLastOrderId());
        $order->cancel();
        $order->addStatusToHistory($order->getStatus(), Mage::helper('webatm')->__('Payment was declined by gateway.<br />Transaction ID: ' . $response['buysafeno']));
        $order->save();
	}

}
