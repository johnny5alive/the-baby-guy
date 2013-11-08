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
class AdjustWare_Icon_Block_Adminhtml_Icon extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_icon';
        $this->_headerText = Mage::helper('adjicon')->__('Manage Attribute Icons');
        $this->_addButtonLabel = Mage::helper('adjicon')->__('Fill Out');
        $this->_blockGroup = 'adjicon';
    }

}