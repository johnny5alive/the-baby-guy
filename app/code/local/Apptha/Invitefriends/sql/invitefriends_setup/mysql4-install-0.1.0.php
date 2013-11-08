<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('apptha_invitefriends_customer')};
CREATE TABLE {$this->getTable('apptha_invitefriends_customer')} (
   `customer_id` int(11) NOT NULL,
  `token_id` varchar(255) NOT NULL,
  `fbuserid` varchar(100) NOT NULL,
  `fbfriendids` text,
  `friend_id` int(11) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `credit_amount` decimal(12,2) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `bonus_flag` tinyint(2) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('apptha_invitefriends_history')};
CREATE TABLE {$this->getTable('apptha_invitefriends_history')} (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `type_of_transaction` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `balance` decimal(12,2) NOT NULL,
  `transaction_detail` varchar(255) CHARACTER SET utf8 NOT NULL,
  `transaction_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 