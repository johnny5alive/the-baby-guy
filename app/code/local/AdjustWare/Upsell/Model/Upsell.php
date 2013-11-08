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
class AdjustWare_Upsell_Model_Upsell extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('adjupsell/upsell');
    }
    
    public function getPreparedCollection($collectionName, $product){
        $enabled = Mage::getStoreConfig('catalog/adjupsell/enabled');
        if (!$enabled)
            return new Varien_Data_Collection();
        
        $size = intVal(Mage::getStoreConfig('catalog/adjupsell/count'));
        if ($size <= 0){ 
            return new Varien_Data_Collection();
        }
        
        $productId = is_object($product) ? $product->getId() : (array)$product;
        $collection = null;
        $ids = array();
        $tmpSize = $size;
        $offset = 0;
        do 
        {
            $tmpIds = Mage::getResourceSingleton('adjupsell/upsell')->getPurchasedWith(
                $productId,array(),$tmpSize,$offset
            );
            if ($tmpIds)
            {
                $tmpCollection = Mage::getModel('catalog/product')->getResourceCollection()
                     ->addIdFilter($tmpIds); 
                     
                $this->_addPricesAndAttributes($tmpCollection);
                $this->_addCommonFilters($tmpCollection);
                $tmpCollection->load();
                if ($collection)
                {
                    foreach ($tmpCollection as $item)
                    {
                        $collection->addItem($item);
                        $ids[] = $item->getId();
                    }
                }
                else
                {
                    $ids = $tmpIds;
                    $collection = $tmpCollection;
                }
                $offset += sizeof($tmpIds);
                $tmpSize -= sizeof($tmpCollection);
            }
        } 
        while ($tmpIds && $tmpSize > 0);
        
        if (is_null($collection) || 0 == $collection->count())
        {
            return new Varien_Data_Collection(); 
        }
        
        /*$size = $size - $collection->count();
        $append = Mage::getStoreConfig('catalog/adjupsell/append');
        if (is_object($product) && $append && $size > 0){ // we need to append some items
            $method = 'get' . $collectionName . 'ProductCollection';
            $default = $product->$method()
                ->addAttributeToSort('position', 'asc');
            
            $this->_addPricesAndAttributes($default);
            $this->_addCommonFilters($default);

            $default->addExcludeProductFilter($collection->getAllIds());
            $default->setPageSize($size);
            $default->load();     
            
            foreach ($default->getItems() as $item)
                $collection->addItem($item);
        }*/
        
        $result = array();
        /* @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        foreach ($ids as $id)
        {
            $result[$id] = $collection->getItemById($id);
        }
        /*foreach ($collection as $item)
        {
            if (!isset($result[$item->getId()]))
            {
                $result[$item->getId()] = $item;
            }
        }*/
        foreach ($collection as $key => $item)
        {
            $collection->removeItemByKey($key);
        }
        
        foreach ($result as $product) 
        {
            if ($product)
            {
                $product->setDoNotUseCategoryId(true);
                $collection->addItem($product);
            }
        }
        
        return $collection;      
    }
    
    protected function _addPricesAndAttributes($collection){
        $collection->addAttributeToSort('position', 'asc')
            ->addStoreFilter()
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
        return $this;
    }
    
    protected function _addCommonFilters($collection){
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        if ($quoteId){
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($collection, $quoteId);
        }      
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        $in_stock_only = Mage::getStoreConfig('catalog/adjupsell/in_stock_only');
        if ($in_stock_only)
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        return $this;
    }
       
}