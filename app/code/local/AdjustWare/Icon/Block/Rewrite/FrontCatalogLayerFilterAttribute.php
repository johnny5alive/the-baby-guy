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
class AdjustWare_Icon_Block_Rewrite_FrontCatalogLayerFilterAttribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adjicon/filter_attribute.phtml'); 
    }
    
    public function getItemsArray()
    {
        return Mage::helper('adjicon')->collectAttributeFilterItems($this);
    }
}