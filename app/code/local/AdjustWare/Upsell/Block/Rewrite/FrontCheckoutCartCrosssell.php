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
class AdjustWare_Upsell_Block_Rewrite_FrontCheckoutCartCrosssell extends Mage_Checkout_Block_Cart_Crosssell
{
    
    protected $_currentBlock = 'CrossSell';
    
    protected function _getUpsellItems( $size , $ninProductIds )
    {
        $items = array();
        $collection = Mage::getModel('adjupsell/upsell')
            ->getPreparedCollection($this->_currentBlock, $this->_getCartProductIds());
        if ($collection){
            foreach ($collection as $item) {
                if (in_array($item->getId(),$ninProductIds))
                {
                    continue;
                }
                $items[] = $item;
                if (!--$size)
                {
                    break;
                }
            }
        }
        return $items;
    }
    
    public function getItems()
    {
        $items = $this->getData('items');
        if (is_null($items)) {
            $block   = Mage::getStoreConfig('catalog/adjupsell/block');
            $enabled = Mage::getStoreConfig('catalog/adjupsell/enabled');
        
            if (!Mage::helper('adjupsell')->isBlockSelected($this->_currentBlock) || !$enabled)
            {
                return parent::getItems();
            }
          
            $items = array();
            $size = intVal(Mage::getStoreConfig('catalog/adjupsell/count'));
            $size = $size > 0 ? $size : 0;
            $ninProductIds = $this->_getCartProductIds();
            if ($size && $ninProductIds) {
                /*$lastAdded = (int) $this->_getLastAddedProductId();
                if ($lastAdded) {
                    $collection = Mage::getModel('adjupsell/upsell')
                        ->getPreparedCollection($this->_currentBlock, $lastAdded);
                    if ($collection){
                        foreach ($collection as $item) {
                            $ninProductIds[] = $item->getId();
                            $items[] = $item;
                            if (!--$size)
                            {
                                break;
                            }
                        }
                    }                   
                }*/
                
                $append = Mage::getStoreConfig('catalog/adjupsell/append');
                
                $order = array('Upsell');
                switch($append)
                {
                    case 1:
                        array_unshift($order,'Standard');
                        break;
                    case 2:
						array_push($order,'Standard');
                        break;
                }
                foreach ($order as $method)
                {
                    $method = '_get'.$method.'Items';
                    foreach ($this->$method($size,$ninProductIds) as $item)
                    {
                        $ninProductIds[] = $item->getId();
                        $items[] = $item;
                        if (!--$size)
                        {
                            break 2;
                        }
                    }
                }
            }
            $this->setData('items', $items);
        }
        return $items;
    }
    
    protected function _getStandardItems( $size , $ninProductIds )
    {
        $items = array();
        $collection = $this->_getCollection()
            ->addProductFilter($this->_getCartProductIds())
            ->addExcludeProductFilter($ninProductIds)
            ->setPageSize($size)
            ->setGroupBy();
            /*->setRandomOrder()*/
        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        $collection->load();
        foreach ($collection as $item) {
            $items[] = $item;
            if (!--$size)
            {
                break;
            }
        }
        return $items;
    }

}