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
class AdjustWare_Icon_Model_Attribute extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('adjicon/attribute');
    }
    
    // return array of icons with store specific titles by given option ids 
    public function getIconsByOptions($ids, $storeId=null)
    {    
        return $this->getResource()->getIconsByOptions($ids, $storeId);        
    }

    // return array of all options with default titles and icon information
    public function getOptions()
    {    
        return $this->getResource()->getOptions($this->getAttributeId());        
    }
    
    // return array of all drop-down magento attributes which haven't linked to icons yet
    public function getAvailableAttributesAsOptions()
    {    
        $attributes =  $this->getResource()->getAvailableAttributes();        
        $options    = array();
        foreach ($attributes as $a){
            $options[] = array('value'=>$a['attribute_id'], 'label'=>$a['frontend_label']);
        }
        return $options;
    }
    
    protected function _beforeSave() {
        parent::_beforeSave();

        if ($this->getId()) {
            $options = $this->getOptions();
            foreach ($options as $info){
                $icon = Mage::getModel('adjicon/icon');
                /* @var $icon AdjustWare_Icon_Model_Icon */
                $icon->load($info['icon_id'])
                     ->upload($this->getAttributeId() , $info);
            }
        }
        else {
            //load attribute code
            $attrModel = Mage::getModel('eav/entity_attribute')->load($this->getAttributeId());
            $this->setAttributeCode($attrModel->getAttributeCode());
        }
        
        return $this;
    }

}