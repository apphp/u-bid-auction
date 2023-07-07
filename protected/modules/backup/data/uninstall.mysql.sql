
DELETE FROM `<DB_PREFIX>modules` WHERE `code` = 'backup';
DELETE FROM `<DB_PREFIX>module_settings` WHERE `module_code` = 'backup';

DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'backup');
DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'backup';

DELETE FROM `<DB_PREFIX>backend_menu_translations` WHERE `menu_id` IN (SELECT id FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'backup');
DELETE FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'backup';


