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
class AdjustWare_Icon_Block_Adminhtml_Icon_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('iconsGrid');
        $this->setDefaultSort('pos');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('adjicon/attribute')
            ->getResourceCollection()
            ->addTitles();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('adjicon');
        $this->addColumn('id', array(
            'header'    => $hlp->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
        ));
        /*
        $this->addColumn('hide', array(
            'header'    => $hlp->__('Hidden'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'hide',
            'type'      => 'options', 
            'options'   => array($hlp->__('No'), $hlp->__('Yes')),
        ));
        */
        $this->addColumn('pos', array(
            'header'    => $hlp->__('Position'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'pos',
        ));
        
        $this->addColumn('frontend_label', array(
            'header'    => $hlp->__('Attribute'),
            'align'     =>'left',
            'index'     => 'frontend_label',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}