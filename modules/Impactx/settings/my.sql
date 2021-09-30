INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('impactx', 'Impactx Customization', 'Impactx Customization', '5.0.0', 1, 'extra') ;

ALTER TABLE `engine4_yndynamicform_entries` ADD `publish` TINYINT( 4 ) NOT NULL DEFAULT '1';
ALTER TABLE `engine4_sitepage_pages` ADD `organization_map` TINYINT( 4 ) NOT NULL DEFAULT '0';