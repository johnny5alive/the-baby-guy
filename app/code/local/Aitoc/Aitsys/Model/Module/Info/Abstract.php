<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
abstract class Aitoc_Aitsys_Model_Module_Info_Abstract
{
    const EE_MIN_VERSION = '10.0.0';
    
    const PLATFORM_CE    = 'community';
    const PLATFORM_EE    = 'enterprise';
    
    /**
     * @var array
     */
    protected $_platforms = array(
        self::PLATFORM_CE,
        self::PLATFORM_EE
    );
    
    /**
     * @var bool
     */
    protected $_loaded = false;
    
    /**
     * @var Aitoc_Aitsys_Model_Module
     */
    protected $_module;
    
    /**
     * @var string
     */
    protected $_codepool;
    
    /**
     * @var string
     */
    protected $_platform = '';

    /**
     * @abstract
     * @return string
     */
    abstract public function getVersion();
    
    /**
     * @abstract
     * @return string
     */
    abstract public function getPlatform();
    
    /**
     * @abstract
     * @return string
     */
    abstract public function getSerial();
    
    public function __construct(Aitoc_Aitsys_Model_Module $module, $codepool = 'local')
    {
        $this->_module = $module;
        $this->_codepool = $codepool;
        try {
            $this->_init();
        } catch (Exception $e) {
            $this->_loaded = false;
        }
    }
    
    protected function _init()
    {
    }
    
    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_loaded;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Module
     */
    public function getModule()
    {
        return $this->_module;
    } 
    
    /**
     * @return bool
     */
    public function getCodepool()
    {
        return $this->_codepool;
    }
        
    /**
     * @return string
     */
    protected function _getFallbackPlatform()
    {
        return version_compare($this->getVersion(), self::EE_MIN_VERSION, 'ge') ? self::PLATFORM_EE : self::PLATFORM_CE;
    }
    
    /**
     * @return bool
     */
    public function isEnterpriseVersion()
    {
        return $this->getPlatform() == self::PLATFORM_EE;
    }

    /**
     * @return bool
     */
    public function isCommunityVersion()
    {
        return $this->getPlatform() == self::PLATFORM_CE;
    }

    /**
     * @return bool
     */
    public function isMagentoCompatible()
    {
        $isMagentoEE = Aitoc_Aitsys_Model_Platform::getInstance()->getEntHash();
        return ((!$isMagentoEE && $this->isCommunityVersion()) || ($isMagentoEE && $this->isEnterpriseVersion()));
    }
    
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getModule()->getLabel();
    }
    
    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getModule()->getId();
    }
}