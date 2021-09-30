ALTER TABLE `engine4_sitepage_categories`
 ADD `category_slug` VARCHAR(128) NOT NULL AFTER `category_name`,
 ADD `photo_id` INT NOT NULL DEFAULT '0' AFTER `subcat_dependency`,
 ADD `banner_id` INT NOT NULL DEFAULT '0' AFTER `file_id`,
 ADD `banner_title` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `banner_id`,
 ADD `banner_url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `banner_title`,
 ADD `banner_url_window` TINYINT NOT NULL DEFAULT '0' AFTER `banner_url`,
 ADD `sponsored` TINYINT NOT NULL DEFAULT '0' AFTER `banner_url_window`,
 ADD `profile_type` INT NOT NULL DEFAULT '0' AFTER `sponsored`,
 ADD `profile_type_review` INT NOT NULL DEFAULT '0' AFTER `profile_type`,
 ADD `meta_title` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `profile_type_review`,
 ADD `meta_description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `meta_title`,
 ADD `meta_keywords` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `meta_description`,
 ADD `top_content` TEXT NOT NULL AFTER `meta_keywords`,
 ADD `bottom_content` TEXT NULL DEFAULT NULL AFTER `top_content`,
 ADD `allow_guestreview` TINYINT NOT NULL DEFAULT '0' AFTER `bottom_content`,
 ADD `userreview` TINYINT NOT NULL DEFAULT '0' AFTER `allow_guestreview`;