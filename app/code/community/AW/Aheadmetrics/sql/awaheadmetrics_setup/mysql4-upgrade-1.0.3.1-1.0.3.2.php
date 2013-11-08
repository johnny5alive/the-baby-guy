<?php

/** @var Mage_Core_Model_Resource_Setup $this */
$this->startSetup();

/** @var Mage_AdminNotification_Model_Inbox $adminNotificationsModel */
$adminNotificationsModel = Mage::getModel('adminnotification/inbox');
$adminNotificationsModel->parse(array(array(
    'severity' => Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE,
    'date_added' => date('Y-m-d H:i:s'),
    'title' => 'Would you like to use aheadMetrics dashboard instead the native one?',
    'description' => 'Turn on the integration in the aheadMetrics section on the <strong>System</strong> -> <strong>Configuration</strong> backend page',
    'url' => '',
    'internal' => true
)));
$this->endSetup();
