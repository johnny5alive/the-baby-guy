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
class AdjustWare_Upsell_Model_Mysql4_Upsell extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
         $this->_init('catalog/product', 'entity_id');
    }
    
    public function getPurchasedWith($id, $exclude, $size, $from = null){
        $size = (int)$size;
        if ($size <= 0)
        {
            return array();
        }
        $where = '';
        if (count($exclude))
        {
            $where = ' AND t2.product_id NOT IN(' . join(',', $exclude) . ')';
        }
        $id = (array)$id;
        $t = $this->getTable('sales/quote_item'); 
        $sql = 'SELECT t2.product_id AS id, SUM(t2.qty) AS cnt'
             . ' FROM ' . $t . ' AS t '
			 . ' INNER JOIN ' . $this->getTable('sales/order') . ' AS t3 ON (t.quote_id = t3.quote_id)'
             . ' INNER JOIN ' . $t . ' AS t2 ON (t.quote_id = t2.quote_id AND t.product_id <> t2.product_id)'
             . ' WHERE t.product_id IN( ' . join(',',$id) . ') AND t2.parent_item_id IS NULL '
             . $where
             . ' GROUP BY t2.product_id'
             . ' ORDER BY cnt DESC'
             . ' LIMIT '.(null === $from ? '' : strval((int)$from).',') . intVal($size);
        $rows = $this->_getReadAdapter()->fetchAll($sql);
        
        $ids = array();
        foreach ($rows as $row)
            $ids[] = $row['id'];
        
        return $ids;
    }
}