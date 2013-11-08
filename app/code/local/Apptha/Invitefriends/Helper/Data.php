<?php

class Apptha_Invitefriends_Helper_Data extends Mage_Core_Helper_Abstract {
    /* function to check if invite friends enabled */

    public function isInvitefriendsEnabled() {
        return Mage::getStoreConfig('invitefriends/invitefriends_enable/enable_invitefriends');
    }

    public function enabledSharelink() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_link');
    }

    public function enabledEmailInvite() {
        return Mage::getStoreConfig('invitefriends/email_settings/enable_email_invite');
    }

    public function enabledFacebookInvite() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_fb');
    }

    public function enabledTwitterInvite() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_twitter');
    }

    public function enabledGmailInvite() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_gmail');
    }

    public function getCreitType() {
        return Mage::getStoreConfig('invitefriends/Credits/fixed_percentage');
    }

    public function getCreditAmount() {
        return Mage::getStoreConfig('invitefriends/Credits/amount_credited');
    }

    public function getInviteCredits() {
        return Mage::getStoreConfig('invitefriends/credits_invite/amount_invite');
    }

    public function getNumberofFriends() {
        return Mage::getStoreConfig('invitefriends/credits_invite/invite_number');
    }

    public function getBonusAmount() {
        return Mage::getStoreConfig('invitefriends/bonus_invites/amount_invite');
    }

    public function getBonusInvites() {
        return Mage::getStoreConfig('invitefriends/bonus_invites/invite_number');
    }

    public function getPurchaseBonus() {
        return Mage::getStoreConfig('invitefriends/purchase_bonus/amount_bonus');
    }

    public function getLimitationDays() {
        return Mage::getStoreConfig('invitefriends/purchase_bonus/days_bonus');
    }

    public function getfbinviteUrl() {
        return $this->_getUrl('invitefriends/index/fbinvite', array('_secure' => true));
    }

    public function getfbappId() {
        return Mage::getStoreConfig('invitefriends/social_invite/fb_app');
    }

    public function getfbsecretKey() {
        return Mage::getStoreConfig('invitefriends/social_invite/fb_secret');
    }

    public function gettwitterConsumerkey() {
        return Mage::getStoreConfig('invitefriends/social_invite/twitter_app');
    }

    public function gettwitterConsumersecret() {
        return Mage::getStoreConfig('invitefriends/social_invite/twitter_secret');
    }

    public function getgmailClientid() {
        return Mage::getStoreConfig('invitefriends/social_invite/gmail_clientid');
    }

    public function getgmailClientsecretkey() {
        return Mage::getStoreConfig('invitefriends/social_invite/gmail_secretkey');
    }

    public function getfbshareTitle() {
        return Mage::getStoreConfig('invitefriends/social_invite/fbshare_title');
    }

    public function getfbshareDescription() {
        return Mage::getStoreConfig('invitefriends/social_invite/fbshare_description');
    }

    //get Invitation link of customer.
    public function getLink(Mage_Customer_Model_Customer $customer) {
        return trim(Mage::getUrl('invitefriends/index'), "/") . "?c=" . $customer->getEmail();
    }

}