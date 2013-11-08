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

class Mgt_AmazingWysiwyg_Model_Wysiwyg extends Mgt_Base_Helper_Data
{
    const CSS_CLASS = 'amazing-wysiwyg';
    const XML_PATH_MGT_AMAZING_WYSIWYG_ACTIVE = 'default/mgt_amazing_wysiwyg/mgt_amazing_wysiwyg/active';
    const XML_PATH_MGT_AMAZING_WYSIWYG_PRODUCTS = 'default/mgt_amazing_wysiwyg/mgt_amazing_wysiwyg/enable_wysiwyg_product';
    const XML_PATH_MGT_AMAZING_WYSIWYG_CMS_PAGE = 'default/mgt_amazing_wysiwyg/mgt_amazing_wysiwyg/enable_wysiwyg_cms_page';
    const XML_PATH_MGT_AMAZING_WYSIWYG_STATIC_BLOCK = 'default/mgt_amazing_wysiwyg/mgt_amazing_wysiwyg/enable_wysiwyg_static_block';
    
    static protected $_isEnabled;
    
    static public function isEnabled()
    {
        if (!self::$_isEnabled) {
            self::$_isEnabled = (int)self::_getConfigurationValue(self::XML_PATH_MGT_AMAZING_WYSIWYG_ACTIVE);
        }
        return self::$_isEnabled;
    }
    
    public function isEnabledForProduct()
    {
        return (int)self::_getConfigurationValue(self::XML_PATH_MGT_AMAZING_WYSIWYG_PRODUCTS);
    }
    
    public function isEnabledForCmsPage()
    {
        return (int)self::_getConfigurationValue(self::XML_PATH_MGT_AMAZING_WYSIWYG_CMS_PAGE);
    }
    
    public function isEnabledForStaticBlock()
    {
        return (int)self::_getConfigurationValue(self::XML_PATH_MGT_AMAZING_WYSIWYG_STATIC_BLOCK);
    }
    
    static protected function _getConfigurationValue($path)
    {
        return Mage::getConfig()->getNode($path);
    }
}