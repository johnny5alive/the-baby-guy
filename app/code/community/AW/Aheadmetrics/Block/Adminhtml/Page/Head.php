<?php
class AW_Aheadmetrics_Block_Adminhtml_Page_Head extends Mage_Adminhtml_Block_Page_Head
{
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $js = $this
            ->getLayout()
            ->createBlock('awaheadmetrics/adminhtml_dashboard_head')
            ->toHtml();

        return $html . $js;
    }
}
