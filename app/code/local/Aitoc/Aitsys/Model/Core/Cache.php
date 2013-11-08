<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

/**
 * Main adapter model for magento cache
 */
class Aitoc_Aitsys_Model_Core_Cache
{
    const AITSYS_CACHE_GROUP  = 'aitsys';
    const DEFAULT_LIFE_TIME = 86400; // 24 hours in seconds

    /**
     * Flush an entire magento cache
     * 
     * @return Aitoc_Aitsys_Model_Core_Cache
     */
    public function flush()
    {
        if(version_compare(Mage::getVersion(),'1.4','>='))
        {
            Mage::app()->getCacheInstance()->flush();
        }
        else
        {
            Mage::app()->getCache()->clean();
        }
        Mage::getConfig()->reinit();
        if (sizeof(Mage::getConfig()->getNode('aitsys')->events))
        {
            Mage::app()->addEventArea('aitsys');
        }
        if(!Mage::app()->getUpdateMode())
        {
            Mage_Core_Model_Resource_Setup::applyAllUpdates();
        }
        return $this;
    }

    /**
     * Save element to cache if cache is enabled.
     *
     * @param mixed $data
     * @param string $id
     * @param bool $serialize Sholud be set `true` for any non-simple data
     * @param integer $lifeTime Timeout in seconds before data will be considered out of date. Default 8 hours. Use `0` to cache element permanently.
     * @return Aitoc_Aitsys_Model_Core_Cache
     */
    public function save($data, $id, $serialize = true, $lifeTime = self::DEFAULT_LIFE_TIME)
    {
        if($this->isEnabled())
        {
            if($serialize)
            {
                $data = serialize($data);
            }
            Mage::app()->saveCache($data, $id, array(self::AITSYS_CACHE_GROUP), $lifeTime);
        }
        return $this;
    }
    
    /**
     * Load data from cache by its id. Uses defaule value if element not found or cache is disabled.
     *  
     * @param string $id
     * @param mixed $defaultValue
     * @param bool $unserialize Sholud be set `true` for any non-simple data
     * @return mixed 
     */
    public function load($id, $defaultValue = null, $unserialize = true)
    {
        $result = null;
        if($this->isEnabled())
        {
            $result = Mage::app()->loadCache($id);
        }
        if($result)
        {
            if($unserialize)
            {
                try
                {
                    $result = unserialize($result);
                }
                catch(Exception $e)
                {
                    $result = null;
                }
            }
        }
        return $result ? $result : $defaultValue;
    }
    
    /**
     * Remove element from cache by its id
     *
     * @param string $id
     * @return Aitoc_Aitsys_Model_Core_Cache
     */
    public function remove($id)
    {
        Mage::app()->removeCache($id);
        return $this;
    }
    
    /**
     * Checks whether aitsys cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::app()->useCache(self::AITSYS_CACHE_GROUP);
    }
}