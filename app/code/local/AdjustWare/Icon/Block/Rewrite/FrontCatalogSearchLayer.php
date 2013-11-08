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
class AdjustWare_Icon_Block_Rewrite_FrontCatalogSearchLayer extends Mage_CatalogSearch_Block_Layer
{
    protected $_filterBlocks = null;
    
    public function getFilters()
    {
        if (is_null($this->_filterBlocks)){
            $this->_filterBlocks = parent::getFilters();
            Mage::helper('adjicon')->addIconsToFilters($this->_filterBlocks);
        }	    
        return $this->_filterBlocks;
    }
}