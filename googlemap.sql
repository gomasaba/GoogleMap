CREATE TABLE IF NOT EXISTS `pg_geo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `point` point() DEFAULT NULL,
  `zoom` decimal(2,0) DEFAULT NULL,
  PRIMARY KEY (`id`)
  PRIMARY KEY  (`id`)
);