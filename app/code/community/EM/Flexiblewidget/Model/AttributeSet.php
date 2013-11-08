<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Sample
 * @package     Sample_WidgetTwo
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */


/**
 * Source model for the social bookmarking widget configuration
 *
 * @category    Sample
 * @package     Sample_WidgetTwo
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class EM_Flexiblewidget_Model_AttributeSet extends Mage_Core_Model_Abstract
{

    /**
     * Provides a value-label array of available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAttributeSetList();
        
    }
    public function getAttributeSetList()
    {
		//Fetch attribute set id by attribute set name
		/*
		 
		//Load product model collecttion filtered by attribute set id
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('name')
			->addFieldToFilter('attribute_set_id', $attributeSetId);
		
		//process your product collection as per your bussiness logic
		$productsName = array();
		foreach($products as $p){
			$productsName[] = $p->getData('name');
		}
		//return all products name with attribute set 'my_custom_attribute'
		print_r($productsName);
    	die;
    	*/
		
		/*
    	$attrSetName = 'computer';
		$attributeSetId = Mage::getModel('eav/entity_attribute_set')->load($attrSetName, 'attribute_set_name')->getAttributeSetId();
    	
		echo $attributeSetId.'<br/>';
		
		$attset = new Mage_Catalog_Model_Product_Attribute_Api();
    	$att = $attset->items($attributeSetId);			
		echo count($att).'<br/>';
    	print_r($att);die;
		*/

		
    	$rs1 = Mage::getModel('catalog/product_attribute_set_api')->items();
        $tmp = array();
        foreach($rs1 as $r)
        {
            $tmp[] = array('value' => $r['name'],'label' => $r['name']);
        }
        return $tmp;
        
    }
}
