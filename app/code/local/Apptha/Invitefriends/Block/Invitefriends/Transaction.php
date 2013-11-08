<?php

class Apptha_Invitefriends_Block_Invitefriends_Transaction extends Mage_Core_Block_Template
{

    protected function _prepareLayout()
    {
		$this->setToolbar($this->getLayout()->createBlock('page/html_pager','invitefriends_transaction_toolbar'));
		$this->getToolbar()->setCollection($this->_getTransaction());
    }
	protected function _getCustomer()
	{
		return Mage::getSingleton("customer/session")->getCustomer();
	}
	
	public function _getTransaction()
	{
		if($this->getPageSize()) $pagesize = $this->getPage_size();
		$transactions = Mage::getModel('invitefriends/invitefriends')->getCollection()
						->addFieldToFilter('customer_id',$this->_getCustomer()->getId())
						->addOrder('transaction_time','DESC')
						->addOrder('history_id','DESC');
		return $transactions;
	}
	
	public function getTransaction()
	{
		return $this->getToolbar()->getCollection();
	}
	
	public function getTypeLabel($type)
	{
		return Apptha_Invitefriends_Model_Type::getLabel($type);
	}
	
	public function getTransactionDetail($type, $detail=null, $status=null)
	{
		return Apptha_Invitefriends_Model_Type::getTransactionDetail($type,$detail,$status);
	}
	
	public function formatAmount($amount, $type)
	{
		return Apptha_Invitefriends_Model_Type::getAmountWithSign($amount,$type);
	}
	
	public function getPositiveAmount($amount, $type)
	{
		$result = Apptha_Invitefriends_Model_Type::getAmountWithSign($amount,$type);
		return $result>0?$result:0;
	}
	
	public function getStatusText($status)
	{
		return Apptha_Invitefriends_Model_Status::getLabel($status);
	}
	
	public function getToolbarHtml()
	{
		return $this->getToolbar()->toHtml();
	}

}