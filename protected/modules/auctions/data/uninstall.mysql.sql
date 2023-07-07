DELETE FROM `<DB_PREFIX>modules` WHERE `code` = 'auctions';
DELETE FROM `<DB_PREFIX>module_settings` WHERE `module_code` = 'auctions';

DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'auctions' AND `category` = 'auctions' AND `code` = 'add');
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'auctions' AND `category` = 'auctions' AND `code` = 'edit');
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'auctions' AND `category` = 'auctions' AND `code` = 'delete');

DELETE FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'auctions';

DELETE FROM `<DB_PREFIX>backend_menu_translations` WHERE `menu_id` IN (SELECT id FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'auctions');
DELETE FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'auctions';

DELETE FROM `<DB_PREFIX>frontend_menu_translations` WHERE `menu_id` IN (SELECT id FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'auctions');
DELETE FROM `<DB_PREFIX>frontend_menus` WHERE `module_code` = 'auctions';

DELETE FROM `<DB_PREFIX>email_template_translations` WHERE `template_code` IN (SELECT code FROM `<DB_PREFIX>email_templates` WHERE `module_code` = 'auctions');
DELETE FROM `<DB_PREFIX>email_templates` WHERE `module_code` = 'auctions';

DELETE FROM `<DB_PREFIX>accounts` WHERE `role` = 'member';

DROP TABLE IF EXISTS `<DB_PREFIX>auction_types`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_type_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>auctions`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_categories`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_category_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_members`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_orders`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_packages`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_package_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_shipments`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_taxes`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_tax_countries`;
DROP TABLE IF EXISTS `<DB_PREFIX>auction_bids_history`;
