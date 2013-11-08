<?php
/**
 * Visualize Your Attributes
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Icon
 * @version      2.0.18
 * @license:     GPC7g2VHtpIP7j623srVjJmuippj4X9BeOkIuhMsJs
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Icon_Model_Mysql4_Icon_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('adjicon/icon');
    }
    
    public function addOptionsFilter($ids, $storeId=null)
    {
        $this->getSelect()
            ->joinInner(array('v' => $this->getTable('eav/attribute_option_value')), 'main_table.option_id=v.option_id', array('value'=>'value','store_id'=>'store_id'))
            ->where('main_table.option_id in (?)', array_unique($ids))
            ->where('v.store_id in(0, ?)', Mage::app()->getStore($storeId)->getId());
         return $this;
    }

}