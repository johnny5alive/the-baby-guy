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
class AdjustWare_Icon_Model_Mysql4_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('adjicon/attribute', 'id');
    }
    
    public function getIconsByOptions($ids, $storeId)
    {    
        $db = $this->_getReadAdapter();
        $sql = $db->select()
            ->from(array('o' => $this->getTable('adjicon/icon')), array('o.option_id', 'o.filename'))
            ->joinInner(array('v' => $this->getTable('eav/attribute_option_value')), 'o.option_id = v.option_id', array('v.value','v.store_id'))
            ->where('o.option_id IN(?)', $ids)
            ->where('v.store_id IN(0,?)', $storeId);
        $result = $db->fetchAll($sql);  

        $icons = array();
        foreach ($result as $row){
            $id = $row['option_id'];
            if (empty($icons[$id]) || $row['store_id'])
                $icons[$id] = array($row['value'], $row['filename']);
        }
            
        return $icons;
    }
    
    public function getOptions($attributeId)
    {    
        $db = $this->_getReadAdapter();
        $sql = $db->select()
            ->from(array('a'=>$this->getTable('eav/attribute_option')), array())
            ->joinInner(array('v' => $this->getTable('eav/attribute_option_value')), 'a.option_id = v.option_id', array('v.value', 'v.option_id'))
            ->joinLeft(array('i' => $this->getTable('adjicon/icon')), 'v.option_id = i.option_id', array('i.icon_id', 'i.filename'))
            ->where('a.attribute_id = ?', $attributeId)
            ->where('v.store_id = 0')  //default values
            ->order('v.value');  
            
        $result = $db->fetchAll($sql);  

        return $result;
    }
    
    public function getAvailableAttributes()
    {    
        $db = $this->_getReadAdapter();
        $sql = $db->select()
            ->from(array('a'=>$this->getTable('eav/attribute')), array('a.frontend_label', 'a.attribute_id'))
            ->joinLeft(array('i' => $this->getTable('adjicon/attribute')), 'a.attribute_id = i.attribute_id', array())
            ->where('a.frontend_input IN (?)', array('select','multiselect'))
            //->where('a.frontend_input = \'select\' ')
            ->where('a.entity_type_id = ?', Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId())
            ->where('i.id IS NULL');
            
            // is_visible_on_front=1  OR used_in_product_listing=1
            
        $result = $db->fetchAll($sql);  

        return $result;
    }

}