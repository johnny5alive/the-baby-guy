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
class AdjustWare_Icon_Model_Mysql4_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('adjicon/attribute');
    }
    
    public function addTitles()
    {
        $this->getSelect()->joinInner(array('a'=> $this->getTable('eav/attribute')), 
            'main_table.attribute_id=a.attribute_id', 
            array('a.frontend_label'));
            
        return $this;
    }

}