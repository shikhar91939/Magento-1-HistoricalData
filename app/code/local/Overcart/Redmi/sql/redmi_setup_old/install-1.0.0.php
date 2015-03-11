<?php

$installer = $this;

$installer->startSetup();

// echo $this->getTable('redmi');die;

$query1 = "DROP TABLE IF EXISTS {$this->getTable('redmi')};
		  CREATE TABLE IF NOT EXISTS {$this->getTable('redmi')} 
		  (
		  	`entity_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
		  	`product_id` varchar(20) NOT NULL DEFAULT '',
		    `product_name` varchar(100) NOT NULL DEFAULT '',
		    `product_url` varchar(255) NOT NULL,
		    `email_id` varchar(100) NOT NULL,
		    `mailsend_status` varchar(5) NOT NULL DEFAULT 'NO',
		    `status` smallint(1) NOT NULL DEFAULT '1',
		    `created_time` datetime DEFAULT NULL ,
		    `update_time` datetime DEFAULT NULL ,
		    PRIMARY KEY (`entity_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";

$installer->run($query1);

$query2 = "DROP TABLE IF EXISTS {$this->getTable('flash_sales')};
		  CREATE TABLE IF NOT EXISTS {$this->getTable('flash_sales')} 
		  (
		  	`entity_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
		  	`created_at` datetime DEFAULT NULL ,
		    `update_at` datetime DEFAULT NULL ,
		    `sale_code` varchar(50) NOT NULL DEFAULT '' ,
		    `sale_title` varchar(100) NOT NULL DEFAULT '',
		    `sale_start_time` datetime DEFAULT NULL ,
		    `sale_end_time` datetime DEFAULT NULL,
		    `status` smallint(1) NOT NULL DEFAULT '1',
		    PRIMARY KEY (`entity_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";

$installer->run($query2);

$installer->endSetup();