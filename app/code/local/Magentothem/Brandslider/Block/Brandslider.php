<?php
class Magentothem_Brandslider_Block_Brandslider extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getBrandslider()     
    { 
        if (!$this->hasData('brandslider')) {
            $this->setData('brandslider', Mage::registry('brandslider'));
        }
        return $this->getData('brandslider');
    }
	public function getDataBrandslider()
    {
    	$resource = Mage::getSingleton('core/resource');
		$read= $resource->getConnection('core_read');
		$slideTable = $resource->getTableName('brandslider');	
		$select = $read->select()
		   ->from($slideTable,array('brandslider_id','title','link','description','image','status'))
		   ->where('status=?',1);
		$slide = $read->fetchAll($select);	
		return 	$slide;			
    }
	public function getConfig($att) 
	{
		$config = Mage::getStoreConfig('brandslider');
		if (isset($config['brandslider_config']) ) {
			$value = $config['brandslider_config'][$att];
			return $value;
		} else {
			throw new Exception($att.' value not set');
		}
	}
}