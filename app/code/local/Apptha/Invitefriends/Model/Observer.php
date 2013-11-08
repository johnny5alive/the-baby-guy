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
class Apptha_Invitefriends_Model_Observer {

    /* function to get session value */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    /* function to get friend id based on customer id */
    public function getFriend($tokenId) {
        $tPrefix = (string) Mage::getConfig()->getTablePrefix(); //get table prefix
        $customerTable = $tPrefix . 'apptha_invitefriends_customer';
        $write = Mage::getSingleton('core/resource')->getConnection('core_write'); //get db connection
        $selectResult = $write->query("select customer_id from $customerTable where token_id = '$tokenId'");
        $customerId = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $customerId;
    }

    /* function to store registered customer */
    public function customerSaveAfter($param) {
        $friend_id = 0;
        $customer = $param->getCustomer();
        $getSessionId = Mage::getModel('core/cookie')->get('tokenid');
        if (Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
            $random_chars = "";
            $characters = array(
                "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M",
                "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                "1", "2", "3", "4", "5", "6", "7", "8", "9");
            $keys = array();
            while (count($keys) < 9) {
                $x = mt_rand(0, count($characters) - 1);
                if (!in_array($x, $keys)) {
                    $keys[] = $x;
                }
            }
            foreach ($keys as $key) {
                $random_chars .= $characters[$key];
            }
            $tokenId = $random_chars;
            $friend_id = $this->getFriend($getSessionId);
            $_customer = Mage::getModel('invitefriends/customer')->getCollection();
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $customerId = $customer->getId();
            $customerEmail = $customer->getEmail();
            $customerName = $customer->getName();
            $tableName = $_customer->getTable('customer');
            $createdDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            $query = "SELECT * FROM $tableName WHERE customer_id=$customerId";
            $selectResult = $write->query($query);
            $customerRecord = $selectResult->fetch(PDO::FETCH_COLUMN);
            if (empty($customerRecord)) {
                $sql = "INSERT INTO  $tableName (customer_id,token_id,friend_id,customer_email,customer_name,credit_amount,status,created_date) VALUES ('$customerId','$tokenId','$friend_id','$customerEmail','$customerName','0','0','$createdDate')";
                $write->query($sql);
            }
            Mage::getModel('core/cookie')->delete('tokenid');

            //check fried id in session (from click referral link)

            if ($friend_id != 0) {
                $friend = Mage::getModel('invitefriends/customer')->load($friend_id);
                $friendsCount = $this->getFriendscount($friend_id);
                if ($friendsCount == Mage::helper('invitefriends')->getNumberofFriends()) {
                    $credits = Mage::helper('invitefriends')->getInviteCredits();
                    //add credits for friend registration to already exiting customer
                    $transactionType = Apptha_Invitefriends_Model_Type::FRIEND_REGISTERING;
                    $transactionDetail = $customer->getId();
                    $data = array('customer_id' => $friend_id, 'type_of_transaction' => $transactionType, 'amount' => $credits, 'balance' => $friend->getCreditAmount(), 'transaction_detail' => $transactionDetail, 'transaction_time' => now(), 'status' => Apptha_Invitefriends_Model_Status::COMPLETE);
                    $this->saveTransactionHistory($data);
                    $friend->setCreditAmount($friend->getCreditAmount() + $credits);
                    $friend->save();
                    $this->updateFriendstatus($friend_id);
                }


                $invitesCount = $this->checkNumberofInvites($friend_id);
                $bonusFlag = $friend->getBonusFlag();
                if (($invitesCount == Mage::helper('invitefriends')->getBonusInvites()) && ($bonusFlag == 0)) {
                    $bounsAmount = Mage::helper('invitefriends')->getBonusAmount();
                    //add credits for friend registration to already exiting customer
                    $transactionType = Apptha_Invitefriends_Model_Type::INVITE_FRIEND_BONUS;
                    $transactionDetail = Mage::helper('invitefriends')->getBonusInvites();
                    $data = array('customer_id' => $friend_id, 'type_of_transaction' => $transactionType, 'amount' => $bounsAmount, 'balance' => $friend->getCreditAmount(), 'transaction_detail' => $transactionDetail, 'transaction_time' => now(), 'status' => Apptha_Invitefriends_Model_Status::COMPLETE);
                    $this->saveTransactionHistory($data);
                    $friend->setCreditAmount($friend->getCreditAmount() + $bounsAmount);
                    $friend->setBonusFlag('1');
                    $friend->save();
                }
            }

            //$this->_redirectUrl(Mage::getBaseurl() . "invitefriends/index");
        }
    }

    /* function to save transaction history */
    public function saveTransactionHistory($data) {
        $model = Mage::getModel('invitefriends/invitefriends');
        /* inserting and updating follow up customer */
        $model->setData($data)
                ->setId($id);
        if ($model->getCreatedDate() == NULL || $model->getUpdatedDate() == NULL) {
            $model->setCreatedDate(now());
            $model->setUpdatedDate(now());
        } else {
            $model->setUpdatedDate(now());
        }
        $model->save();
    }

    /* function to get friends count of a particular customer */
    public function getFriendscount($friend_id) {
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('customer');
        $sql = "SELECT count('friend_id') FROM $tableName WHERE friend_id = '$friend_id' and status=0";
        $selectResult = $write->query($sql);
        $invitesCount = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $invitesCount;
    }

    /* fucntion to update friend status after money credit*/
    public function updateFriendstatus($friend_id) {
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('customer');
        $sql = "UPDATE $tableName SET status=1 WHERE friend_id = '$friend_id'";
        $write->query($sql);
    }

    /* function to check number of invites */
    public function checkNumberofInvites($friend_id) {
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('customer');
        $sql = "SELECT count('friend_id') FROM $tableName WHERE friend_id = '$friend_id'";
        $selectResult = $write->query($sql);
        $invitesCount = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $invitesCount;
    }

    public function getPurchasecount($customerId) {
        $_customer = Mage::getModel('invitefriends/invitefriends')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('invitefriends');
        $sql = "SELECT count('history_id') FROM $tableName WHERE type_of_transaction = 3 and customer_id=$customerId";
        $selectResult = $write->query($sql);
        $purchaseCount = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $purchaseCount;
    }

    /* function to get discount */

    public function getDiscountAmount() {
        $typeofDetection = Mage::helper('invitefriends')->getCreitType();
        $detectionAmount = Mage::helper('invitefriends')->getCreditAmount();
        if ($typeofDetection == 1) {
            $discountAmount = $detectionAmount;
        } else {
             $totals = Mage::getSingleton('checkout/session')->getQuote()->getTotals(); //Total object
            $subtotal = round($totals["subtotal"]->getValue()); //Subtotal value
            $discountAmount = $detectionAmount*$subtotal / 100;
        }
        return $discountAmount;
    }

    /* Function to update discount */

    public function setdiscountamount($observer) {
        if(Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
        /* get quote item */
        $quote = $observer->getEvent()->getQuote();
        /* Get discount amount for invites */
        $customerCredits = Mage::getModel('invitefriends/customer')->getCustomercredits();
        $discountAmount = $this->getDiscountAmount();
        if ($customerCredits >= $discountAmount) {
            //we calculate the Ratio of taxes between GrandTotal & Discount Amount to know how tach we need to remove.
            $rat = 1 - ($discountAmount / $quote->getGrandTotal());
            $tax = $quote->getGrandTotal() - $quote->getSubtotal();
            $tax = $tax * $rat;
            $discountAmountWithoutTax = $discountAmount - $tax;
            $total = $quote->getGrandTotal();
            $quote->setGrandTotal($quote->getGrandTotal() - $discountAmount)
                    ->setBaseGrandTotal($quote->getBaseGrandTotal() - $discountAmount)
                    ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmountWithoutTax)
                    ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmountWithoutTax)
                    ->save();
            $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
            foreach ($quote->getAllAddresses() as $address) {
                /* Set Subtotal */
                $address->setSubtotal(0);
                $address->setBaseSubtotal(0);
                /* Set Grand total */
                $address->setGrandTotal(0);
                $address->setBaseGrandTotal(0);
                $address->collectTotals();
                if ($address->getAddressType() == $canAddItems) {
                    $address->setSubtotal((float) $quote->getSubtotal());
                    $address->setBaseSubtotal((float) $quote->getBaseSubtotal());
                    $address->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount());
                    $address->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount());
                    $address->setGrandTotal((float) $quote->getGrandTotal());
                    $address->setBaseGrandTotal((float) $quote->getBaseGrandTotal());
                    $address->setDiscountAmount($address->getDiscountAmount() + (-$discountAmount));
                    /* Set Discount Description */
                    if ($address->getDiscountDescription()) {

                        $title = 'Discount for invites + ' . $address->getDiscountDescription();
                    } else {
                        $title = 'Discount for invites';
                    }
                    /* Set Discount Amount */
                    $address->setDiscountDescription($title);
                    $address->setBaseDiscountAmount($address->getBaseDiscountAmount() + (-$discountAmount));
                    $address->save();
                }//end: if
            } //end: foreach
            foreach ($quote->getAllItems() as $item) {
                //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                $rat = $item->getBaseRowTotal() / $total;
                $ratdisc = $discountAmount * $rat;
                $item->setDiscountAmount($ratdisc);
                $item->setBaseDiscountAmount($ratdisc);
            } //end: foreach
        }
    }
    }

    public function placeAfter($argv) {
        if(Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
        $order = $argv->getOrder();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $_customer = Mage::getModel('invitefriends/customer')->load($customer->getId());
        if ($customer->getId()) {
            $_customer = Mage::getModel('invitefriends/customer')->load($customer->getId());
            //Subtract reward points of customer and save reward points to order if customer use this point to checkout
            $customerCredits = Mage::getModel('invitefriends/customer')->getCustomercredits();
            $discountAmount = $this->getDiscountAmount();
            if ($customerCredits >= $discountAmount) {
                $historyData = array('customer_id' => $customer->getId(), 'type_of_transaction' => Apptha_Invitefriends_Model_Type::USE_TO_CHECKOUT, 'amount' => $discountAmount, 'balance' => $_customer->getCreditAmount(), 'transaction_detail' => $order->getIncrementId(), 'transaction_time' => now(), 'status' => Apptha_Invitefriends_Model_Status::PENDING);
                $this->saveTransactionHistory($historyData);
            }
            //invited friend first purchase
            $strFrndId = $_customer->getFriendId();
            $orders = Mage::getModel("sales/order")->getCollection()
		->addFieldToFilter('customer_id',$customer->getId());
            $purchaseCount = $this->getPurchasecount($strFrndId);
            if ((sizeof($orders) == 1) && $strFrndId) {
                $strCusId = $_customer->getCustomerId();
                $objFrnd = Mage::getModel('invitefriends/customer')->load($strFrndId);
                $orders = Mage::getModel("sales/order")->getCollection()
                                ->addFieldToFilter('customer_id', $customer->getId());

                $purchaseBonus = Mage::helper('invitefriends')->getPurchaseBonus();
                $historyData = array('customer_id' => $strFrndId, 'type_of_transaction' => Apptha_Invitefriends_Model_Type::FRIEND_PURCHASE, 'amount' => $purchaseBonus, 'balance' => $objFrnd->getCreditAmount(), 'transaction_detail' => $customer->getId() . "|" . $order->getIncrementId(), 'transaction_time' => now(), 'status' => Apptha_Invitefriends_Model_Status::PENDING);
                $this->saveTransactionHistory($historyData);
            }
        }
        }
    }

    public function saveOrderInvoiceAfter($argv) {
        if(Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
        $invoice = $argv->getInvoice();
        $order = $invoice->getOrder();
        $customerId = $order->getCustomerId();
        $orderId = $order->getIncrementId();
        $customer = Mage::getModel('invitefriends/customer')->load($customerId);
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $_history = Mage::getModel('invitefriends/invitefriends')->getCollection();
        $tableName = $_history->getTable('invitefriends');
        if($customer->getCreditAmount() != 0) {
        $selectResult = $write->query("SELECT history_id,amount FROM $tableName WHERE customer_id=$customerId and transaction_detail=$orderId");
        $transactions = $selectResult->fetch(PDO::FETCH_ASSOC);
        $creditAmount = $customer->getCreditAmount() - $transactions['amount'];
        $historyId = $transactions['history_id'];
        $transaction = Mage::getModel('invitefriends/invitefriends')->load($historyId);
        $customer->setCreditAmount($creditAmount);
        $customer->save();
        $status = Apptha_Invitefriends_Model_Status::COMPLETE;
        $transaction->setBalance($customer->getCreditAmount())->setTransactionTime(now());
        $transaction->setStatus($status)->save();
        }
        $typeofTransaction = Apptha_Invitefriends_Model_Type::FRIEND_PURCHASE;
        $statusCheck = Apptha_Invitefriends_Model_Status::PENDING;
        $friendId = $customer->getFriendId();
        $transactionDetails = $customerId.'|'.$orderId;
        $selectResult = $write->query("SELECT history_id FROM $tableName WHERE customer_id=$friendId and type_of_transaction=$typeofTransaction and status=$statusCheck and transaction_detail='$transactionDetails'");
        $historyId = $selectResult->fetch(PDO::FETCH_COLUMN);
        $transaction = Mage::getModel('invitefriends/invitefriends')->load($historyId);
        $status = Apptha_Invitefriends_Model_Status::PROCESSING;
        $transaction->setStatus($status)->save();
        }
    }

    public function paymentCancel($arvgs) {
        if(Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
        $payment = $arvgs->getPayment();
        $order = $payment->getOrder();
        $customerId = $order->getCustomerId();
        $orderId = $order->getIncrementId();
        $customer = Mage::getModel('invitefriends/customer')->load($customerId);
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $_history = Mage::getModel('invitefriends/invitefriends')->getCollection();
        $tableName = $_history->getTable('invitefriends');
        $selectResult = $write->query("SELECT history_id,amount FROM $tableName WHERE customer_id=$customerId and transaction_detail=$orderId");
        $transactions = $selectResult->fetch(PDO::FETCH_ASSOC);
        $historyId = $transactions['history_id'];
        $transaction = Mage::getModel('invitefriends/invitefriends')->load($historyId);
        $status = Apptha_Invitefriends_Model_Status::UNCOMPLETE;
        $transaction->setBalance($customer->getCreditAmount())->setTransactionTime(now());
        $transaction->setStatus($status)->save();
        }
    }
   /* function to update credits for friend purchase */
    public function updateFriendpurchase() {
        if(Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
        $transactions = Mage::getModel('invitefriends/invitefriends')->getCollection()
                        ->addFieldToFilter('status', Apptha_Invitefriends_Model_Status::PROCESSING)
                        ->addFieldToFilter('type_of_transaction', 3)
                        ->addOrder('history_id', 'ASC');

        foreach ($transactions as $transaction) {
            $date = date('Y-m-d');
            $transactionTime = $transaction->getTransactionTime();
            $status = $transaction->getStatus();
            $transactionDate = date('Y-m-d', strtotime($transactionTime));
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $selectResult = $write->query("SELECT DATEDIFF('$date','$transactionDate') AS DiffDate");
            $days = $selectResult->fetch(PDO::FETCH_COLUMN);
            if (($days >= Mage::helper('invitefriends')->getLimitationDays()) && ($status == Apptha_Invitefriends_Model_Status::PROCESSING)) {
                $customerId = $transaction->getCustomerId();
                $customerDetails = Mage::getModel('invitefriends/customer')->load($customerId);
                $customerDetails->setCreditAmount($customerDetails->getCreditAmount() + $transaction->getAmount())->save();
                $status = Apptha_Invitefriends_Model_Status::COMPLETE;
                $transaction->setBalance($customerDetails->getCreditAmount())->setTransactionTime(now());
                $transaction->setStatus($status)->save();
            }
        }
        }
    }

}

?>
