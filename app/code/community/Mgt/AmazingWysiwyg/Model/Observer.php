<?php

/**
 * MGT-Commerce GmbH
 * http://www.mgt-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@mgt-commerce.com so we can send you a copy immediately.
 *
 * @category    Mgt
 * @package     Mgt_AmazingWysiwyg
 * @author      Stefan Wieczorek <stefan.wieczorek@mgt-commerce.com>
 * @copyright   Copyright (c) 2012 (http://www.mgt-commerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mgt_AmazingWysiwyg_Model_Observer 
{
    public function addJs(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block && $block instanceof Mage_Adminhtml_Block_Page_Head) {
            $transport = $observer->getEvent()->getTransport();
            $transportHtml = $transport->getHtml();
            $transportHtml .= $block->getLayout()->createBlock('mgt_amazing_wysiwyg_adminhtml/js')->setTemplate('mgt_amazing_wysiwyg/js.phtml')->toHtml();
            $transport->setHtml($transportHtml);
        }
    }

    public function addCmsPageCssClass(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();
        $helper = Mage::helper('mgt_amazing_wysiwyg');
        if ($form && ($form instanceof Varien_Data_Form) && ($contentElement = $form->getElement('content')) && $helper->isEnabled() && $helper->isEnabledForCmsPage()) {
            $contentElement->unsConfig();
            $contentElement->setClass(Mgt_AmazingWysiwyg_Model_Wysiwyg::CSS_CLASS);
        }
    }
    
    public function addStaticBlockCssClass(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $helper = Mage::helper('mgt_amazing_wysiwyg');
        if ($block && ($block instanceof Mage_Adminhtml_Block_Cms_Block_Edit_Form) && $helper->isEnabled() && $helper->isEnabledForStaticBlock()) {
            $form = $block->getForm();
            $contentElement = $form->getElement('content');
            $contentElement->unsConfig();
            $contentElement->setClass(Mgt_AmazingWysiwyg_Model_Wysiwyg::CSS_CLASS);
        }
    }
    
    public function setMergeJsConfigValue(Varien_Event_Observer $observer)
    {
        // disable js merge in backend to prevent conflict with jquery
        $store = Mage::app()->getStore();
        $store->setConfigCacheValue('dev/js/merge_files', 0);
        $store->setConfigCacheValue('dev/css/merge_css_files', 0);
    }
}