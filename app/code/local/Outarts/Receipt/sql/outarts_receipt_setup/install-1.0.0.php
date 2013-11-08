<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales_flat_order'), 'receipt_type', 'varchar(10)');
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order'), 'receipt_by_buy', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order'), 'receipt_uniform_number', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote'), 'receipt_type', 'varchar(10)');
$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote'), 'receipt_by_buy', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote'), 'receipt_uniform_number', 'varchar(255)');

$installer->endSetup();