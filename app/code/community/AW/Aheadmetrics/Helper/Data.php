<?php

class AW_Aheadmetrics_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return AW_Aheadmetrics_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('awaheadmetrics/config');
    }

    public function getDomainUrl($domainName)
    {
        return str_replace('/app.', '/' . $domainName . '.', $this->_getConfigHelper()->getProcessingServer());
    }

    public function getVersion()
    {
        return (string)Mage::getConfig()->getModuleConfig('AW_Aheadmetrics')->version;
    }
    
    public function isDebugMode()
    {
        return (bool)preg_match('/.dev$/', $this->_getConfigHelper()->getProcessingServer());
    }

    /**
     * Check is extension installed, active and not
     * disabled in the Advanced > Disable Output tab
     * @param $name - extension name
     * @return bool - results of check
     */
    public function isExtensionInstalled($name)
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        return array_key_exists($name, $modules)
        && 'true' == (string)$modules[$name]->active
        && !(bool)Mage::getStoreConfig('advanced/modules_disable_output/' . $name);
    }

    /**
     * Check extension version
     * @param $extensionName
     * @param $extVersion
     * @param string $operator
     * @return bool
     */
    public function checkExtensionVersion($extensionName, $extVersion, $operator = '>=')
    {
        if ($this->isExtensionInstalled($extensionName) && ($version = Mage::getConfig()->getModuleConfig($extensionName)->version)) {
            return version_compare($version, $extVersion, $operator);
        }
        return false;
    }
}
