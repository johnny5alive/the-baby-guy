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
class AdjustWare_Icon_Helper_Data extends Mage_Core_Helper_Abstract
{
    // icons and titles
    protected $_icons = null;
    // attributes with images
    protected $_attributes = null;
    
    // use array(_product) on the product page to uniform interface
    public function init($productCollection){
        if (!is_null($this->_icons))
            return;
            
        $attributesCollection = $this->getAttributes();
        $ids = array();    
        foreach ($productCollection as $prod){
            foreach ($attributesCollection as $attr){
                $optionIds  = trim($prod->getData($attr->getAttributeCode()), ',');
                if ($optionIds){
                    $ids = array_merge($ids, explode(',', $optionIds));  
                }
            }
        } 
        
        $this->_icons = Mage::getModel('adjicon/attribute')
                ->getIconsByOptions($ids, Mage::app()->getStore()->getId());
    }
    
    // get only attributes with icons
    public function getAttributes(){
        if (is_null($this->_attributes)){
            $this->_attributes = Mage::getResourceModel('adjicon/attribute_collection')
                ->setOrder('pos','asc')
                ->load();
        }
        return $this->_attributes;
    }
    
    public function display($product, $mode='view'){
        
        if (is_null($this->_icons))
            return $this->__('Please insert initialization code in the page template');    
            
        if (!$this->_icons)
            return '';
            
        $icons = array();  
        $prefix = ('view' == $mode ? '' : 's_'); // show full image only on product page
        foreach ($this->getAttributes() as $attr){
            $code = $attr->getAttributeCode(); 
            if (!$code) // it is unreal, howerer ...
                continue;
                
            $optionIds  = trim($product->getData($attr->getAttributeCode()), ',');
            if ($optionIds){
                $optionIds = explode(',', $optionIds);
                foreach ($optionIds as $id){
                    if (!empty($this->_icons[$id])){
                        $icons[] = array(
                            'title'   => $this->_icons[$id][0], 
                            'filename'=> $prefix . $this->_icons[$id][1]
                        );
                    }
                }
            }
        }  
         
        $block = Mage::getModel('core/layout')->createBlock('core/template')
            ->setArea('frontend')
            ->setTemplate('adjicon/icons.phtml');
        $block->assign('_type', 'html')
            ->assign('_section', 'body')        
            ->setIcons($icons)
            ->setMode($mode); 
             
        return $block->toHtml();         
    }
    
    //layered navigation
    public function addIconsToFilters($filters)
    {
        // get option_id from items
        $ids = array();
        // get attributes_ids from items
        $attrIds = array();
        foreach ($filters as $f){
            if ($f->getItemsCount() && ($f instanceof Mage_Catalog_Block_Layer_Filter_Attribute)){
                $items = $f->getItems();
                foreach ($items as $item){    
                    $ids[] = $item->getValue();
                }
                $attrIds[] = $items[0]->getFilter()->getAttributeModel()->getId();
            }
        }
        
        // load attributes
        $attrCollection = Mage::getResourceModel('adjicon/attribute_collection')
                ->addFieldToFilter('attribute_id', array('in'=>$attrIds)) 
                ->load();
        // convert to hash 
        $attributes = array();
        foreach ($attrCollection as $row){
            $attributes[$row->getAttributeId()] = $row;
        }
        
        // load icons
        $iconCollection = Mage::getResourceModel('adjicon/icon_collection')
                ->addFieldToFilter('option_id', array('in'=>$ids)) 
                ->load();
        //convert to hash 
        $icons = array();        
        foreach ($iconCollection as $row){
            $icons[$row->getOptionId()] = 's_' . $row->getFilename();
        }

        // set values back to attributes(blocks) and options    
        foreach ($filters as $f){
            if ($f->getItemsCount() && $f instanceof Mage_Catalog_Block_Layer_Filter_Attribute){
                $items = $f->getItems();
                $attributeId = $items[0]->getFilter()->getAttributeModel()->getId();
                if (isset($attributes[$attributeId])){
                   
                    $a = $attributes[$attributeId];
                    
                    $f->setHideQty($a->getHideQty());
                    $f->setShowImages($a->getShowImages());
                    $f->setColumnsNum($a->getColumnsNum());
                    
                    if ($a->getShowImages()){
                        foreach ($f->getItems() as $item){   
                            if (!empty($icons[$item->getValue()])){
                                $item->setIcon($icons[$item->getValue()]);
                            }
                        }
                    }
                    
                } //if attibute
            }// if count items
        }
    }
    
    public function collectAttributeFilterItems(Mage_Catalog_Block_Layer_Filter_Abstract $filterAttribute)
    {
        $items = array(); 
        
        $displayStyle = $filterAttribute->getColumnsNum();
        $onlyIcons = (3 == $displayStyle);
        foreach ($filterAttribute->getItems() as $_item){
            
            $htmlParams = 'href="' . $filterAttribute->htmlEscape($_item->getUrl()) . '" ';
            if ($onlyIcons)
                $htmlParams .= 'class="adj-nav-icon"';
                
            $icon = '';
            if ($_item->getIcon()){
                $icon = '<span class="adjicon_icon_img"><img border="0" alt="'.$_item->getLabel().'" src="'. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/icons/'.$_item->getIcon().'" /></span>';
            } else 
            {
                $icon = '<span class="adjicon_icon_img"></span>';
            }
        
            $qty = '';
            if (!$filterAttribute->getHideQty()) 
                $qty =  ' (' .  $_item->getCount() .')';
        
            $label = $_item->getLabel();
            if ($onlyIcons){
                $label = '';
            }
            $label = $icon . $label;
            
            $items[] = '<a '.$htmlParams.'>'.$label.'</a>'.$qty;
        }
        return $items;
    }
}