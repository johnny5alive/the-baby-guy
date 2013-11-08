<?php
class AW_Aheadmetrics_Model_Resource_Sync_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('awaheadmetrics/sync');
    }
}
