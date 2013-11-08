<?php

$installer = $this;
$installer->startSetup();
$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('yotpo/richsnippet')}` (
      `rich_snippet_id` int(11) NOT NULL auto_increment,
      `product_id` int(11) NOT NULL,
      `html_code` text,
      `expiration_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
      PRIMARY KEY  (`rich_snippet_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("CREATE UNIQUE INDEX yotpo_rich_snippets_product_id_i
ON `{$installer->getTable('yotpo/richsnippet')}` (`product_id`);");

//add notice to notification inbox if no app key or secret is inserted yet
if (!Mage::getStoreConfig('yotpo/yotpo_general_group/yotpo_appkey',Mage::app()->getStore()) || !Mage::getStoreConfig('yotpo/yotpo_general_group/yotpo_appkey',Mage::app()->getStore())) {
	Mage::helper('yotpo/Utils')->createAdminNotification
				(
					"Please visit the Yotpo extension page in your system configuration and finish the installation.", 
					"In order to start generating reviews with Yotpo, you'll need to finish the installation process",
					"http://support.yotpo.com/entries/24858236-Configuring-Yotpo-after-installation?utm_source=customers_magento_admin&utm_medium=pop_up&utm_campaign=magento_not_installed_pop_up"
				);	
}
$installer->endSetup();