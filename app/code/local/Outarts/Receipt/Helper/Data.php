<?php

class Outarts_Receipt_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getReceiptType($type){
        if($type == "bivalence"){
            return "Bivalent invoice";
        }elseif($type == "triple"){
            return "Triple invoice";
        }
    }
}
