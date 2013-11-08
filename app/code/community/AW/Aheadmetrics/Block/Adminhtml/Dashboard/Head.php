<?php
class AW_Aheadmetrics_Block_Adminhtml_Dashboard_Head extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aw_aheadmetrics/dashboard/head.phtml');
    }

    public function getProcessingServer()
    {
        return (string)Mage::getConfig()->getNode(
            'default/awaheadmetrics/processing'
        )->server;
    }
}
