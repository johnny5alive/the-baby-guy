<?php
/**
 * Customers Who Purchased
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Upsell
 * @version      2.1.2
 * @license:     2FAsxXJzc5JeHBuQEqczWVkN1VAQ4c4jbOwkjabuwA
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Upsell_Model_Observer
{
    public function appendProducts($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $limit = $observer->getEvent()->getLimit();
        $defLimit = intVal(Mage::getStoreConfig('catalog/adjupsell/count'));
        $size = isset($limit['adjupsell']) ? $limit['adjupsell'] : $defLimit;
        if ($size <= 0)
        {
            return $this;
        }

        //remove already added products
        $collection = $observer->getEvent()->getCollection();
        $exclude = $collection->getAllIds();
        foreach ($collection->getItems() as $k=>$v) //not from sql, but from current collection items
            $exclude[] = $k;
            
        $ids = Mage::getResourceSingleton('adjupsell/upsell')->getPurchasedWith($product->getId(), $exclude, $size);
        if (!count($ids)){
            return $this;
        } 

        $upsell = Mage::getModel('catalog/product')->getResourceCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addStoreFilter()
            ->addMinimalPrice();
        $upsell->addIdFilter($ids); 
        
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($upsell);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($upsell);
        //remove produts already in cart
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        if ($quoteId)
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($upsell, $quoteId);

        $upsell->load();
        foreach ($upsell->getItems() as $key=>$item)
            $collection->addItem($item);

        return $this;        
    }
}