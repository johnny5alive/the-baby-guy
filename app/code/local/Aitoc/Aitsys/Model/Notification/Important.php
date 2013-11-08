<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Notification_Important extends Aitoc_Aitsys_Model_Notification_News
{
    /**
     * @var string 
     */
    protected $_cacheKey = 'AITOC_AITSYS_NEWS_IMPORTANT';
    
    /**
     * @var string 
     */
    protected $_method = 'getNewsNotification';
    
    /**
     * @var string 
     */
    protected $_type = 'important';
    
    /**
     * @return Aitoc_Aitsys_Model_Notification_News
     */
    public function saveData()
    {
        $feedData = array();
        foreach ($this->_news as $item) {
            if ($item['title'] || $item['content']) {
                $feedData[] = array(
                    'severity'    => isset($item['severity']) ? $item['severity'] : Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR,
                    'date_added'  => isset($item['pubDate'])  ? $item['pubDate']  : date('Y-m-d H:i:s'),
                    'title'       => isset($item['title'])    ? $item['title']    : '',
                    'description' => isset($item['content'])  ? $item['content']  : '',
                    'url'         => (isset($item['link']) && $item['link']) ? $item['link'] : 'http://aitoc.com/#'.strtolower(preg_replace('/\W+/','_',$item['title']))
                );
            }
        }
        
        if ($feedData) {
            Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
        }
        return parent::saveData();
    }
}