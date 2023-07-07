-- modules --
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'auctions', 'Auctions', 'Auctions', 'Auction management, online auction system.', '0.0.1', 'icon.png', 1, 1, 1, 1, 1, '<CURRENT_DATETIME>', NULL, 1, (SELECT COUNT(m.id) + 10 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 1));


-- module settings --
INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'auctions', '', 'moduleblock', 'drawDashboardBlock', 'Dashboard Block', 'Draws dashboard link block', 'label', '', '', '', '', 0),
(NULL, 'auctions', 'Images', 'image_max_size', '2Mb', 'Maximum Image Size', 'Defines a maximum size for image in megabytes (Mb)', 'enum', '500Kb,1Mb,1.5Mb,2Mb,2.5Mb,3Mb,4Mb', '', '', '', 0),
(NULL, 'auctions', 'Images', 'allow_multi_image_upload', '1', 'Multi Image Upload', 'Specifies whether to allow multiple images upload for auctions', 'bool', '', '', '', '', 0),
(NULL, 'auctions', 'Images', 'auction_maximum_images_upload', '20', 'Maximum Images to Upload', 'Defines a maximum number of files for auction multi-images uploading', 'range', '1-50', '', '', '', 0),
(NULL, 'auctions', 'Member Registration', 'member_allow_registration', '1', 'Allow members to Register', 'Specifies whether to allow new members to register', 'bool', '', 0, '', '', 0),
(NULL, 'auctions', 'Member Registration', 'member_approval_type', 'automatic', 'Approval Type', 'Specifies which type of approval is required for member registration', 'enum', 'by_admin,by_email,automatic', 0, '', '', 0),
(NULL, 'auctions', 'Member Registration', 'member_new_registration_alert', '1', 'New Registration, Admin Alert', 'Specifies whether to alert admin on new member registration', 'bool', '', 0, '', '', 0),
(NULL, 'auctions', 'Member Registration', 'member_verification_allow', '1', 'Verification captcha', 'Specifies whether to allow verification captcha on member registration page', 'bool', '', '', '', '', 0),
(NULL, 'auctions', 'Member Registration', 'modulelink', 'members/registration', 'Member Registration Link', 'This link leads to the page where member can register to the site', 'label', '', 0, '', '', 0),
(NULL, 'auctions', 'Member Settings', 'member_allow_remember_me', '1', 'Allow Remember Me', 'Specifies whether to allow Remember Me feature by members', 'bool', '', '', '', '', 0),
(NULL, 'auctions', 'Member Settings', 'member_removal_type', 'logical', 'Remove Account Type', 'Specifies the type of member account removal: logical, physical', 'enum', 'logical,physical','', '', '', 0),
(NULL, 'auctions', 'Member Settings', 'change_member_password', '1', 'Admin Changes Member Password', 'Specifies whether to allow changing member password by Admin', 'bool', '', '', '', '', 0),
(NULL, 'auctions', 'Member Settings', 'member_allow_restore_password', '1', 'Allow Restore Password', 'Specifies whether to allow members to restore their passwords', 'bool', '', '', '', '', 0),
(NULL, 'auctions', 'Member Settings', 'modulelink', 'members/restorePassword', 'Restore Password Link', 'This link leads to the page where member may restore forgotten password', 'label', '', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'auction_link_format', 'auctions/ID/Name', 'Auction Profile Link Format', 'Defines a SEO format for profile links that will be used on the site', 'enum', 'auctions/view/id/ID,auctions/view/id/ID/Name,auctions/view/ID,auctions/view/ID/Name,auctions/ID,auctions/ID/Name', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'auction_categories_format', 'auctions/categories/ID/Name', 'Auction Categories Link Format', 'Defines a SEO format for categories links that will be used on the site', 'enum', 'auctions/categories/id/ID,auctions/categories/id/ID/Name,auctions/categories/ID,auctions/categories/ID/Name', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'auctions_per_page', '12', 'Auctions Per Page', 'Defines how many auctions will be shown per page', 'enum', '8,12,16,20,24,28,32,36,40', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'new_auctions_count_days', '5', 'Days to show new auctions', 'Specifies how many days to show new auctions in "New Arrivals"', 'range', '1-15', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'closed_auctions_count_days', '5', 'Days to show closed auctions', 'Specifies how many days to show closed auctions in "Closed Auctions"', 'range', '1-15', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'closing_soon_count_days', '5', 'Days to show in "Closing soon"', 'Specifies the number of days before the auction closes, when auctions appear in the "Closing soon"', 'range', '1-15', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'send_email_admin_auction_closed', '1', 'Send Email To Admin', 'Specifies whether to send email to admin when closing an auction.', 'bool', '', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'send_email_member_auction_closed', '1', 'Send Email To Member', 'Specifies whether to send email to member when closing an auction.', 'bool', '', '', '', '', 0),
(NULL, 'auctions', 'Auctions Settings', 'review_moderation', '1', 'Review Moderation', 'Specifies moderating the review after publication', 'bool', '', '', '', '', 0);

-- auction privileges --
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'auction', 'add', 'Add Auction', 'Add auction on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'auction', 'edit', 'Edit Auction', 'Edit auction on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'auction', 'delete', 'Delete Auction', 'Delete auction from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

-- category privileges --
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'category', 'add', 'Add Category', 'Add category on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'category', 'edit', 'Edit Category', 'Edit category on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'category', 'delete', 'Delete Category', 'Delete category from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

-- privileges auction type --
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'auction_type', 'add', 'Add Auction Type', 'Add auction type on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'auction_type', 'edit', 'Edit Auction Type', 'Edit auction type on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'auction_type', 'delete', 'Delete Auction Type', 'Delete auction type from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

-- members privileges --
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'member', 'add', 'Add Member', 'Add memberon the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'member', 'edit', 'Edit Member', 'Edit member on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'member', 'delete', 'Delete Member', 'Delete member from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

-- packages privileges --
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'package', 'add', 'Add Package', 'Add package the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'package', 'edit', 'Edit Package', 'Edit package on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'package', 'delete', 'Delete Package', 'Delete package from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

-- taxes privileges --
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'tax', 'add', 'Add Tax', 'Add tax on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'tax', 'edit', 'Edit Tax', 'Edit tax on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'tax', 'delete', 'Delete Tax', 'Delete tax from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

-- orders privileges --
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'order', 'edit', 'Edit Order', 'Edit order on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'auctions', 'order', 'delete', 'Delete Order', 'Delete order from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);


-- backend menus --
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, 0, '', 'auctions', 'auctions.png', 0, 1, 7);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Auctions' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), '<SITE_BO_URL>modules/settings/code/auctions', 'auctions', '', 0, 1, 0);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Settings' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'categories/manage', 'auctions', '', 0, 1, 1);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Categories' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'auctionTypes/manage', 'auctions', '', 0, 1, 1);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Auction Types' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'auctions/manage', 'auctions', '', 0, 1, 3);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Auctions' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'members/manage', 'auctions', '', 0, 1, 4);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Members' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'packages/manage', 'auctions', '', 0, 1, 5);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Packages' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'taxes/manage', 'auctions', '', 0, 1, 6);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Taxes' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'orders/manage', 'auctions', '', 0, 1, 7);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Orders' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'auctions' AND bm.parent_id = 0), 'statistics/manage', 'auctions', '', 0, 1, 8);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Statistics' FROM `<DB_PREFIX>languages`;


-- frontend menus --
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'moduleblock', 'auctions', 'drawDashboardBlock', '', 'right', 0, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Dashboard' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'auctions', 'packages/packages', '', 'top', 1, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Bid Packages' FROM `<DB_PREFIX>languages`;

  INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'moduleblock', 'auctions', 'drawFilteringBlock', '', 'left', 2, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Filtering' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'moduleblock', 'auctions', 'drawRecentlyClosedAuctionsBlock', '', 'left', 3, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Recently Closed Auctions' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'moduleblock', 'auctions', 'drawLastWinnersBlock', '', 'left', 4, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Last Winners' FROM `<DB_PREFIX>languages`;

INSERT INTO `<DB_PREFIX>email_templates` (`id`, `code`, `module_code`, `is_system`) VALUES
(NULL, 'member_new_account_created_by_admin', 'auctions', 1),
(NULL, 'member_account_approved_by_admin', 'auctions', 1),
(NULL, 'member_password_changed_by_admin', 'auctions', 1),
(NULL, 'member_account_created_notify_admin', 'auctions', 1),
(NULL, 'member_account_created_admin_approval', 'auctions', 1),
(NULL, 'member_account_created_email_confirmation', 'auctions', 1),
(NULL, 'member_account_created_auto_approval', 'auctions', 1),
(NULL, 'member_password_forgotten', 'auctions', 1),
(NULL, 'member_account_removed_by_member', 'auctions', 1),
(NULL, 'member_account_removed_by_admin', 'auctions', 1),
(NULL, 'member_success_order', 'auctions', 1),
(NULL, 'member_success_order_for_admin', 'auctions', 1),
(NULL, 'member_paid_order', 'auctions', 1),
(NULL, 'admin_auction_closed', 'auctions', 1),
(NULL, 'admin_auction_suspended', 'auctions', 1),
(NULL, 'member_auction_closed', 'auctions', 1),
(NULL, 'admin_auction_buy_now', 'auctions', 1),
(NULL, 'member_auction_buy_now', 'auctions', 1),
(NULL, 'admin_auction_paid', 'auctions', 1),
(NULL, 'member_auction_paid', 'auctions', 1),
(NULL, 'admin_auction_shipped', 'auctions', 1),
(NULL, 'member_auction_shipped', 'auctions', 1);

-- Member --
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_new_account_created_by_admin', code, 'New member account created (by admin)', 'Your account has been created by administrator', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nThe {WEB_SITE} Admin has invited you to contribute to our site.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nPlease follow the link below to log into your account: <a href={SITE_URL}members/login>Login</a>.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_account_approved_by_admin', code, 'New member account approved (by admin)', 'Your account has been approved', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nCongratulations! This e-mail is to confirm that your registration at {WEB_SITE} has been approved.\r\n\r\nYou may now <a href={SITE_URL}members/login>log into</a> your account.\r\n\r\nThank you for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_password_changed_by_admin', code, 'Password for the member account changed (by admin)', 'Your password has been changed by admin', 'Hello <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nYour password has been changed by administrator of the site:\r\n{WEB_SITE}\r\n\r\nBelow your new login info:\r\n-\r\nUsername: {USERNAME} \r\nPassword: {PASSWORD}\r\n\r\n-\r\nBest Regards,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_account_created_notify_admin', code, 'New member account created (notify admin)', 'New member account has been created', "Hello Admin!\r\n\r\nA new member has been registered on your site.\r\n\r\nThis email contains a member account details:\r\n\r\nName: {FIRST_NAME} {LAST_NAME}\r\nEmail: {MEMBER_EMAIL}\r\nUsername: {USERNAME}\r\n\r\nP.S. Please check if it doesn't require your approval for activation." FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_account_created_admin_approval', code, 'New member account created (admin approval)', 'Your account has been created (admin approval required)', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nAfter your registration is approved by administrator, you could log into your account with a following link:\r\n<a href={SITE_URL}members/login>Login Here</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_account_created_email_confirmation', code, 'New member account created (email confirmation)', 'Your account has been created (email confirmation required)', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nIn order to become authorized member, you will need to confirm your registration. You may follow the link below to access the confirmation page:\r\n<a href="{SITE_URL}members/confirmRegistration/code/{REGISTRATION_CODE}">Confirm Registration</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_account_created_auto_approval', code, 'New member account created (auto approval)', 'Your account has been created and activated', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nYou may follow the link below to log into your account:\r\n<a href={SITE_URL}members/login>Login Here</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_password_forgotten', code, 'Restore forgotten password (by member)', 'Forgotten Password', 'Hello!\r\n\r\nYou or someone else asked to restore your login info on our site:\r\n<a href={SITE_URL}members/login>{WEB_SITE}</a>\r\n\r\nYour new login:\r\n---------------\r\nUsername: {USERNAME}\r\nPassword: {PASSWORD}\r\n\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_account_removed_by_member', code, 'Account removed (by patient)', 'Your account has been removed', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>!\r\n\r\nYour account has been successfully removed according to your request.\r\n\r\n-\r\nBest Regards,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_account_removed_by_admin', code, 'Account removed (by admin)', 'Your account has been removed by administrator', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>!\r\n\r\nYour account has been successfully removed by administrator.\r\n\r\n-\r\nBest Regards,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_success_order', code, 'Success Order', 'Your order has been placed in our system!', 'Dear {FIRST_NAME} {LAST_NAME}!\r\n\r\nThank you for reservation request!\r\n\r\nYour order <b>{ORDER_NUMBER}</b> has been placed in our system and will be processed shortly.\r\nStatus: {STATUS}\r\n\r\nDate Created: {DATE_CREATED}\r\nPayment Date: {DATE_PAYMENT}\r\nPayment Type: {PAYMENT_TYPE}\r\nCurrency: {CURRENCY}\r\nPrice: {PRICE}\r\n\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_paid_order', code, 'Package Paid', 'Payment of the package has been successfully confirmed.', 'Dear {FIRST_NAME} {LAST_NAME}!\r\n\r\nThank you for your payment!!\r\n\r\nYour order <b>{ORDER_NUMBER}</b> has been paid and approved.\r\n\r\nPackage: {PACKAGE}\r\nOrder Status: {STATUS}\r\nDate Created: {DATE_CREATED}\r\nDate Status Changed: {STATUS_CHANGED}\r\n\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_auction_closed', code, 'Auction Closed(Member copy)', 'You won the auction!', 'Dear <b>{MEMBER_NAME}!</b>\r\n\r\nThis email confirms that you are the winner of the auction {AUCTION_NAME}.\r\n\r\nYou need to pay the full cost of the auction within 48 hours after receiving this email.\r\n\r\nYou can pay for the auction in your dashboard.\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_auction_buy_now', code, 'Auction Item Purchased(Member copy)', 'You have purchased an auction item', 'Dear <b>{MEMBER_NAME}!</b>\r\n\r\nThis email confirms that you have purchased an auction item {AUCTION_NAME}.\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_auction_paid', code, 'Auction Item Paid(Member copy)', 'You paid for the auction item', 'Dear <b>{MEMBER_NAME}!</b>\r\n\r\nThis email confirms that you have paid an auction  item {AUCTION_NAME}.\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_auction_shipped', code, 'Auction Item Shipped(Member copy)', 'Your auction item has been shipped', 'Dear <b>{MEMBER_NAME}!</b>\r\n\r\nThis email confirms that the administration has sent your won auction item {AUCTION_NAME}.\r\n\r\nShipment Details:\r\n\r\n{SHIPMENT_DETAILS}\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;

-- administrator --
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'member_success_order_for_admin', code, 'Success Order (admin copy)', 'The order has been placed in system!', 'User <b>{FIRST_NAME} {LAST_NAME} ({USERNAME})</b>!\r\n\r\nThe order <b>{ORDER_NUMBER}</b> has been placed in system.\r\nStatus: {STATUS}\r\n\r\nDate Created: {DATE_CREATED}\r\nPayment Date: {DATE_PAYMENT}\r\nPayment Type: {PAYMENT_TYPE}\r\nCurrency: {CURRENCY}\r\nPrice: {PRICE}\r\n\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'admin_auction_closed', code, 'Auction Closed(Admin copy)', 'Member won in the auction!', 'Hello Admin!</b>\r\n\r\n This email confirms that the {MEMBER_NAME} is the winner of the auction {AUCTION_NAME}. \r\n\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'admin_auction_suspended', code, 'Auction Suspended', 'The auction ended without a winner!', 'Hello Admin!</b>\r\n\r\n This email confirms that the auction {AUCTION_NAME} has been completed without a winner.\r\n\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'admin_auction_buy_now', code, 'Auction Item Purchased(Admin copy)', 'Member have purchased an auction item', 'Hello Admin!</b>\r\n\r\n This email confirms that the auction item {AUCTION_NAME} was purchased by member.\r\n\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'admin_auction_paid', code, 'Auction Item Paid(Admin copy)', 'Member paid for the auction item', 'Hello Admin!</b>\r\n\r\n This email confirms that the auction item {AUCTION_NAME} was paid by member.\r\n\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'admin_auction_shipped', code, 'Auction Item Shipped(Admin copy)', 'Auction item shipped to member', 'Hello Admin!</b>\r\n\r\nThis email confirms that an auction item {AUCTION_NAME} has been shipped to a member.\r\n\r\nShipment Details:\r\n\r\n{SHIPMENT_DETAILS}' FROM `<DB_PREFIX>languages`;

-- site info --
UPDATE `<DB_PREFIX>site_info` SET `header`='uBidAuction', `slogan`='Welcome to Auctions!', `footer`='&copy; 2018 Powered by <a class="footer_link" target="_blank" rel="noopener noreferrer" href="https://www.apphp.com">ApPHP</a>', `meta_title`='Auctions', `meta_description`='Auctions', `meta_keywords`='auctions, auction framework, auction content management framework, auction cms, auction cms', `site_address`='Auction<br>1, North Avenue<br>New York, NY';

-- payment providers --
UPDATE `<DB_PREFIX>payment_providers` SET `is_default`='0' WHERE `is_default`='1';
UPDATE `<DB_PREFIX>payment_providers` SET `is_default`='1' WHERE `code` = 'paypal_standard';
UPDATE `<DB_PREFIX>payment_providers` SET `is_active`='0' WHERE `code` <> 'paypal_standard';


-- create here module tables --
DROP TABLE IF EXISTS `<DB_PREFIX>auction_types`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_types` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `class_name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `file_name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `auction_number` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `sort_order` smallint(3) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

INSERT INTO `<DB_PREFIX>auction_types` (`id`, `class_name`, `file_name`, `sort_order`, `is_active`, `is_default`) VALUES
(1, 'ClassicAuction', 'ClassicAuction.php', 0, 1, 1),
(2, 'PennyAuction', 'PennyAuction.php', 1, 1, 0);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_type_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_type_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auction_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auction_type_id` (`auction_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

INSERT INTO `<DB_PREFIX>auction_type_translations` (`id`, `auction_type_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, 'Classic', 'Participants bid openly against one another, with each subsequent bid required to be higher than the previous bid. An auctioneer may announce prices, bidders may call out their bids themselves (or have a proxy call out a bid on their behalf), or bids may be submitted electronically with the highest current bid publicly displayed. The auction ends when no participant is willing to bid further, at which point the highest bidder pays their bid.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_type_translations` (`id`, `auction_type_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, 'Penny', 'Each participant must pay a fixed price to place each bid, typically one penny (hence the name) higher than the current bid. When an auction''s time expires, the highest bidder wins the item and must pay a final bid price. Unlike in a conventional auction, the final price is typically much lower than the value of the item, but all bidders (not just the winner) will have paid for each bid placed; the winner will buy the item at a very low price (plus price of rights-to-bid used), all the losers will have paid, and the seller will typically receive significantly more than the value of the item.' FROM <DB_PREFIX>languages;

DROP TABLE IF EXISTS `<DB_PREFIX>auctions`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auction_number` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `auction_type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `category_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_from` datetime NULL DEFAULT NULL,
  `date_to` datetime NULL DEFAULT NULL,
  `start_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `buy_now_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `size_bid` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `step_size` int(11) unsigned NOT NULL DEFAULT 0,
  `current_bid` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `created_at` datetime NULL DEFAULT NULL,
  `winner_member_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0 - inactive, 1 - active, 2 - suspended, 3 - won, 4 - closed',
  `status_changed` datetime NULL DEFAULT NULL,
  `paid_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not approved, 1 - paid',
  `paid_status_changed` datetime NULL DEFAULT NULL,
  `shipping_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - pending, 1 - shipped, 2 - received, 3 - denied',
  `won_date` datetime NULL DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `auction_type_id` (`auction_type_id`),
  KEY `category_id` (`category_id`),
  KEY `winner_member_id` (`winner_member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21;


INSERT INTO `<DB_PREFIX>auctions` (`id`, `auction_number`, `auction_type_id`, `category_id`, `date_from`, `date_to`, `start_price`, `buy_now_price`, `size_bid`, `step_size`, `current_bid`, `created_at`, `winner_member_id`, `status`, `status_changed`, `paid_status`, `paid_status_changed`, `won_date`, `shipping_status`, `hits`) VALUES
(1, 'TEST-1', 1, 1, NOW() - INTERVAL '4 5:4:5' DAY_SECOND, NOW() + INTERVAL '11 5:4:5' DAY_SECOND, '100.00', '500.00', '25.00', '0', '150.00', NOW(), 0, 1, NOW(), 0, NOW(), NULL, 0, 101),
(2, 'TEST-2', 1, 1, NOW() - INTERVAL '2 1:2:1' DAY_SECOND, NOW() + INTERVAL '13 1:2:1' DAY_SECOND, '250.00', '400.00', '25.00', '0', '300.00', NOW() - INTERVAL '2 1:2:1' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 93),
(3, 'TEST-3', 1, 2, NOW() - INTERVAL '1 7:1:7' DAY_SECOND, NOW() + INTERVAL '14 7:1:7' DAY_SECOND, '9000.00', '16000.00', '100.00', '0', '9000.00', NOW() - INTERVAL '1 7:1:7' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 30),
(4, 'TEST-4', 2, 2, NOW() - INTERVAL '1 8:3:8' DAY_SECOND, NOW() + INTERVAL '14 8:3:8' DAY_SECOND, '11000.00', '18000.00', '0.01', '3', '0', NOW() - INTERVAL '1 8:3:8' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 11),
(5, 'TEST-5', 2, 3, NOW() - INTERVAL '1 9:8:3' DAY_SECOND, NOW() + INTERVAL '14 9:1:7' DAY_SECOND, '150.00', '1500.00', '0.01', '5', '0', NOW() - INTERVAL '1 9:8:3' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 12),
(6, 'TEST-6', 2, 3, NOW() - INTERVAL '1 3:3:8' DAY_SECOND, NOW() + INTERVAL '14 3:3:8' DAY_SECOND, '800.00', '2400.00', '0.01', '1', '0', NOW() - INTERVAL '1 3:3:8' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 13),
(7, 'TEST-7', 1, 4, NOW() - INTERVAL '14 1:14:1' DAY_SECOND, NOW() + INTERVAL '1 1:14:1' DAY_SECOND, '700.00', '1500.00', '50.00', '0', '700.00', NOW() - INTERVAL '14 1:14:1' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 14),
(8, 'TEST-8', 1, 4, NOW() - INTERVAL '13 2:13:2' DAY_SECOND, NOW() + INTERVAL '2 2:13:2' DAY_SECOND, '550.00', '1150.00', '50.00', '0', '550.00', NOW() - INTERVAL '13 2:13:2' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 15),
(9, 'TEST-9', 1, 5, NOW() - INTERVAL '13 8:13:8' DAY_SECOND, NOW() + INTERVAL '2 8:13:8' DAY_SECOND, '300.00', '850.00', '25.00', '0', '300.00', NOW() - INTERVAL '13 8:13:8' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 16),
(10, 'TEST-10', 1, 5, NOW() - INTERVAL '12 7:12:7' DAY_SECOND, NOW() + INTERVAL '3 7:12:7' DAY_SECOND, '350.00', '950.00', '25.00', '0', '350.00', NOW() - INTERVAL '12 7:12:7' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 15),
(11, 'TEST-11', 1, 6, NOW() - INTERVAL '11 3:11:3' DAY_SECOND, NOW() + INTERVAL '4 3:11:3' DAY_SECOND, '15000.00', '2300.00', '150.00', '0', '15000.00', NOW() - INTERVAL '11 3:11:3' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 25),
(12, 'TEST-12', 1, 6, NOW() - INTERVAL '10 1:10:1' DAY_SECOND, NOW() + INTERVAL '5 1:10:1' DAY_SECOND, '25000.00', '40000.00', '250.00', '0', '25000.00', NOW() - INTERVAL '10 1:10:1' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 35),
(13, 'TEST-13', 1, 7, NOW() - INTERVAL '10 1:10:1' DAY_SECOND, NOW() + INTERVAL '5 1:10:1' DAY_SECOND, '3500.00', '7000.00', '100.00', '0', '3500.00', NOW() - INTERVAL '10 1:10:1' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 15),
(14, 'TEST-14', 1, 7, NOW() - INTERVAL '13 5:13:5' DAY_SECOND, NOW() + INTERVAL '2 5:13:5' DAY_SECOND, '5000.00', '9000.00', '150.00', '0', '5000.00', NOW() - INTERVAL '13 5:13:5' DAY_SECOND, 0, 1, NOW(), 0, NOW(), NULL, 0, 4),
(15, 'TEST-15', 1, 1, NOW() - INTERVAL 16 DAY, NOW() - INTERVAL 1 DAY, '300.00', '800.00', '25.00', '0', '300.00', NOW() - INTERVAL 16 DAY, 1, 2, NOW(), 0, NOW(), NULL, 0, 15),
(16, 'TEST-16', 1, 2, NOW() - INTERVAL 16 DAY, NOW() - INTERVAL 2 DAY, '4500.00', '6000.00', '100.00', '0', '4500.00', NOW() - INTERVAL 16 DAY, 2, 2, NOW(), 0, NOW(), NULL, 0, 50),
(17, 'TEST-17', 1, 3, NOW() - INTERVAL 16 DAY, NOW() - INTERVAL 3 DAY, '2000.00', '5000.00', '75.00', '0', '2000.00', NOW() - INTERVAL 16 DAY, 2, 2, NOW(), 0, NOW(), NULL, 0, 60),
(18, 'TEST-18', 1, 4, NOW() - INTERVAL 16 DAY, NOW() - INTERVAL 4 DAY, '550.00', '950.00', '50.00', '0', '550.00', NOW() - INTERVAL 16 DAY, 1, 2, NOW(), 0, NOW(), NULL, 0, 90),
(19, 'TEST-19', 1, 5, NOW() - INTERVAL 16 DAY, NOW() - INTERVAL 5 DAY, '750.00', '1200.00', '75.00', '0', '750.00', NOW() - INTERVAL 16 DAY, 2, 2, NOW(), 0, NOW(), NULL, 0, 40),
(20, 'TEST-20', 1, 6, NOW() - INTERVAL 16 DAY, NOW() - INTERVAL 6 DAY, '400.00', '1300.00', '50.00', '0', '400.00', NOW() - INTERVAL 16 DAY, 1, 3, NOW(), 0, NOW(), NULL, 0, 30);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auction_id` int(11) unsigned NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `short_description` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auction_id` (`auction_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21;

INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 1, code, 'Apple iPhone 7', '<ul><li><strong>Capacity:</strong> 128GB</li><li><strong>Display:</strong> Retina HD display, 4.7-inch (diagonal) widescreen LCD, Multi-Touch display with IPS technology,<br/>1334-by-750-pixel resolution at 326 ppi,<br/>1400:1 contrast ratio (typical)</li><li><strong>Chip:</strong> A10 Fusion chip</li><li><strong>Camera:</strong> 12MP camera</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 2, code, 'Apple iPad 2', '<ul><li><strong>Display</strong>9.70-inch.</li><li><strong>Processor</strong>1GHz.</li><li><strong>Front Camera</strong>0.3-megapixel.</li><li><strong>Resolution</strong>768x1024 pixels.</li><li><strong>RAM</strong>512MB.</li><li>OSiOS 4.</li><li><strong>Storage</strong>16GB.</li><li>Rear <strong>Camera</strong>0.7-megapixel.</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 3, code, 'Ford Mustang', '<ul><li><strong>Year:</strong> 2015, </li><li><strong>Manufacturer:</strong> Ford, </li><li><strong>Model:</strong> Mustang, </li><li><strong>Body Type:</strong> Coupe, </li><li><strong>Doors:</strong> 3, </li><li><strong>Colour:</strong> Red, </li><li><strong>Mileage:</strong> 80000, </li><li><strong>Engine Size:</strong> 4700, </li><li><strong>Transmission:</strong> Automatic, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 4, code, 'Ford Focus', '<ul><li><strong>Year:</strong> 2012, </li><li><strong>Manufacturer:</strong> Ford, </li><li><strong>Model:</strong> Focus, </li><li><strong>Body Type:</strong> Hatchback, </li><li><strong>Doors:</strong> 5, </li><li><strong>Colour:</strong> White, </li><li><strong>Mileage:</strong> 25000, </li><li><strong>Engine Size:</strong> 1600, </li><li><strong>Transmission:</strong> Automatic, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 5, code, 'Tissot Watch', '<ul><li>Brand: Tissot</li><li>Style: Sport</li><li>Face Color: Black</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 6, code, 'Acoustic Guitar', '<ul><li>Model Year: 2005/6</li><li>Model: 000M</li><li>String Configuration: 6 String</li><li>Body Material: Solid spruce top / Mahogany back and sides</li><li>Body Type: Auditorium</li><li>With Bag: Hardcase</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 7, code, 'Samsung Galaxy S9', '<ul><li><strong>Capacity:</strong> 128GB</li><li><strong>Display:</strong> Retina HD display, 4.7-inch (diagonal) widescreen LCD, Multi-Touch display with IPS technology,<br/>1334-by-750-pixel resolution at 326 ppi,<br/>1400:1 contrast ratio (typical)</li><li><strong>Chip:</strong> A10 Fusion chip</li><li><strong>Camera:</strong> 12MP camera</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 8, code, 'Apple iPhone 8', '<ul><li><strong>Capacity:</strong> 128GB</li><li><strong>Display:</strong> Retina HD display, 4.7-inch (diagonal) widescreen LCD, Multi-Touch display with IPS technology,<br/>1334-by-750-pixel resolution at 326 ppi,<br/>1400:1 contrast ratio (typical)</li><li><strong>Chip:</strong> A10 Fusion chip</li><li><strong>Camera:</strong> 12MP camera</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 9, code, 'Samsung Galaxy Tab A', '<ul><li><strong>Display</strong>9.70-inch.</li><li><strong>Processor</strong>1GHz.</li><li><strong>Front Camera</strong>0.3-megapixel.</li><li><strong>Resolution</strong>768x1024 pixels.</li><li><strong>RAM</strong>512MB.</li><li>OSiOS 4.</li><li><strong>Storage</strong>16GB.</li><li>Rear <strong>Camera</strong>0.7-megapixel.</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 10, code, 'Apple iPad Pro', '<ul><li><strong>Display</strong>9.70-inch.</li><li><strong>Processor</strong>1GHz.</li><li><strong>Front Camera</strong>0.3-megapixel.</li><li><strong>Resolution</strong>768x1024 pixels.</li><li><strong>RAM</strong>512MB.</li><li>OSiOS 4.</li><li><strong>Storage</strong>16GB.</li><li>Rear <strong>Camera</strong>0.7-megapixel.</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 11, code, 'Dodge Ram', '<ul><li><strong>Year:</strong> 2010, </li><li><strong>Manufacturer:</strong> Dodge, </li><li><strong>Model:</strong> Ram, </li><li><strong>Body Type:</strong> Pickup, </li><li><strong>Doors:</strong> 2, </li><li><strong>Colour:</strong> White, </li><li><strong>Mileage:</strong> 95000, </li><li><strong>Engine Size:</strong> 5600, </li><li><strong>Transmission:</strong> Automatic, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 12, code, 'Chevrolet Corvette', '<ul><li><strong>Year:</strong> 2009, </li><li><strong>Manufacturer:</strong> Chevrolet, </li><li><strong>Model:</strong> Corvette, </li><li><strong>Body Type:</strong> Coupe, </li><li><strong>Doors:</strong> 2, </li><li><strong>Colour:</strong> Red, </li><li><strong>Mileage:</strong> 100000, </li><li><strong>Engine Size:</strong> 5000, </li><li><strong>Transmission:</strong> Automatic, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 13, code, 'Suzuki Bandit', '<ul><li><strong>Year:</strong> 2003, </li><li><strong>Manufacturer:</strong> Suzuki, </li><li><strong>Model:</strong> Bandit, </li><li><strong>Colour:</strong> Yellow, </li><li><strong>Mileage:</strong> 40000, </li><li><strong>Engine Size:</strong> 400, </li><li><strong>Transmission:</strong> Mechanical, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 14, code, 'Honda CBR1000', '<ul><li><strong>Year:</strong> 2007, </li><li><strong>Manufacturer:</strong> Honda, </li><li><strong>Model:</strong> CBR1000, </li><li><strong>Colour:</strong> Red, </li><li><strong>Mileage:</strong> 25000, </li><li><strong>Engine Size:</strong> 1000, </li><li><strong>Transmission:</strong> Mechanical, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 15, code, 'Samsung Galaxy S7', '<ul><li><strong>Display</strong>9.70-inch.</li><li><strong>Processor</strong>1GHz.</li><li><strong>Front Camera</strong>0.3-megapixel.</li><li><strong>Resolution</strong>768x1024 pixels.</li><li><strong>RAM</strong>512MB.</li><li>OSiOS 4.</li><li><strong>Storage</strong>16GB.</li><li>Rear <strong>Camera</strong>0.7-megapixel.</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 16, code, 'Honda Accord', '<ul><li><strong>Year:</strong> 2005, </li><li><strong>Manufacturer:</strong> Honda, </li><li><strong>Model:</strong> Accord, </li><li><strong>Body Type:</strong> Sedan, </li><li><strong>Doors:</strong> 4, </li><li><strong>Colour:</strong> Red, </li><li><strong>Mileage:</strong> 70000, </li><li><strong>Engine Size:</strong> 2300, </li><li><strong>Transmission:</strong> Automatic, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 17, code, 'Coffee Maker', '<ul><li>Dimensions of the product: 22,1 x 34 x 43 cm</li><li>Weight of the product: 8,74 Kg</li> <li>Capacity: 1.7 litres</li><li>Volume: 60.9oz</li> <li>Power: 230 volts</li><li>Colour main: Black, Stainless Steel</li> <li>Pressure:15 bar</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 18, code, 'Samsung Galaxy J6', '<ul><li><strong>Capacity:</strong> 128GB</li><li><strong>Display:</strong> Retina HD display, 4.7-inch (diagonal) widescreen LCD, Multi-Touch display with IPS technology,<br/>1334-by-750-pixel resolution at 326 ppi,<br/>1400:1 contrast ratio (typical)</li><li><strong>Chip:</strong> A10 Fusion chip</li><li><strong>Camera:</strong> 12MP camera</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 19, code, 'Samsung Galaxy Tab S3', '<ul><li><strong>Display</strong>9.70-inch.</li><li><strong>Processor</strong>1GHz.</li><li><strong>Front Camera</strong>0.3-megapixel.</li><li><strong>Resolution</strong>768x1024 pixels.</li><li><strong>RAM</strong>512MB.</li><li>OSiOS 4.</li><li><strong>Storage</strong>16GB.</li><li>Rear <strong>Camera</strong>0.7-megapixel.</li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_translations` (`id`, `auction_id`, `language_code`, `name`, `description`, `short_description`) SELECT NULL, 20, code, 'Mercedes-Benz W121', '<ul><li><strong>Year:</strong> 1955, </li><li><strong>Manufacturer:</strong> Mercedes-Benz, </li><li><strong>Model:</strong> W121, </li><li><strong>Body Type:</strong> Coupe, </li><li><strong>Doors:</strong> 2, </li><li><strong>Colour:</strong> Silver, </li><li><strong>Mileage:</strong> 120000, </li><li><strong>Engine Size:</strong> 2000, </li><li><strong>Transmission:</strong> Automatic, </li><li><strong>Fuel:</strong> Petrol, </li></ul>', 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.' FROM <DB_PREFIX>languages;

DROP TABLE IF EXISTS `<DB_PREFIX>auction_images`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auction_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_file_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(3) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `auction_id` (`auction_id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23;

INSERT INTO `<DB_PREFIX>auction_images` (`id`, `auction_id`, `title`, `image_file`, `image_file_thumb`, `sort_order`, `is_active`) VALUES
(1, 1, '1 photo', 'auction1_1.jpg', 'auction1_1_thumb.jpg', 0, 1),
(2, 1, '2 photo', 'auction1_2.jpg', 'auction1_2_thumb.jpg', 1, 1),
(3, 1, '3 photo', 'auction1_3.jpg', 'auction1_3_thumb.jpg', 2, 1),
(4, 2, '1 photo', 'auction2_1.jpg', 'auction2_1_thumb.jpg', 0, 1),
(5, 3, '1 photo', 'auction3_1.jpg', 'auction3_1_thumb.jpg', 0, 1),
(6, 4, '1 photo', 'auction4_1.jpg', 'auction4_1_thumb.jpg', 0, 1),
(7, 5, '1 photo', 'auction5_1.jpg', 'auction5_1_thumb.jpg', 0, 1),
(8, 6, '1 photo', 'auction6_1.jpg', 'auction6_1_thumb.jpg', 0, 1),
(9, 7, '1 photo', 'auction7_1.jpg', 'auction7_1_thumb.jpg', 0, 1),
(10, 8, '1 photo', 'auction8_1.jpg', 'auction8_1_thumb.jpg', 0, 1),
(11, 9, '1 photo', 'auction9_1.jpg', 'auction9_1_thumb.jpg', 0, 1),
(12, 10, '1 photo', 'auction10_1.jpg', 'auction10_1_thumb.jpg', 0, 1),
(13, 11, '1 photo', 'auction11_1.jpg', 'auction11_1_thumb.jpg', 0, 1),
(14, 12, '1 photo', 'auction12_1.jpg', 'auction12_1_thumb.jpg', 0, 1),
(15, 13, '1 photo', 'auction13_1.jpg', 'auction13_1_thumb.jpg', 0, 1),
(16, 14, '1 photo', 'auction14_1.jpg', 'auction14_1_thumb.jpg', 0, 1),
(17, 15, '1 photo', 'auction15_1.jpg', 'auction15_1_thumb.jpg', 0, 1),
(18, 16, '1 photo', 'auction16_1.jpg', 'auction16_1_thumb.jpg', 0, 1),
(19, 17, '1 photo', 'auction17_1.jpg', 'auction17_1_thumb.jpg', 0, 1),
(20, 18, '1 photo', 'auction18_1.jpg', 'auction18_1_thumb.jpg', 0, 1),
(21, 19, '1 photo', 'auction19_1.jpg', 'auction19_1_thumb.jpg', 0, 1),
(22, 20, '1 photo', 'auction20_1.jpg', 'auction20_1_thumb.jpg', 0, 1);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_reviews`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auction_id` int(10) unsigned NOT NULL DEFAULT '0',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - pending, 1 - approved, 2 - declined',
  PRIMARY KEY (`id`),
  KEY `auction_id` (`auction_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

-- INSERT INTO `<DB_PREFIX>auction_reviews` (`id`, `auction_id`, `member_id`, `message`, `rating`, `created_at`, `status`) VALUES
-- (1, 1, 1, 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.', 4, NOW() - INTERVAL 1 MONTH, 1),
-- (2, 1, 2, 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.', 5, NOW() - INTERVAL 15 DAY, 1),
-- (3, 1, 1, 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.', 4, NOW() - INTERVAL 10 DAY, 1),
-- (4, 1, 2, 'Lorem ipsum dolor sit amet, consec adipiscing elit onvallis dignissim.', 4, NOW() - INTERVAL 5 DAY, 1);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_categories`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
  `icon` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `icon_thumb` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sort_order` smallint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8;

INSERT INTO `<DB_PREFIX>auction_categories` (`id`, `parent_id`, `icon`, `icon_thumb`, `sort_order`) VALUES
(1, 0, '', '', 0),
(2, 0, '', '', 1),
(3, 0, '', '', 2),
(4, 1, '', '', 3),
(5, 1, '', '', 4),
(6, 2, '', '', 5),
(7, 2, '', '', 6);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_category_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_category_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `language_code` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>auction_category_translations` (`id`, `category_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, 'Electronics', '' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>auction_category_translations` (`id`, `category_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, 'Automotive', '' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>auction_category_translations` (`id`, `category_id`, `language_code`, `name`, `description`) SELECT NULL, 3, code, 'Other', '' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>auction_category_translations` (`id`, `category_id`, `language_code`, `name`, `description`) SELECT NULL, 4, code, 'Smartphones', '' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>auction_category_translations` (`id`, `category_id`, `language_code`, `name`, `description`) SELECT NULL, 5, code, 'Tablets', '' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>auction_category_translations` (`id`, `category_id`, `language_code`, `name`, `description`) SELECT NULL, 6, code, 'Cars & Trucks', '' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>auction_category_translations` (`id`, `category_id`, `language_code`, `name`, `description`) SELECT NULL, 7, code, 'Motorcycles', '' FROM `<DB_PREFIX>languages`;

DROP TABLE IF EXISTS `<DB_PREFIX>auction_members`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT 0,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` enum('f','m') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  `birth_date` date NULL DEFAULT NULL,
  `website` varchar(125) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `company` varchar(125) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fax` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(125) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_2` varchar(125) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zip_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bids_amount` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES (NULL, 'member', 'member1', '1921a0fb5aad4577086262cb6fcb4fc1461e4a4cf2f12499593154c0e4f3a9b8', 'aSt/VJyNz1rTQHIMWrSseRHUAbv6cqRj', '', 'member1@exampe.com', 'en', '', NULL, '', NULL, '000.000.000.000', 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>auction_members` (`id`, `account_id`, `first_name`, `last_name`, `gender`, `birth_date`, `website`, `company`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`, `bids_amount`) VALUES (1, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'Jon', 'Carter', 'm', '1921-01-09', '', '', '(321) 123-4567', '', '216 West 50th Street', '', 'New York', '10019', 'US', 'NY', 50);
INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES (NULL, 'member', 'member2', '110b574b1e260b22521d740de651969daa30f0f944d375bd773cb61d95b1f37b', 'aSt/VJyNz1rTQHIMWrSseRHUAbv6cqRj', '', 'member2@exampe.com', 'en', '', NULL, '', NULL, '000.000.000.000', 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>auction_members` (`id`, `account_id`, `first_name`, `last_name`, `gender`, `birth_date`, `website`, `company`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`, `bids_amount`) VALUES (2, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'Donald', 'Johnson', 'm', '1945-01-09', '', '', '(321) 123-4567', '', '216 West 50th Street', '', 'New York', '10019', 'US', 'NY', 15);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_orders`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `order_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Package, 1 - Auction',
  `order_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_sum` decimal(10,2) NOT NULL DEFAULT '0.00',
  `vat_percent` decimal(5,3) NOT NULL DEFAULT '0.000',
  `vat_fee` decimal(11,2) NOT NULL DEFAULT '0.00',
  `vat_fee_info` varchar(512) NOT NULL DEFAULT '',
  `total_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET latin1 NOT NULL DEFAULT 'USD',
  `auction_id` int(11) NOT NULL DEFAULT '0',
  `package_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `shipment_address_id` int(11) NOT NULL DEFAULT '0',
  `is_admin_order` tinyint(1) NOT NULL DEFAULT '0',
  `transaction_number` varchar(30) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `created_at` datetime NULL DEFAULT NULL,
  `payment_date` datetime NULL DEFAULT NULL,
  `payment_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `payment_method` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Payment Company Account, 1 - Credit Card, 2 - E-Check',
  `coupon_id` int(11) NOT NULL DEFAULT '0',
  `additional_info` varchar(3072) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cc_type` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `cc_holder_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cc_number` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `cc_expires_month` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `cc_expires_year` varchar(4) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `cc_cvv_code` varchar(4) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - preparing, 1 - pending, 2 - paid, 3 - canceled',
  `status_changed` datetime NULL DEFAULT NULL,
  `email_sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `coupon_id` (`coupon_id`),
  KEY `package_id` (`package_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2;

INSERT INTO `<DB_PREFIX>auction_orders` (`id`, `order_number`, `order_type`, `order_description`, `order_price`, `discount_sum`, `vat_percent`, `vat_fee`, `total_price`, `currency`, `auction_id`, `package_id`, `member_id`, `is_admin_order`, `transaction_number`, `created_at`, `payment_date`, `payment_id`, `payment_method`, `coupon_id`, `additional_info`, `cc_type`, `cc_holder_name`, `cc_number`, `cc_expires_month`, `cc_expires_year`, `cc_cvv_code`, `status`, `status_changed`, `email_sent`) VALUES
(1, '7NCJWDLSA', 0, 'Package Bids', '100.00', '0.00', '0.000', '0.00', '100.00', 'USD', 0, 1, 1, 0, '', NOW(), NOW(), 1, 2, 0, '', '', '', '', '', '', '', 2, NOW(), 0),
(2, '6NAW2HK3P', 1, 'Auction', '300.00', '0.00', '0.000', '0.00', '100.00', 'USD', 1, 0, 1, 0, '', NOW(), NOW(), 1, 2, 0, '', '', '', '', '', '', '', 2, NOW(), 0);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_packages`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bids_amount` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>auction_packages` (`id`, `bids_amount`, `price`, `is_default`, `is_active`) VALUES
(1, '25', '15', 0, 1),
(2, '50', '25', 1, 1),
(3, '100', '40', 0, 1),
(4, '150', '50', 0, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>auction_package_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_package_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) unsigned NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>auction_package_translations` (`id`, `package_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, '25 Bids', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_package_translations` (`id`, `package_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, '50 Bids', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_package_translations` (`id`, `package_id`, `language_code`, `name`, `description`) SELECT NULL, 3, code, '100 Bids', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>auction_package_translations` (`id`, `package_id`, `language_code`, `name`, `description`) SELECT NULL, 4, code, '150 Bids', '' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>auction_shipments`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_shipments`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auction_id` int(11) unsigned NOT NULL DEFAULT '0',
  `member_id` int(11) unsigned NOT NULL DEFAULT 0,
  `carrier` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `tracking_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `shipping_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - pending, 1 - shipped, 2 - received, 3 - denied',
  `last_update_shipping_status` datetime NULL DEFAULT NULL,
  `shipped_date` datetime NULL DEFAULT NULL,
  `received_date` datetime NULL DEFAULT NULL,
  `shipping_comment` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auction_id` (`auction_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `<DB_PREFIX>auction_shipment_address`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_shipment_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL DEFAULT 0,
  `first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `company` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fax` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_2` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zip_code` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

INSERT INTO `<DB_PREFIX>auction_shipment_address` (`id`, `member_id`, `first_name`, `last_name`, `company`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`, `is_default`) VALUES
(1, 1, 'Jon', 'Carter', '', '(321) 123-4567', '', '216 West 50th Street', '', 'New York', '10019', 'US', 'NY', 1),
(2, 2, 'Donald', 'Johnson', '', '(321) 123-4567', '', '216 West 50th Street', '', 'New York', '10019', 'US', 'NY', 15);


DROP TABLE IF EXISTS `<DB_PREFIX>auction_taxes`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2;

INSERT INTO `<DB_PREFIX>auction_taxes` (`id`, `name`, `description`, `percent`, `sort_order`, `is_active`) VALUES
(1, 'VAT', '', '0.00', 0, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>auction_tax_countries`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_tax_countries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tax_id` int(11) unsigned NOT NULL DEFAULT '0',
  `country_code` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `percent` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `tax_id` (`tax_id`),
  KEY `country_code` (`country_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

INSERT INTO `<DB_PREFIX>auction_tax_countries` (`id`, `tax_id`, `country_code`, `percent`) VALUES
(1, 1, 'DE', '19.00'),
(2, 1, 'US', '15.00');


DROP TABLE IF EXISTS `<DB_PREFIX>auction_bids_history`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_bids_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auction_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `auction_id` int(11) unsigned NOT NULL DEFAULT '0',
  `member_id` int(11) unsigned NOT NULL DEFAULT '0',
  `size_bid` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `created_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auction_type_id` (`auction_id`),
  KEY `auction_id` (`auction_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>auction_bids_history` (`id`, `auction_type_id`, `auction_id`, `member_id`, `size_bid`, `created_at`) VALUES
(1, 1, 1, 1, '125.00', NOW() - INTERVAL 2 DAY),
(2, 1, 1, 2, '150.00', NOW() - INTERVAL 1 DAY),
(3, 1, 2, 1, '275.00', NOW() - INTERVAL 2 DAY),
(4, 1, 2, 2, '300.00', NOW() - INTERVAL 1 DAY),
(5, 1, 20, 1, '1350.00', NOW() - INTERVAL 1 DAY);

DROP TABLE IF EXISTS `<DB_PREFIX>auction_watchlist`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>auction_watchlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` int(11) unsigned NOT NULL DEFAULT '0',
  `member_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `auction_id` (`auction_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>auction_watchlist` (`id`, `auction_id`, `member_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1);