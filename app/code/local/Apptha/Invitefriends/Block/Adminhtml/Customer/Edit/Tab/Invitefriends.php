<?php

class Apptha_Invitefriends_Block_Adminhtml_Customer_Edit_Tab_Invitefriends extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('invitefriends/customer/tab/invitefriends.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_invitefriends');       
        $this->setForm($form);
        return $this;
    }


    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('invitefriends/adminhtml_customer_edit_tab_invitefriends_grid','invitefriends.grid')
        );
        return parent::_prepareLayout();
    }

}
