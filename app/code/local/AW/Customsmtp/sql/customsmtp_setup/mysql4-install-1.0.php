<?php

$installer = $this;

$installer->startSetup();
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('customsmtp/mail')} (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `subject` varchar(255) NOT NULL,
	  `is_plain` tinyint(1) NOT NULL DEFAULT '0',
	  `body` text NOT NULL,
	  `from_email` varchar(255) NOT NULL,
	  `from_name` varchar(255) NOT NULL,
	  `to_email` varchar(255) NOT NULL,
	  `reply_to` varchar(255) NOT NULL,
	  `to_name` varchar(255) NOT NULL,
	  `date` datetime DEFAULT NULL,
	  `status` enum('failed','pending','processed','in_progress') NOT NULL DEFAULT 'processed',
	  `template_id` varchar(255) NOT NULL,
	  `store_id` int(4) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`),
	  KEY `subject` (`subject`),
	  KEY `is_plain` (`is_plain`),
	  KEY `from_email` (`from_email`),
	  KEY `from_name` (`from_name`),
	  KEY `to_email` (`to_email`),
	  KEY `to_name` (`to_name`),
	  KEY `date` (`date`),
	  KEY `status` (`status`),
	  KEY `template_id` (`template_id`,`store_id`),
	  KEY `reply_to` (`reply_to`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
?>