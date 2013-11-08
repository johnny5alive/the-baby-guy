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
class AdjustWare_Upsell_Block_Rewrite_FrontCatalogProductListRelated extends Mage_Catalog_Block_Product_List_Related
{
    protected $_currentBlock = 'Related';
    
    protected function _prepareData()
    {
        parent::_prepareData();
        $block = Mage::getStoreConfig('catalog/adjupsell/block');
        $append = Mage::getStoreConfig('catalog/adjupsell/append');
        $enabled = Mage::getStoreConfig('catalog/adjupsell/enabled');
        
        if (!Mage::helper('adjupsell')->isBlockSelected($this->_currentBlock) || !$enabled)
        {
            return $this;
        }
        
        $product = Mage::registry('product')->getId();    
        $itemCollection = Mage::getModel('adjupsell/upsell')
            ->getPreparedCollection($this->_currentBlock, $product);
            
        if (!$append)
        {
            $this->_itemCollection = $itemCollection;
            return $this;
        }
        
        switch ($append)
        {
            case 1:
                $source = $itemCollection;
                $destination = $this->_itemCollection;
                break;
            case 2:
                $source = $this->_itemCollection;
                $destination = $itemCollection;
                break;
        }
        
        $size = (int)Mage::getStoreConfig('catalog/adjupsell/count');
        $size = $size > 0 ? $size : 0;
        foreach ($source as $item)
        {
            if (!$destination->getItemByColumnValue('entity_id',$item->getId()))
            {
                $destination->addItem($item);
            }
        }
        
        
        $keys = array_keys($destination->getItems());
        while (sizeof($keys) > $size)
        {
            $destination->removeItemByKey(array_pop($keys));
        }
        
        // $destination->getSize() may return 0 even if collection contains some items.
        // Please see bug report @ http://mantis.it/view.php?id=27732
        // Creating new NON-DB model object to avoid that.        
        
        $newCollection = new Varien_Data_Collection();
        foreach ($destination->getItems() as $item)
        {
            $newCollection->addItem($item);
        }
        
        $this->_itemCollection = $newCollection;
        
        return $this;
    }
}