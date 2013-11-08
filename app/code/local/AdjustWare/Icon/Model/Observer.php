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
class AdjustWare_Icon_Model_Observer 
{
    public function bindConfigChanges(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        /* @var $action Mage_Core_Controller_Varien_Action */
        
        if ('adminhtml_system_config_save' != $action->getFullActionName() || $action->getRequest()->getParam('section') != 'design') {
            return;
        }
        
        $groups = $action->getRequest()->getPost('groups');
        $website = $action->getRequest()->getParam('website');
        $store   = $action->getRequest()->getParam('store');
        if (array_key_exists('adjicon', $groups)) {
            if ($this->_isResizeConfigChanged($groups['adjicon'])) {
                Mage::register('adjicon_resize_config_changed', true);
            }
        }
    }
    
    protected function _isResizeConfigChanged($newConfig)
    {
        $newConfig = $this->_convertInputGroupData($newConfig);
        $oldConfig = Mage::getConfig()->getNode('default/design/adjicon')->asArray();
        return $newConfig != $oldConfig;
    }
    
    protected function _convertInputGroupData($newConfig)
    {
        $simpleConfig = array();
        foreach ($newConfig['fields'] as $k => $value) {
            $simpleConfig[$k] = $value['value'];
        }
        return $simpleConfig;
    }
    
    public function resizeIcons(Varien_Event_Observer $observer)
    {
        if (!Mage::registry('adjicon_resize_config_changed')) {
            return;
        }
        $iconCollection = Mage::getResourceModel('adjicon/icon_collection');
        foreach ($iconCollection as $icon) {
            $icon->setResizeOptions(Mage::getConfig()->getNode('default/design/adjicon')->asArray())->makeThumb();
        }
    }
}