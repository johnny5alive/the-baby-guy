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
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `{$this->getTable('adjicon/attribute')}` 
ADD `show_images` TINYINT( 1 ) NOT NULL AFTER `pos` ,
ADD `columns_num` TINYINT( 1 ) NOT NULL AFTER `show_images` ,
ADD `hide_qty`    TINYINT( 1 ) NOT NULL AFTER `columns_num` ,
ADD `sort_by`     TINYINT( 1 ) NOT NULL AFTER `hide_qty` ;

");

$installer->endSetup();