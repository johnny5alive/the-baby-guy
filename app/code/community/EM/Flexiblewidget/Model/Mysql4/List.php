<?php

class EM_Flexiblewidget_Model_Mysql4_List extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the blog_id refers to the key field in your database table.
        $this->_init('flexiblewidget/list', 'entity_id');
    }
}