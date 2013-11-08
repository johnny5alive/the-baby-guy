<?php
/**
 * Visualize Your Attributes
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Icon
 * @version      2.0.18
 * @license:     GPC7g2VHtpIP7j623srVjJmuippj4X9BeOkIuhMsJs
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Icon_Model_Icon extends Mage_Core_Model_Abstract
{
    protected $_iconPath;

    protected $_resizeOptions = array();

    public function _construct()
    {
        parent::_construct();
        $this->_init('adjicon/icon');
        $this->_iconPath = Mage::getBaseDir('media') . DS . 'icons' . DS;

        $this->setResizeOptions($this->_getDafaultResizeOptions());
    }
    
    public function upload($attributeId, $attributeOptionInfo)
    {
        $fieldName = 'option_' . $attributeOptionInfo['option_id'];
        if (empty($_FILES[$fieldName]['name'])) {
            return $this;
        }
        
        // create a human readable name
        $iconName = $attributeId . '_' . $attributeOptionInfo['option_id'] . '_';  // for better debug/maintenance
        $iconName .= preg_replace('/[^a-z0-9]+/', '', strtolower($attributeOptionInfo['value'])); // seo / userfriendly
        $iconName .= '_' . rand(0, 99); // to prevent browser cache
        $iconName .= '.' . strtolower(substr(strrchr($_FILES[$fieldName]['name'], '.'), 1)); // keep original extension

        //upload an icon file
        $uploader = new Varien_File_Uploader($fieldName);
        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $uploader->save($this->_iconPath, $iconName);

        // create and save thumbnail
        $this->makeThumb($iconName);


        // store new values in DB
        $oldFile = $this->getFilename();
        if ($oldFile){
            @unlink($this->_iconPath . $oldFile);
            @unlink($this->_iconPath . 's_' . $oldFile); 
        }
        $this->setOptionId($attributeOptionInfo['option_id']);
        $this->setFilename($iconName);
        $this->save();

    }
    
    public function makeThumb($iconName = false)
    {
        if (!$iconName) {
            $iconName = $this->getFilename();
        }
        if (!$this->_resizeIcon($iconName)){
            $this->copyThumb($iconName);
        }
    }
    
    public function copyThumb($iconName)
    {
        
        @copy($this->_iconPath . $iconName, $this->_iconPath . 's_' .$iconName);
        @chmod($this->_iconPath . 's_' .$iconName, 0644);
    }


    protected function _getDafaultResizeOptions()
    {                
        return array (
            'resize' => Mage::getStoreConfig('design/adjicon/resize'),
            'keep_ration' => Mage::getStoreConfigFlag('design/adjicon/keep_ratio'),
            'keep_frame' => Mage::getStoreConfigFlag('design/adjicon/keep_frame'),
            'width' => intVal(Mage::getStoreConfig('design/adjicon/width')) < 1 ? null : intVal(Mage::getStoreConfig('design/adjicon/width')),
            'height'=> intVal(Mage::getStoreConfig('design/adjicon/height')) < 1 ? null : intVal(Mage::getStoreConfig('design/adjicon/height')),
            'transparency' => true,
        );
    }
    
    public function setResizeOptions($options)
    {
        $this->_resizeOptions = array_merge($this->_resizeOptions, $options);
        return $this;
    }
    
    protected function _getResizeParam($param)
    {
        if (isset ($this->_resizeOptions[$param])) {
            return $this->_resizeOptions[$param];
        }
        return null;
    }

    protected function _resizeIcon($iconName)
    {
        if (!($this->_getResizeParam('resize') && ($this->_getResizeParam('width') || $this->_getResizeParam('height')))) {
            return false;
        }
        
        $image = new Varien_Image($this->_iconPath . $iconName);
        $image->keepFrame($this->_getResizeParam('keep_frame') ? true : false);
        $image->keepAspectRatio($this->_getResizeParam('keep_ration') ? true : false);
        $image->keepTransparency($this->_getResizeParam('transparency'));
        $image->resize($this->_getResizeParam('width'), $this->_getResizeParam('height'));
        $image->save(null, 's_' .$iconName);
        
        return true;
    }
}