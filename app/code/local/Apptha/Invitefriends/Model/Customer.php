<?php
/**
 * Invite Friends
 *
 * @category   Apptha
 * @package    Apptha_Invitefriends
 * @author     Apptha Team <support@apptha.com>
 * @copyright  Copyright (c) 2012 (www.apptha.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    0.1.0
 */
class Apptha_Invitefriends_Model_Customer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('invitefriends/customer');
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getRefferalLink() {
        $customer = $this->_getSession()->getCustomer();
        $customerId = $customer->getId();
        $customerDetails = Mage::getModel('invitefriends/customer')->load($customerId);
        $tokenId = $customerDetails->getTokenId();
        return trim(Mage::getUrl('invitefriends/index'),"/")."?token=".$tokenId;
    }

    public function getCustomercredits() {
        $customer = $this->_getSession()->getCustomer();
        $customerId = $customer->getId();
        $customerDetails = Mage::getModel('invitefriends/customer')->load($customerId);
        $creditAmount = $customerDetails->getCreditAmount();
        return $creditAmount;
    }

    public function creditCalculation() {
        $totalCredits = $availableCredit = $upcomingCredits = $usedCredits = 0;
        $customer = $this->_getSession()->getCustomer();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write'); //get db connection
        $currencySumbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->
                          getCurrentCurrencyCode())->getSymbol();//get currency symbol
        $customerEmail = $customer->getEmail();
        $tPrefix = (string) Mage::getConfig()->getTablePrefix(); //get table prefix
        $customerTable = $tPrefix . 'apptha_invitefriends_customer';
        $historyTable = $tPrefix . 'apptha_invitefriends_history';
        $status = Apptha_Invitefriends_Model_Status::COMPLETE;
        $totalResult = $write->query("select credit_amount from $customerTable where customer_email = '$customerEmail'");
        $availableCredit = $totalResult->fetch(PDO::FETCH_COLUMN);
        $searchResult = $write->query("select sum(b.amount) from $customerTable a left join $historyTable b on a.customer_id=b.customer_id where a.customer_email = '$customerEmail' and b.type_of_transaction=3 and b.status!=$status");
        $upcomingCredits = $searchResult->fetch(PDO::FETCH_COLUMN);        
        $status = Apptha_Invitefriends_Model_Status::COMPLETE;
        $searchResult = $write->query("select sum(b.amount) from $customerTable a left join $historyTable b on a.customer_id=b.customer_id where a.customer_email = '$customerEmail' and b.type_of_transaction=4 and b.status='$status'");
        $usedCredits = $searchResult->fetch(PDO::FETCH_COLUMN);
        $totalCredits = $availableCredit + $usedCredits;
        if(empty ($usedCredits)) {
            $usedCredits = 0;
        }
        if(empty ($upcomingCredits)) {
            $upcomingCredits = 0;
        }
        $creditDetails = array('total_credits' => $currencySumbol.$totalCredits,'balance' => $currencySumbol.$availableCredit,'upcoming_credits' => $currencySumbol.$upcomingCredits,'used_credits' => $currencySumbol.$usedCredits);
        return $creditDetails;
    }

   
}