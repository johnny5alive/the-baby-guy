<?php

class Apptha_Invitefriends_Block_Adminhtml_Customer_Edit_Tab_Invitefriends_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('Invitefriends_Grid');
		$this->setDefaultSort('transaction_time');
		$this->setDefaultDir('desc');

		$this->setUseAjax(true);
		$this->setTemplate('invitefriends/grid.phtml');
		$this->setEmptyText(Mage::helper('invitefriends')->__('No Transaction Found'));
	}

	public function getGridUrl()
	{
		return $this->getUrl('invitefriends/adminhtml_invitefriends/transaction', array('id'=>Mage::registry('current_customer')->getId()));

	}

	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('invitefriends/invitefriends_collection')
		->addFieldToFilter('customer_id',Mage::registry('current_customer')->getId());

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('history_id', array(
            'header'    =>  Mage::helper('invitefriends')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'history_id',
            'width'     =>  10
		));

		$this->addColumn('type_of_transaction', array(
            'header'    =>  Mage::helper('invitefriends')->__('Transaction Type'),
        	'type'		=>	'options',
            'align'     =>  'left',
            'index'     =>  'type_of_transaction',
        	'options'	=>  Mage::getModel('invitefriends/type')->getOptionArray()
		));

		$this->addColumn('amount', array(
            'header'    =>  Mage::helper('invitefriends')->__('Amount'),
            'align'     =>  'left',
            'index'     =>  'amount',
		));

		$this->addColumn('balance', array(
            'header'    =>  Mage::helper('invitefriends')->__('Balance'),
            'align'     =>  'left',
            'index'     =>  'balance',
		));
		$this->addColumn('transaction_detail', array(
            'header'    =>  Mage::helper('invitefriends')->__('Transaction Details'),
            'align'     =>  'left',
        	'width'		=>  400,
            'index'     =>  'transaction_detail',
		));

		$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('invitefriends')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
            ));

       /*$this->addColumn('expires', array(
          'header'    => Mage::helper('invitefriends')->__('Expires'),
          'align'     => 'left',
          'width'     => '80px',
          'renderer'  => 'Apptha_invitefriends_Block_Adminhtml_invitefriends_Renderer_Expires',
          'type'      => 'text',
          'index'     => 'transaction_time',
            ));*/

       $this->addColumn('status', array(
            'header'    =>  Mage::helper('invitefriends')->__('Status'),
        	'type'		=> 'options',
            'align'     =>  'center',
            'index'  	=>  'status',
        	'options'	=>	Mage::getModel('invitefriends/status')->getOptionArray()
            ));

            return parent::_prepareColumns();
	}

}
