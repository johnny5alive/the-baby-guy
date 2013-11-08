<?php

class Outarts_Receipt_Model_Observer
{
    public function saveReceiptData(Varien_Event_Observer $observer){
        $receiptData = Mage::app()->getRequest()->getParam("receipt");
        if(!$receiptData){
            return;
        }
        $quote = Mage::getSingleton("checkout/session")->getQuote();
        $quote->setReceiptType(isset($receiptData["type"]) ? $receiptData["type"]:"");
        $quote->setReceiptByBuy(isset($receiptData["by_buy"]) ? $receiptData["by_buy"]:"");
        $quote->setReceiptUniformNumber(isset($receiptData["uniform_number"]) ? $receiptData["uniform_number"]:"");
        $quote->save();
    }
}
