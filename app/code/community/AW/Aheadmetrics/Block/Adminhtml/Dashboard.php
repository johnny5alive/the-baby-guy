<?php
class AW_Aheadmetrics_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aw_aheadmetrics/dashboard/index.phtml');
    }

    protected function _beforeChildToHtml($name, $child)
    {
        $this
            ->getChild('store_switcher')
            ->setTemplate('aw_aheadmetrics/dashboard/store/switcher.phtml');
        return parent::_beforeChildToHtml($name, $child);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}
