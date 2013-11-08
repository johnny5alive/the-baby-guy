<?php
$installer = $this;

$installer->startSetup();

$installer->run(<<<SQL
CREATE TABLE IF NOT EXISTS `{$installer->getTable('awaheadmetrics/sync')}` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Data ID',
    `sync_data` text NOT NULL COMMENT 'Data',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='aheadAnalytics sync table';
SQL
);

$installer->endSetup();
