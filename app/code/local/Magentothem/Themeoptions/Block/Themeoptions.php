<?php
class Magentothem_Themeoptions_Block_Themeoptions extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getThemeoptions()     
     { 
        if (!$this->hasData('themeoptions')) {
            $this->setData('themeoptions', Mage::registry('themeoptions'));
        }
        return $this->getData('themeoptions');
        
    }
	public function getConfig($att) 
	{
		$config = Mage::getStoreConfig('themeoptions');
		if (isset($config['themeoptions_config']) ) {
			$value = $config['themeoptions_config'][$att];
			return $value;
		} else {
			throw new Exception($att.' value not set');
		}
	}
}