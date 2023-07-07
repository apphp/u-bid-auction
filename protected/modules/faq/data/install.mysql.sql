
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'faq', 'Faq', 'FAQ', 'FAQ module allows creating and displaying FAQ on the site', '0.0.3', 'icon.png', 0, 0, 1, 0, 1, '<CURRENT_DATETIME>', NULL, 0, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 0));


INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'faq', '', 'shortcode', '{module:faq}', 'Shortcode', 'This shortcode allows you to display faq on the site pages', 'label', '', '', '', '', 0);


INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'faq', 'faq', 'add', 'Add FAQ', 'Add faq on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'faq', 'faq', 'edit', 'Edit FAQ', 'Edit faq on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'faq', 'faq', 'delete', 'Delete FAQ', 'Delete faq from the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);


DROP TABLE IF EXISTS `<DB_PREFIX>faq_categories`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>faq_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>faq_categories` (`id`, `sort_order`, `is_active`) VALUES
(1, 0, 1);

DROP TABLE IF EXISTS `<DB_PREFIX>faq_category_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>faq_category_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_category_id` int(11) DEFAULT '0',
  `language_code` varchar(3) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `category_name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>faq_category_translations` (`id`, `faq_category_id`, `language_code`, `category_name`) SELECT NULL, 1, code, 'FAQ' FROM `<DB_PREFIX>languages`;


DROP TABLE IF EXISTS `<DB_PREFIX>faq_category_items`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>faq_category_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_category_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` tinyint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>faq_category_items` (`id`, `faq_category_id`, `sort_order`, `is_active`) VALUES
(1, 1, 0, 1),
(2, 1, 1, 1),
(3, 1, 2, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>faq_category_item_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>faq_category_item_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_category_item_id` int(11) DEFAULT '0',
  `language_code` varchar(3) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `faq_question` VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `faq_answer` VARCHAR(2048) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>faq_category_item_translations` (`id`, `faq_category_item_id`, `language_code`, `faq_question`, `faq_answer`) SELECT NULL, 1, code, 'How does uBidAuction work?', '<p>Bids can be purchased in packages at the dashboard. Each time you place a bid; it will be deducted from your remaining bids, and will increase the price of the auction.</p><p>And also just like a traditional auction, you win by being the last to bid. Once you pay the final auction price and shipping costs, the item is yours!</p>' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>faq_category_item_translations` (`id`, `faq_category_item_id`, `language_code`, `faq_question`, `faq_answer`) SELECT NULL, 2, code, 'What is Buy Now?', '<p>With Buy Now, you can purchase any product at any time during the auction by clicking the Buy Now button on the auction page.</p>' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>faq_category_item_translations` (`id`, `faq_category_item_id`, `language_code`, `faq_question`, `faq_answer`) SELECT NULL, 3, code, 'What`s the best way to win an auction?', '<p>Everyone wants to know how to improve their chances of winning an auction. There isn`t a guaranteed winning strategy, but we`ll offer you a few tips to help get you started:</p><p><strong>Place your bid within the last 15 seconds</strong><br>By placing a bid in the last few seconds, you know the time will reset and you`ll become the highest bidder. If someone else bids, time will be added and you`ll get another chance to bid.</p><p><strong>Bid on the cheapest products</strong><br>You can get some great deals on our most expensive items. However, these items also tend to generate the most competitive auctions. Fewer bidders bid on cheaper items, meaning that you are much more likely to win.</p>' FROM `<DB_PREFIX>languages`;

