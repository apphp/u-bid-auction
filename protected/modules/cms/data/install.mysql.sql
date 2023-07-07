
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'cms', 'Cms', 'Content Management', 'CMS module allows management of site content', '0.0.5', 'icon.png', 1, 1, 1, 0, 1, '<CURRENT_DATETIME>', NULL, 0, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 0));


INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'cms', '', 'page_link_format', 'pages/view/id/ID', 'Page Link Format', 'Defines a SEO format for page links that will be used on the site', 'enum', 'pages/view/id/ID,pages/view/id/ID/Name,pages/view/ID,pages/view/ID/Name,pages/ID,pages/ID/Name', '', '', '', 0);


INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'cms', 'pages', 'add', 'Add Pages', 'Add Pages on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'cms', 'pages', 'edit', 'Edit Pages', 'Edit Pages on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'cms', 'pages', 'delete', 'Delete Pages', 'Delete Pages from the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1);


INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, 0, '', 'cms', 'cms.png', 0, 1, 6);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Content Management' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'cms' AND bm.parent_id = 0), '<SITE_BO_URL>modules/settings/code/cms', 'cms', '', 0, 1, 0);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Settings' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'cms' AND bm.parent_id = 0), 'pages/manage', 'cms', '', 0, 1, 1);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Pages' FROM `<DB_PREFIX>languages`;

INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'cms', 'pages/view/id/3', '', 'top', 2, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'How it works' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'cms', 'pages/view/id/4', '', 'top', 3, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'FAQ' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'cms', 'pages/view/id/2', '', 'top', 4, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'About Us' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'cms', 'pages/view/id/5', '', 'top', 5, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Contact Us' FROM `<DB_PREFIX>languages`;

INSERT INTO `<DB_PREFIX>search_categories` (`id`, `module_code`, `category_code`, `category_name`, `callback_class`, `callback_method`, `items_count`, `sort_order`, `is_active`) VALUES
(NULL, 'cms', 'pages', 'Pages', 'Modules\\Cms\\Models\\Pages', 'search', '20', (SELECT COUNT(sc.id) + 1 FROM `<DB_PREFIX>search_categories` sc), 1);

DROP TABLE IF EXISTS `<DB_PREFIX>cms_pages`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>cms_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comments_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NULL DEFAULT NULL,
  `modified_at` datetime NULL DEFAULT NULL,
  `finish_publishing_at` date NULL DEFAULT NULL,
  `is_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `publish_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1 - removed, 0 - draft, 1 - published',
  `show_in_search` tinyint(1) NOT NULL DEFAULT '1',
  `access_level` enum('public','registered') CHARACTER SET latin1 NOT NULL DEFAULT 'public',
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `publish_status` (`publish_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>cms_pages` (`id`, `comments_allowed`, `created_at`, `modified_at`, `finish_publishing_at`, `is_homepage`, `publish_status`, `show_in_search`, `access_level`, `sort_order`) VALUES
(1, 0, '2013-01-01 00:00:01', '2013-01-01 00:00:01', NULL, 1, 1, 1, 'public', 0),
(2, 0, '2018-09-03 00:00:01', '2018-09-03 00:00:01', NULL, 0, 1, 1, 'public', 1),
(3, 0, '2018-09-03 00:00:01', '2018-09-03 00:00:01', NULL, 0, 1, 1, 'public', 2),
(4, 0, '2018-09-03 00:00:01', '2018-09-03 00:00:01', NULL, 0, 1, 1, 'public', 3),
(5, 0, '2018-09-03 00:00:01', '2018-09-03 00:00:01', NULL, 0, 1, 1, 'public', 4);


DROP TABLE IF EXISTS `<DB_PREFIX>cms_page_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>cms_page_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) DEFAULT '0',
  `language_code` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `tag_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag_keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `tag_description` text COLLATE utf8_unicode_ci NOT NULL,
  `page_header` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_text` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `language_code` (`language_code`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 1, code, 'Our Site', 'php site', 'Our Site', 'WELCOME TO OUR WEBSITE!', '<h3>Hi there, Guest!</h3>\r\n<p>If you can read this message, this script has been successfully installed on your web hosting.</p>\r\n<p>This is an example of a HomePage, you could edit this to put information about yourself or your site do readers know where you are coming from. It’s a great way to get attention.</p>\r\n<p><strong>Dummy Text</strong></p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ac mattis elit. Nam convallis tristique lorem non ornare. Sed mi augue, luctus quis est sed, viverra aliquet metus. Pellentesque urna neque, elementum sit amet aliquam dapibus, tristique id metus. In pretium venenatis faucibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec varius lectus sed neque tincidunt tempor. In aliquam leo quis dui egestas, quis feugiat leo facilisis.</p>\r\n<p>Aliquam at lacus non lacus rhoncus bibendum id eget dolor. Donec placerat velit sed dictum tincidunt. Praesent odio lectus, eleifend nec viverra eu, sollicitudin vitae metus. Fusce quis tortor convallis ipsum aliquam dignissim. Nullam dignissim facilisis consectetur. Vestibulum sagittis augue nibh, non aliquet diam interdum tempor. Phasellus rhoncus commodo lectus id suscipit. Nullam non enim eu metus tempus lacinia ut condimentum tellus. Vestibulum eu odio eu mauris feugiat vulputate ut sed leo. Vivamus mollis non neque quis scelerisque.</p>' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 2, code, 'About As', 'php site', 'About As', 'About As', '<div class="tab-pane fade active in" id="tagline"><div class="v-shadow-wrap"><div class="v-tagline-box v-tagline-box-v1 v-box-shadow shadow-effect-2"><h2>uBidAuction</h2><p>is a powerful & fully-featured penny auction script that lets create the ultimate profitable online auction website. It allows to manage entire online auction operation: create new auctions within seconds, view members auctions and use the auction extension settings tool.</p></div></div></div>' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 3, code, 'How it works', 'php site', 'How it works', 'How it works', '<div class="v-process-steps four-columns mb20"><ul class="v-process"><li><div class="feature-box feature-box-st"><div class="feature-box-icon small icn-holder"><i class="fa fa-user-plus v-icon"></i></div><div class="feature-box-text"><h3>Sign up</h3><div class="feature-box-line"></div><div class="feature-box-text-inner"><a href="members/registration">Click here</a>. It`s free to register.</div></div></div></li><li><div class="feature-box feature-box-st"><div class="feature-box-icon small icn-holder"><i class="fa fa-shopping-cart  v-icon"></i></div><div class="feature-box-text"><h3>Buy Bids</h3><div class="feature-box-line"></div><div class="feature-box-text-inner">After you register, buy bids for your account so you can bid.</div></div></div></li><li><div class="feature-box feature-box-st"><div class="feature-box-icon small icn-holder"><i class="fa fa-usd v-icon"></i></div><div class="feature-box-text"><h3>Bid</h3><div class="feature-box-line"></div><div class="feature-box-text-inner">Choose your product, carefully choose a bidding strategy, place bids and beat your competitors.</div></div></div></li><li><div class="feature-box feature-box-st"><div class="feature-box-icon small icn-holder"><i class="fa fa-trophy v-icon"></i></div><div class="feature-box-text"><h3>Win</h3><div class="feature-box-line"></div><div class="feature-box-text-inner">If you are the final bidder, you win the auction and can buy the product for up to 90% off.</div></div></div></li></ul></div><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle collapsed" data-toggle="collapse" href="#how_bidding_works" aria-expanded="false"><i class="fa fa-briefcase"></i>How bidding works</a></h4></div><div id="how_bidding_works" class="accordion-body collapse" aria-expanded="false" style="height: 0px;"><div class="panel-body"><ul class="v-list"><li><i class="fa fa-check"></i><span>Place bids by clicking the auction then click "BID NOW" button under the bid amount.</span></li><li><i class="fa fa-check"></i><span>The last bidder is the winner and buys the product for the price.</span></li><li><i class="fa fa-check"></i><span>Time is counted down toward auction closing time. If a new bid is placed in the last 30 seconds, the time is reset for another 30 seconds.</span></li><li><i class="fa fa-check"></i><span>The auction finishes when there are no more bids just like a real live auction.</span></li></ul></div></div></div><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle collapsed" data-toggle="collapse" href="#how_to_use_autobid" aria-expanded="false"><i class="fa fa-laptop"></i>How to use Autobid</a></h4></div><div id="how_to_use_autobid" class="accordion-body collapse" aria-expanded="false" style="height: 0px;"><div class="panel-body"><ul class="v-list"><li><i class="fa fa-check"></i><span>If you intend to bid on auctions with only a few seconds remaining, we suggest you bid manually first, then set up Autobids, as you may run out of time.</span></li><li><i class="fa fa-check"></i><span>Automatically set the number of bids you wish to place and the maximum amount you want to pay for an item. Then let Autobids do the work for you.</span></li><li><i class="fa fa-check"></i><span>Log in to your account.</span></li><li><i class="fa fa-check"></i><span>Select the auction you want to participate in.</span></li><li><i class="fa fa-check"></i><span>Click the green Autobid link below the Auction ID number.</span></li><li><i class="fa fa-check"></i><span>Select +ADD NEW and pick the auction from the drop down menu.</span></li><li><i class="fa fa-check"></i><span>Set the MAX NUMBER OF BIDS and MAX AMOUNT you wish to pay.</span></li><li><i class="fa fa-check"></i><span>Click the CREATE button and you are done.</span></li><li><i class="fa fa-check"></i><span>Autobid assists with responsible bidding and eliminates the need to monitor auctions constantly.</span></li></ul></div></div></div>' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 4, code, 'Help', 'php site', 'Help', 'FAQ', '{module:faq}' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 5, code, 'Contact Us', 'php site', 'Contact US', 'Contact US', '{module:webforms}' FROM `<DB_PREFIX>languages`;


DROP TABLE IF EXISTS `<DB_PREFIX>cms_page_comments`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>cms_page_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cms_page_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `user_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(80) CHARACTER SET latin1 NOT NULL,
  `comment_text` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - awaiting, 1 - approved, 2 - denied',
  `created_at` datetime NULL DEFAULT NULL,
  `changed_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_page_id` (`cms_page_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

