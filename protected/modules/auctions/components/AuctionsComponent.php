<?php
/**
 * AuctionsComponent
 *
 * PUBLIC:                   PRIVATE
 * -----------               ------------------
 * init                      _redirect
 * prepareTab                _getCountAuctionForCategory
 * prepareSubTab
 * priceFormating
 * getCountries
 * getStates
 * drawFooterLinks
 * checkRecordAccess
 * drawDashboardBlock
 * drawFilteringBlock
 * checkFreePlanInOrders
 * categoriesList
 * auctionTypesList
 * drawAuctionForm
 * checkAuctionInWatchlist
 * drawTimerScript
 * callDrawTimerScript
 * getSimilarAuction
 * getAuctionImages
 * registerAuctionTypeScriptFiles
 * checkActiveAuction
 * getBidsHistoryInfo
 * getHtmlCurrentBidHistory
 * getParentCategories
 * drawRecentlyClosedAuctionsBlock
 * drawLastWinnersBlock
 * drawStepShipment
 * drawPayOrShipmentLink
 * drawConfirmDeliveredLink
 *
 *
 *
 *
 * STATIC
 * -------------------------------------------
 * init
 *
 */

namespace Modules\Auctions\Components;


//Models
use \Modules\Auctions\Models\Auctions,
    \Modules\Auctions\Models\AuctionImages,
    \Modules\Auctions\Models\AuctionTypes,
    \Modules\Auctions\Models\BidsHistory,
    \Modules\Auctions\Models\Categories,
    \Modules\Auctions\Models\Members,
    \Modules\Auctions\Models\Reviews,
    \Modules\Auctions\Models\Watchlist;

// Framework
use \AuctionType,
    \Accounts,
    \CComponent,
    \CAuth,
    \CConfig,
    \CCurrency,
    \CString,
    \CHtml,
    \CLocale,
    \CLoader,
    \CWidget,
    \ModulesSettings;

// Global
use \A,
    \Bootstrap,
    \Countries,
    \LocalTime,
    \Modules,
    \States,
    \Website;
use Modules\Auctions\Models\Orders;


class AuctionsComponent extends CComponent
{

    public static function init()
    {
        return parent::init(__CLASS__);
    }


    /**
     * Prepares Auctions module tabs
     * @param string $activeTab
     * @return html
     */
    public static function prepareTab($activeTab = 'info')
    {
        return CWidget::create('CTabs', array(
            'tabsWrapper' => array('tag' => 'div', 'class' => 'title'),
            'tabsWrapperInner' => array('tag' => 'div', 'class' => 'tabs'),
            'contentWrapper' => array(),
            'contentMessage' => '',
            'tabs' => array(
                A::t('app', 'Settings') => array('href' => Website::getBackendPath() . 'modules/settings/code/auctions', 'id' => 'tabSettings', 'content' => '', 'active' => false, 'htmlOptions' => array('class' => 'modules-settings-tab')),
                A::t('auctions', 'Categories') => array('href' => 'categories/manage', 'id' => 'tabCategories', 'content' => '', 'active' => ($activeTab == 'categories' ? true : false)),
                A::t('auctions', 'Auction Types') => array('href' => 'auctionTypes/manage', 'id' => 'tabAuctionTypes', 'content' => '', 'active' => ($activeTab == 'auction_types' ? true : false)),
                A::t('auctions', 'Auctions') => array('href' => 'auctions/manage', 'id' => 'tabAuctions', 'content' => '', 'active' => ($activeTab == 'auctions' ? true : false)),
                A::t('auctions', 'Members') => array('href' => 'members/manage', 'id' => 'tabMembers', 'content' => '', 'active' => ($activeTab == 'members' ? true : false)),
                A::t('auctions', 'Packages') => array('href' => 'packages/manage', 'id' => 'tabPackages', 'content' => '', 'active' => ($activeTab == 'packages' ? true : false)),
                A::t('auctions', 'Taxes') => array('href' => 'taxes/manage', 'id' => 'tabTaxes', 'content' => '', 'active' => ($activeTab == 'taxes' ? true : false)),
                A::t('auctions', 'Orders') => array('href' => 'orders/manage', 'id' => 'tabOrders', 'content' => '', 'active' => ($activeTab == 'orders' ? true : false)),
                A::t('auctions', 'Statistics') => array('href' => 'statistics/manage', 'id' => 'tabStatistics', 'content' => '', 'active' => ($activeTab == 'statistics' ? true : false)),
            ),
            'events' => array(//'click'=>array('field'=>$errorField)
            ),
            'return' => true,
        ));
    }

    /**
     * Prepares Auctions module tabs
     * @param array $param
     * @return html
     */
    public static function prepareSubTab($param = array())
    {
        $output = '';
        $parentTab = !empty($param['parentTab']) ? $param['parentTab'] : '';
        $activeTab = !empty($param['activeTab']) ? $param['activeTab'] : '';
        $additionText = !empty($param['additionText']) ? $param['additionText'] : '';
        $id = !empty($param['id']) ? $param['id'] : 0;
        $name = !empty($param['name']) ? $param['name'] : '';

        $arrPrepareTabs = array(
            'tax_countries' => array(
                array('title' => 'taxes', 'url' => 'taxes/manage', 'text' => A::t('auctions', 'Taxes')),
            ),
            'country' => array(
                array('title' => 'taxes', 'url' => 'taxes/manage', 'text' => A::t('auctions', 'Taxes')),
                array('title' => 'tax_country', 'url' => 'taxes/manageCountries/taxId/' . $id, 'text' => $name),
            ),
            'sub_categories' => array(
                array('title' => 'categories', 'url' => 'categories/manage', 'text' => A::t('auctions', 'Categories')),
            ),
            'add_or_edit_sub_category' => array(
                array('title' => 'categories', 'url' => 'categories/manage', 'text' => A::t('auctions', 'Categories')),
                array('title' => 'sub_category', 'url' => 'categories/manage/parentId/' . $id, 'text' => $name),
            ),
            'images' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
            ),
            'add_or_edit_image' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
                array('title' => 'images', 'url' => 'auctionImages/manage/auctionId/' . $id, 'text' => $name),
            ),
            'shipments' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
                array('title' => 'auction', 'url' => 'auctions/edit/id/' . $id, 'text' => $name),
                array('title' => 'members', 'url' => 'auctions/auctionMembers/auctionId/' . $id, 'text' => A::t('auctions', 'Auction Members')),
            ),
            'add_or_edit_shipment' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
                array('title' => 'auction', 'url' => 'auctions/edit/id/' . $id, 'text' => $name),
                array('title' => 'shipment', 'url' => 'shipments/manage/auctionId/' . $id, 'text' => A::t('auctions', 'Shipments')),
            ),
            'edit_auction' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
            ),
            'shipment_active' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
                array('title' => 'shipment', 'url' => 'shipments/manage/auctionId/' . $id, 'text' => A::t('auctions', 'Shipments')),
                array('title' => 'members', 'url' => 'auctions/auctionMembers/auctionId/' . $id, 'text' => A::t('auctions', 'Auction Members')),
            ),
            'bids_history' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
            ),
            'add_or_edit_bid_history' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
                array('title' => 'bid_history', 'url' => 'bidsHistory/manage/auctionId/' . $id, 'text' => $name),
            ),
            'auction_members' => array(
                array('title' => 'auctions', 'url' => 'auctions/manage', 'text' => A::t('auctions', 'Auctions')),
                array('title' => 'auction', 'url' => 'auctions/edit/id/' . $id, 'text' => $name),
                array('title' => 'shipment', 'url' => 'shipments/manage/auctionId/' . $id, 'text' => A::t('auctions', 'Shipments')),
            ),
            'reviews' => array(
                array('title' => 'approved', 'url' => 'reviews/manage/auctionId/' . $id . '/status/approved', 'text' => A::t('auctions', 'Approved')),
                array('title' => 'pending', 'url' => 'reviews/manage/auctionId/' . $id . '/status/pending', 'text' => A::t('auctions', 'Pending')),
                array('title' => 'declined', 'url' => 'reviews/manage/auctionId/' . $id . '/status/declined', 'text' => A::t('auctions', 'Declined')),
            ),
        );

        if (isset($arrPrepareTabs[$parentTab])) {
            foreach ($arrPrepareTabs[$parentTab] as $tab) {
                $output .= '<a class="sub-tab' . ($activeTab == $tab['title'] ? ' active' : ' previous') . '" href="' . $tab['url'] . '">' . $tab['text'] . '</a>';
                $output .= $activeTab == $tab['title'] && !empty($additionText) ? '» <a class="sub-tab active"><b>' . $additionText . '</b></a>&nbsp;' : '';
            }
        }

        return $output;
    }

    /**
     * Price formatting
     * @param string $record
     * @param array $params
     * @return string $output
     */
    public static function priceFormating($record = '', $params = array())
    {
        $output = '';

        $fieldName = isset($params['field_name']) ? $params['field_name'] : 'price';

        if (isset($record[$fieldName])) {
            $output = CCurrency::format($record[$fieldName]);
        }

        return $output;
    }

    /*
     * Get All Countries
     */
    public static function getCountries()
    {
        $countries = array();
        $countries['countries'][''] = A::t('app', '-- select --');
        $countriesResult = Countries::model()->findAll(array('condition' => 'is_active = 1', 'order' => 'sort_order DESC, country_name ASC'));
        if (!empty($countriesResult) && is_array($countriesResult)) {
            foreach ($countriesResult as $key => $val) {
                $countries['countries'][$val['code']] = $val['country_name'];
                if ($val['is_default']) $countries['default_country_code'] = $val['code'];
            }
        }

        return $countries;
    }

    /**
     * Get All States
     * @return array
     */
    public static function getStates()
    {
        $arrStateNames = array();

        $statesResult = States::model()->findAll(array('condition' => 'is_active = 1', 'order' => 'sort_order DESC, state_name ASC'));
        if (is_array($statesResult)) {
            foreach ($statesResult as $key => $val) {
                $arrStateNames[$val['code']] = $val['state_name'];
            }
        }

        return $arrStateNames;
    }

    /**
     * Prepare links for footer block
     */
    public static function drawFooterLinks()
    {
        $output = '';

        if (!Members::isLogin() && !CAuth::isLoggedInAsAdmin()) {
            $output .= CHtml::link(A::t('auctions', 'Member Login'), 'members/login');
        } elseif (Members::isLogin()) {
            $output .= CHtml::link(A::t('auctions', 'Dashboard'), 'members/dashboard');
            $output .= ' | ' . CHtml::link('<i class="fa fa-sign-out"></i> ' . A::t('auctions', 'Logout'), 'members/logout');
        } elseif (CAuth::isLoggedInAsAdmin()) {
            $output .= CHtml::link(A::t('app', 'Back to Admin Panel'), Website::getBackendPath() . 'dashboard');
        }

        if (in_array(APPHP_MODE, array('debug', 'demo')) && !CAuth::isLoggedIn()) {
            if (!empty($output)) {
                $output .= ' | ';
            }
            $output .= CHtml::link(A::t('app', 'Admin Login'), Website::getBackendPath() . 'admin/login');
        }

        return $output;
    }

    /**
     * Check if passed record ID is valid
     * @param int $id
     * @param string $model
     * @param bool $redirect
     * @param string $redirectPath
     * @param array $findParams
     * @return bool|object
     */
    public static function checkRecordAccess($id = 0, $model, $redirect = true, $redirectPath = '', $findParams = array())
    {
        $result = '';
        // Set Redirect Path
        if (CAuth::isLoggedInAsAdmin()) {
            $defaultRedirectPath = Website::getBackendPath() . 'modules/index';
        } else {
            $defaultRedirectPath = Website::getDefaultPage();
        }
        $redirectPath = !empty($redirectPath) ? $redirectPath : $defaultRedirectPath;

        // Exit if parameters is empty
        if (empty($id) && empty($model)) {
            A::app()->getSession()->setFlash('alert', A::t('auctions', 'Input incorrect parameters'));
            A::app()->getSession()->setFlash('alertType', 'error');
            self::_redirect($redirectPath);
        }

        // New copy of the Class and record search
        $objectName = '\Modules\Auctions\Models\\' . $model;
        if (class_exists($objectName)) {
            $object = new $objectName;
            if ($object) {
                $condition = '';
                $tableName = CConfig::get('db.prefix') . $object::model()->getTableName();
                if (!empty($findParams)) {
                    foreach ($findParams as $field => $value) {
                        $condition .= $tableName . '.' . $field . ' = ' . $value;
                    }
                }
                $result = $object::model()->findByPk($id, $condition);
            }
        }

        //Exit if no record is found in the database
        if (empty($result) && $redirect) {
            A::app()->getSession()->setFlash('alert', A::t('auctions', 'Input incorrect parameters'));
            A::app()->getSession()->setFlash('alertType', 'error');
            self::_redirect($redirectPath);
        }

        return $result;
    }

    /**
     * Draw Dashboard Link
     * @return void
     */
    public static function drawDashboardBlock()
    {
        if (CAuth::getLoggedId() && CAuth::getLoggedRole() == 'member') {
            $view = A::app()->view;
            $view->renderContent('drawDashboardBlock');
        }
    }

    /**
     * Draw Dashboard Link
     * @return void
     */
    public static function drawFilteringBlock()
    {
        $view = A::app()->view;
        $prepareCategories = array();

        $categories = Categories::model()->findAll();
        if (!empty($categories)) {
            $prepareCategories = array();

            foreach ($categories as $category) {
                $countAuctions = self::_getCountAuctionForCategory($category['id']);
                if ($category['parent_id'] == 0) {
                    $prepareCategories[$category['id']] = array('name' => $category['name'] . ' (' . $countAuctions . ')', 'subCategories' => array());
                } else {
                    $prepareCategories[$category['parent_id']]['subCategories'][$category['id']] = $category['name'] . ' (' . $countAuctions . ')';
                }
            }
        }

        $view->categoriesList = $prepareCategories;
        $view->renderContent('drawFilteringBlock');
    }


    /**
     * Check Free Plan In Orders
     * @param bool $redirect
     * @param string $redirectPath
     * @return bool
     */
    public static function checkFreePlanInOrders($redirect = false, $redirectPath = 'Home/index')
    {
        $result = false;

        $memberId = CAuth::getLoggedRoleId();
        $member = self::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');

        $orders = Orders::model()->count('member_id = :member_id AND order_price = 0', array('member_id' => $member->id));
        if ($orders > 0 && !$redirect) {
            $result = true;
        } elseif ($orders > 0 && $redirect) {
            self::_redirect($redirectPath);
        }

        return $result;
    }

    /**
     * Get Active Categories List
     * @param bool $emptyOption
     * @param bool $markSubcategories
     * @param bool $qtyAuctions
     * @return array
     */
    public static function categoriesList($emptyOption = true, $markSubcategories = true, $qtyAuctions = false, $groupParentCategories = false)
    {
        $categoriesList = array();

        $categories = Categories::model()->findAll();
        if (!empty($categories)) {
            $prepareCategories = array();
            if ($emptyOption) {
                $categoriesList[''] = A::t('app', '-- select --');
            }
            foreach ($categories as $category) {
                if ($category['parent_id'] == 0) {
                    $prepareCategories[$category['id']] = array('name' => $category['name'], 'subCategories' => array());
                } else {
                    $prepareCategories[$category['parent_id']]['subCategories'][$category['id']] = $category['name'];
                }
            }

            foreach ($prepareCategories as $id => $prepareCategory) {
                $countAuctions = 0;
                if ($qtyAuctions) {
                    $countAuctions = self::_getCountAuctionForCategory($id);
                }

                $categoriesList[$id] = $prepareCategory['name'] . ($qtyAuctions ? ' (' . $countAuctions . ')' : '');
                foreach ($prepareCategory['subCategories'] as $subId => $subCategoryName) {
                    $countSubAuctions = 0;
                    if ($qtyAuctions) {
                        $countSubAuctions = self::_getCountAuctionForCategory($subId);
                    }
                    $categoriesList[$subId] = ($markSubcategories ? ' • ' : '') . $subCategoryName . ($qtyAuctions ? ' (' . $countSubAuctions . ')' : '');
                }
            }
        }

        return $categoriesList;
    }

    /**
     * Get Active Auction Types List
     * @return array
     */
    public static function auctionTypesList()
    {
        $auctionTypesList = array();

        $auctionTypes = AuctionTypes::model()->findAll('is_active');
        if (!empty($auctionTypes)) {
            $auctionTypesList[''] = A::t('app', '-- select --');
            foreach ($auctionTypes as $auctionType) {
                $auctionTypesList[$auctionType['id']] = $auctionType['name'];
            }
        }

        return $auctionTypesList;
    }

    /**
     * Draw Auction Form
     * @param array $params
     * @return string
     */
    public static function drawAuctionForm($params = array())
    {
        $auctionTypeName = '';

        $auctionId = isset($params['auction_id']) ? $params['auction_id'] : 0;
        $auctionTypeId = isset($params['auction_type_id']) ? $params['auction_type_id'] : 0;


        if (!empty($auctionTypeId)) {
            $auctionType = AuctionsComponent::checkRecordAccess($auctionTypeId, 'AuctionTypes', false);
            if (isset($auctionType)) {
                $auctionTypeName = $auctionType->name ? mb_strtolower($auctionType->name) : '';
            }
        }

        $output = '';

        if (!empty($auctionId)) {

            $view = A::app()->view;

            $view->auctionId = $auctionId;
            $view->auctionTypeName = $auctionTypeName;
            $view->bids = isset($params['bids']) ? $params['bids'] : 0;
            $view->bidders = isset($params['bidders']) ? $params['bidders'] : 0;
            $view->hits = isset($params['hits']) ? $params['hits'] : 0;
            $view->startPrice = isset($params['start_price']) ? $params['start_price'] : 0;
            $view->currentBid = isset($params['current_bid']) ? $params['current_bid'] : 0;
            $view->nextStep = isset($params['next_step']) ? $params['next_step'] : 0;
            $view->buyNowPrice = isset($params['buy_now_price']) ? $params['buy_now_price'] : 0;
            $view->winner = isset($params['winner']) ? $params['winner'] : 0;
            $view->winnerId = isset($params['winner_id']) ? $params['winner_id'] : 0;
            $view->status = isset($params['status']) ? $params['status'] : 0;
            $view->isLoggedMemberId = CAuth::getLoggedRoleId();
            $view->auctionNotStart = false;
            $view->auctionClosed = $view->status == 3 ? true : false;

            $currentDate = CLocale::date('Y-m-d H:i:s');
            $dateFrom = isset($params['date_from']) ? $params['date_from'] : 0;
            $dateTo = isset($params['date_to']) ? $params['date_to'] : 0;

            if ($dateFrom > $currentDate) {
                $view->auctionNotStart = true;
            } elseif ($dateTo < $currentDate) {
                $view->auctionClosed = true;
            }

            $view->checkAuctionInWatchlist = AuctionsComponent::checkAuctionInWatchlist($auctionId, false);

            $output = $view->renderContent('drawAuctionForm', true);
        }

        return $output;
    }

    /**
     * Check if there is an auction on the waiting list
     * @param int $auctionId
     * @param bool $returnObject
     * @return object|bool
     */
    public static function checkAuctionInWatchlist($auctionId = 0, $returnObject = true)
    {
        $result = false;
        $memberId = CAuth::getLoggedRoleId();

        if (!empty($auctionId) && !empty($memberId)) {
            $watchlist = Watchlist::model()->find('auction_id = :auction_id AND member_id = :member_id', array(':auction_id' => $auctionId, ':member_id' => $memberId));
            if ($watchlist) {
                $result = $returnObject ? $watchlist : true;
            }
        }

        return $result;
    }

    /**
     * Javascript function for timer
     * @return bool
     */
    public static function drawTimerScript()
    {
        return A::app()->getClientScript()->registerScript(
            'timer',
            '
            function auctions_Timer(id, dateTo)
            {
                if(id === "") return false;
                dateTo = Number(dateTo); 
                var myDate = new Date(dateTo);
                $("#"+id).countdownTimer(myDate, function (event) {
                
                    $(this).html(
                        event.strftime(
                            \'<div class="timer-wrapper"><div class="time">%D</div><span class="text">' . A::t('auctions', 'days') . '</span></div><div class="timer-wrapper"><div class="time">%H</div><span class="text">' . A::t('auctions', 'hrs') . '</span></div><div class="timer-wrapper"><div class="time">%M</div><span class="text">' . A::t('auctions', 'min') . '</span></div><div class="timer-wrapper"><div class="time">%S</div><span class="text">' . A::t('auctions', 'sec') . '</span></div>\'
                        )
                    );
                });
            }
            ',
            3
        );
    }

    /**
     * Call javascript function for timer
     * @param string $callTimer
     * @return object|bool
     */
    public static function callDrawTimerScript($callTimer = '')
    {
        return A::app()->getClientScript()->registerScript(
            'callTimer',
            '
            $(document).ready(function () {
            ' . $callTimer . '
            });
            ',
            3
        );
    }


    /**
     * Get Similar Auction
     * @param int $auctionId
     * @param int $categoryId
     * @return array
     */
    public static function getSimilarAuction($auctionId = 0, $categoryId = 0)
    {
        $similarAuctions = array();
        $categoryIds = array();
        $auctionIds = array();
        $auctionTypeIds = array();
        $auctionImages = array();

        if (!empty($auctionId) && !empty($categoryId)) {
            // Search Category and Parent Category
            $tableNameCategories = CConfig::get('db.prefix') . Categories::model()->getTableName();
            $category = Categories::model()->findByPk($categoryId);
            if ($category) {
                $categoryIds[] = $categoryId;
                if ($category->parent_id > 0) {
                    $parentCategories = Categories::model()->findAll($tableNameCategories . '.parent_id = ' . $category->parent_id);
                    if (!empty($parentCategories) && is_array($parentCategories)) {
                        foreach ($parentCategories as $parentCategory) {
                            if (!in_array($parentCategory['id'], $categoryIds))
                                $categoryIds[] = $parentCategory['id'];
                        }
                    }
                } else {
                    $parentCategories = Categories::model()->findAll($tableNameCategories . '.parent_id = ' . $categoryId);
                    if (!empty($parentCategories) && is_array($parentCategories)) {
                        foreach ($parentCategories as $parentCategory) {
                            if (!in_array($parentCategory['id'], $categoryIds))
                                $categoryIds[] = $parentCategory['id'];
                        }
                    }
                }

                // Search Similar Auction
                $tableNameAuctions = CConfig::get('db.prefix') . Auctions::model()->getTableName();
                $auctions = Auctions::model()->findAll(array('condition' => $tableNameAuctions . '.category_id IN (' . (implode(',', $categoryIds)) . ') AND ' . $tableNameAuctions . '.id != ' . $auctionId . ' AND ' . $tableNameAuctions . '.date_from <= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND ' . $tableNameAuctions . '.date_to >= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND status = 1', 'orderBy' => $tableNameAuctions . '.hits DESC', 'limit' => 10));

                if (!empty($auctions) && is_array($auctions)) {
                    //Find Auction Images
                    foreach ($auctions as $auction) {
                        if (!in_array($auction['id'], $auctionIds)) {
                            $auctionIds[] = $auction['id'];
                            $auctionTypeIds[] = $auction['auction_type_id'];
                        }
                    }

                    $auctionImages = AuctionsComponent::getAuctionImages($auctionIds);

                    foreach ($auctions as $auction) {
                        $similarAuctions[$auction['id']] = $auction;
                        // Add auction Image
                        if (isset($auctionImages[$auction['id']])) {
                            $similarAuctions[$auction['id']]['image_title'] = $auctionImages[$auction['id']]['title'];
                            $similarAuctions[$auction['id']]['image_file'] = $auctionImages[$auction['id']]['image_file'];
                        }
                    }

                    //Register Script Files For libraries/classes
                    if (!empty($auctionTypeIds) && is_array($auctionTypeIds)) {
                        AuctionsComponent::registerAuctionTypeScriptFiles($auctionTypeIds);
                    }
                }
            }
        }

        return $similarAuctions;
    }

    /**
     * Get auction images
     * @param array $auctionIds
     * @return array
     * */
    public static function getAuctionImages($auctionIds = array())
    {
        $auctionImages = array();

        if (!empty($auctionIds) && is_array($auctionIds)) {
            $tableAuctionImagesName = CConfig::get('db.prefix') . AuctionImages::model()->getTableName();
            $allAuctionImages = AuctionImages::model()->findAll(array('condition' => $tableAuctionImagesName . '.auction_id IN (' . implode(',', $auctionIds) . ')', 'orderBy' => 'id ASC, sort_order ASC'));
            if (!empty($allAuctionImages) && is_array($allAuctionImages)) {
                foreach ($allAuctionImages as $allAuctionImage) {
                    if (!isset($auctionImages[$allAuctionImage['auction_id']])) {
                        $auctionImages[$allAuctionImage['auction_id']]['title'] = $allAuctionImage['title'];
                        $auctionImages[$allAuctionImage['auction_id']]['image_file'] = $allAuctionImage['image_file'];
                    }
                }
            }
        }

        return $auctionImages;
    }

    /**
     * Register Script Files For libraries/libAuction
     * @param array $auctionTypeIds
     * @return void
     * */
    public static function registerAuctionTypeScriptFiles($auctionTypeIds = array())
    {
        if (!empty($auctionTypeIds) && is_array($auctionTypeIds)) {
            CLoader::library('libauction/AuctionType.php');
            foreach ($auctionTypeIds as $key => $auctionTypeId) {
                $auctionType = AuctionType::init($auctionTypeId);
                $auctionType->registerFiles();
            }
        }
    }

    /**
     * Check Active The Auction
     * @param string $startDate
     * @param string $endDate
     * @param int $status
     * @return array
     */
    public static function checkActiveAuction($startDate = '', $endDate = '', $status = 0)
    {
        $currentDate = date('Y-m-d H:i:s');
        $result = array();

        if (empty($startDate) || empty($endDate)) {
            $result['active'] = false;
        } elseif ($startDate > $currentDate) {
            $result['active'] = false;
            $result['status'] = 'not_started';
        } elseif ($endDate < $currentDate) {
            $result['active'] = false;
            $result['status'] = 'end_time';
        } elseif (in_array($status, array(0, 2, 3, 4))) {
            $result['active'] = false;
            $result['status'] = 'closed';
        } else {
            $result['active'] = true;
        }

        return $result;
    }

    /**
     * Check Active The Auction
     * @param int $auctionId
     * @param bool $returnAllBidsHistory
     * @return array
     */
    public static function getBidsHistoryInfo($auctionId = 0, $returnAllBidsHistory = true)
    {
        $result = array();

        if (!empty($auctionId)) {

            $winnerId = 0;
            $winner = '';
            $bidWinner = array();

            $tableNameBidsHistory = CConfig::get('db.prefix') . BidsHistory::model()->getTableName();
            $bidsHistory = BidsHistory::model()->findAll(array('condition' => $tableNameBidsHistory . '.auction_id = :auction_id', 'orderBy' => $tableNameBidsHistory . '.created_at DESC'), array(':auction_id' => $auctionId));
            $countBidders = BidsHistory::model()->count(array('condition' => $tableNameBidsHistory . '.auction_id = :auction_id', 'select' => $tableNameBidsHistory . '.member_id', 'count' => '*', 'groupBy' => $tableNameBidsHistory . '.member_id', 'allRows' => true), array(':auction_id' => $auctionId));
            $countBidders = !empty($countBidders) ? count($countBidders) : 0;
            $countBids = !empty($bidsHistory) ? count($bidsHistory) : 0;

            if (!empty($bidsHistory) && is_array($bidsHistory)) {
                foreach ($bidsHistory as $bid) {
                    if (empty($bidWinner['created_at']) || $bid['created_at'] >= $bidWinner['created_at']) {
                        $bidWinner = $bid;
                    }
                }
                if (!empty($bidWinner)) {
                    if ($bidWinner['member_id'] == CAuth::getLoggedRoleId()) {
                        $winner = A::t('auctions', 'You\'re winner');
                    } else {
                        $winner = $bidWinner['first_name'] . ' ' . (CString::substr($bid['last_name'], 1, '', false) . '.');
                    }
                    $winnerId = $bidWinner['member_id'];
                }
            } else {
                $winner = A::t('auctions', 'No Bids');
            }

            $result['bids_history'] = $returnAllBidsHistory ? $bidsHistory : array();
            $result['count_bidders'] = $countBidders;
            $result['count_bids'] = $countBids;
            $result['winner'] = $winner;
            $result['winner_id'] = $winnerId;

        }
        return $result;
    }

    /**
     * Get current bid history for the auction
     * @param array $bidsHistory
     * @param int $memberId
     * @param float $currentBid
     * @return string
     */
    public static function getHtmlCurrentBidHistory($bidsHistory = array(), $currentBid = 0.00)
    {
        $result = '';

        $memberId = CAuth::getLoggedRoleId();
        if (!empty($bidsHistory) && is_array($bidsHistory) && !empty($currentBid)) {
            $dateTimeFormat = Bootstrap::init()->getSettings()->datetime_format;
            foreach ($bidsHistory as $bid) {
                if ($bid['size_bid'] == $currentBid) {
                    $result = '<tr><td>' . (CLocale::date($dateTimeFormat, $bid['created_at'])) . '</td><td>' . ($bid['first_name'] . ' ' . (CString::substr($bid['last_name'], 1, '', false) . '.')) . ($bid['member_id'] == $memberId ? '<span class=\"v-menu-item-info bg-success\">' . A::t('auctions', 'It\'s You') . '</span>' : '') . '</td><td>' . (CCurrency::format($bid['size_bid'])) . '</td></tr>';
                }
            }
        }

        return $result;
    }

    /**
     * Get category and parent category
     * @param int $categoryId
     * @return array
     * */
    public static function getParentCategories($categoryId = 0)
    {
        $parentCategories = array();

        if (!empty($categoryId)) {
            $category = Categories::model()->findByPk($categoryId);
            if (!empty($category)) {
                $parentCategories['current_category'] = array(
                    'id' => $category->id,
                    'name' => $category->name,
                );
                if ($category->parent_id > 0) {
                    $category = Categories::model()->findByPk($category->parent_id);
                    if (!empty($category)) {
                        $parentCategories['parent_category'] = array(
                            'id' => $category->id,
                            'name' => $category->name,
                        );
                    }
                }
            }
        }


        return $parentCategories;
    }


    /**
     * Shows recently closed auctions
     * @return void
     */
    public static function drawRecentlyClosedAuctionsBlock()
    {

        $view = A::app()->view;
        $auctionsIds = array();
        $recentlyClosedAuctions = array();
        $auctionImages = array();

        $closedAuctionsCountDays = (int)ModulesSettings::model()->param('auctions', 'closed_auctions_count_days');
        $closedAuctionsCountDays = '-' . $closedAuctionsCountDays . ' days';

        $tableNameAuctions = CConfig::get('db.prefix') . Auctions::model()->getTableName();
        $recentlyClosedAuctions = Auctions::model()->findAll(array('condition' => $tableNameAuctions . '.date_to <= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND ' . $tableNameAuctions . '.date_to >= "' . CLocale::date('Y-m-d H:i:s', strtotime($closedAuctionsCountDays), true) . '" AND status IN(0,2,3,4)', 'orderBy' => $tableNameAuctions . '.status_changed ASC', 'limit' => '0, 5'));

        if (!empty($recentlyClosedAuctions) && is_array($recentlyClosedAuctions)) {
            foreach ($recentlyClosedAuctions as $auction) {
                if (!in_array($auction['id'], $auctionsIds)) {
                    $auctionsIds[] = $auction['id'];
                }
            }

            if (!empty($auctionsIds) && is_array($auctionsIds)) {
                //Find Auction Images
                $auctionImages = AuctionsComponent::getAuctionImages($auctionsIds);
            }
        }

        $view->recentlyClosedAuctions = $recentlyClosedAuctions;
        $view->auctionImages = $auctionImages;
        $view->renderContent('drawRecentlyClosedAuctions');
    }

    /**
     * Shows recently closed auctions
     * @return void
     */
    public static function drawLastWinnersBlock()
    {

        $view = A::app()->view;
        $lastWinnersIds = array();
        $lastWinners = array();
        $closedAuctions = array();
        $auctionImages = array();

        $tableNameAuctions = CConfig::get('db.prefix') . Auctions::model()->getTableName();
        $closedAuctions = Auctions::model()->findAll(array('condition' => $tableNameAuctions . '.date_to <= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND status IN(0,3,4)', 'orderBy' => $tableNameAuctions . '.date_to DESC', 'limit' => '0, 5'));

        if (!empty($closedAuctions) && is_array($closedAuctions)) {
            foreach ($closedAuctions as $auction) {
                if (!in_array($auction['winner_member_id'], $lastWinnersIds)) {
                    $lastWinnersIds[] = $auction['winner_member_id'];
                }
            }

            if (!empty($lastWinnersIds) && is_array($lastWinnersIds)) {
                $tableNameMembers = CConfig::get('db.prefix') . Members::model()->getTableName();
                $members = Members::model()->findAll($tableNameMembers . '.id IN (' . implode(',', $lastWinnersIds) . ')');
                if (!empty($members) && is_array($members)) {
                    foreach ($members as $member) {
                        $lastWinners[$member['id']] = CHtml::encode($member['first_name'] . ' ' . (CString::substr($member['last_name'], 1, '', false) . '.'));
                    }
                }
            }
        }

        $view->lastWinners = $lastWinners;
        $view->auctionImages = $auctionImages;
        $view->renderContent('drawLastWinners');
    }

    /**
     * Draw Shipments Steps
     * @param int $auctionId
     * @param int $shipmentId
     * @return void
     */
    public static function drawStepShipment($shipmentId = 0)
    {
        $view = A::app()->view;

        $redirectPath = 'auctions/manage';
        $loggedRole = CAuth::getLoggedRole();

        $shipment = AuctionsComponent::checkRecordAccess($shipmentId, 'Shipments', true, $redirectPath);

        if ($loggedRole == 'members') {
            $memberId = CAuth::getLoggedRoleId();
            $redirectPath = 'Home/index';
            $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, $redirectPath);
            if ($shipment->member_id !== $member->id) {
                self::_redirect('Home/index');
            }
        }

        $view->shipmentShippingStatus = $shipment->shipping_status;
        return $view->renderContent('drawStepShipment', true);
    }

    /**
     * Draw pay or shipment link for the won auction
     * @param string $record
     * @param array $params
     * @return string $output
     */
    public static function drawPayOrShipmentLink($record = '', $params = array())
    {
        $output = '';

        // A::t('auctions', 'Pay')
        $fieldName = isset($params['field_name']) ? $params['field_name'] : '';

        if (!empty($record) && is_array($record) && !empty($fieldName)) {
            if ($record[$fieldName]) {
                $output = '[ ' . (CHtml::tag('a', array('href' => 'shipments/myShipments?auction_id=' . $record['id'] . '&but_filter=Filter', 'title' => A::t('auctions', 'Shipment')), A::t('auctions', 'Shipment'))) . ' ]';
            } else {
                $output = '[ ' . (CHtml::tag('a', array('href' => 'checkout/auctionPaymentForm/id/' . $record['id'] . '/type/won', 'title' => A::t('auctions', 'Pay')), A::t('auctions', 'Pay'))) . ' ]';

            }
        }

        return $output;
    }

    /**
     * Draw pay or shipment link for the won auction
     * @param string $record
     * @param array $params
     * @return string $output
     */
    public static function drawReviewLink($record = '', $params = array())
    {
        $output = '';

        // A::t('auctions', 'Pay')
        $fieldName = isset($params['field_name']) ? $params['field_name'] : '';

        $isMember = CAuth::isLoggedInAs('member');
        if (!empty($record) && is_array($record) && !empty($fieldName) && $isMember) {
            $tableNameAccount = CConfig::get('db.prefix') . Accounts::model()->getTableName();
            $memberId = CAuth::getLoggedRoleId();
            $member = Members::model()->findByPk($memberId, $tableNameAccount . '.is_active = 1 AND ' . $tableNameAccount . '.is_removed = 0');
            if ($member && $record[$fieldName] == 2 && $member->id == $record['winner_member_id']) {
                $reviewCount = Reviews::model()->count(array('condition' => 'auction_id = :auction_id AND member_id = :member_id'), array(':auction_id' => $record['id'], ':member_id' => $member->id,));
                if ($reviewCount == 0) {
                    $output = '[ ' . (CHtml::tag('a', array('href' => 'reviews/add/auctionId/' . $record['id'], 'title' => A::t('auctions', 'Add Review')), A::t('auctions', 'Add Review'))) . ' ]';
                }
            }
        }

        return $output;
    }

    /**
     * Draw confirm delivered link for the shipment
     * @param string $record
     * @param array $params
     * @return string $output
     */
    public static function drawConfirmDeliveredLink($record = '', $params = array())
    {
        $output = '';

        // A::t('auctions', 'Pay')
        $fieldName = isset($params['field_name']) ? $params['field_name'] : '';

        if (!empty($record) && is_array($record) && !empty($fieldName)) {
            if (!in_array($record[$fieldName], array(0, 2))) {
                $output = '[ ' . (CHtml::tag('a', array('href' => 'shipments/confirmReceived/id/' . $record['id'], 'id' => 'confirm_received', 'title' => A::t('auctions', 'Confirm Received')), A::t('auctions', 'Confirm Received'))) . ' ]';
            }
        }

        return $output;
    }

    /**
     * Draw paid status for the auction management
     * @param string $record
     * @param array $params
     * @return string $output
     */
    public static function drawPaidStatus($record = '', $params = array())
    {
        $output = '';

        $fieldName = isset($params['field_name']) ? $params['field_name'] : '';

        if (!empty($record) && is_array($record) && !empty($fieldName)) {
            if (in_array($record[$fieldName], array(0, 3, 4))) {
                $paidStatusLabel = array(
                    '0' => '<span class="label-red label-square">' . A::t('auctions', 'Not Approved') . '</span>',
                    '1' => '<span class="label-green label-square">' . A::t('auctions', 'Paid') . '</span>',
                );
                $output = $paidStatusLabel[$record['paid_status']];
            } else {
                $output = '--';
            }
        }

        return $output;
    }

    /**
     * Draw shipping status for the auction management
     * @param string $record
     * @param array $params
     * @return string $output
     */
    public static function drawShippingStatus($record = '', $params = array())
    {
        $output = '';

        $fieldName = isset($params['field_name']) ? $params['field_name'] : '';

        if (!empty($record) && is_array($record) && !empty($fieldName)) {
            if (in_array($record['status'], array(0, 3, 4)) && $record[$fieldName]) {
                $shippingStatusLabel = array(
                    '0' => '<span class="label-gray label-square">' . A::t('auctions', 'Pending') . '</span>',
                    '1' => '<span class="label-yellow label-square">' . A::t('auctions', 'Shipped') . '</span>',
                    '2' => '<span class="label-green label-square">' . A::t('auctions', 'Received') . '</span>',
                );
                $output = $shippingStatusLabel[$record['shipping_status']];
            } else {
                $output = '--';
            }
        }

        return $output;
    }

    /**
     * Count auctions for category
     * @param int $categoryId
     * @return int
     */
    private static function _getCountAuctionForCategory($categoryId = 0)
    {
        $countAuctions = 0;

        if (!empty($categoryId)) {
            $tableNameCategories = CConfig::get('db.prefix') . Categories::model()->getTableName();
            $categories = Categories::model()->findAll($tableNameCategories . '.id = :category_id OR ' . $tableNameCategories . '.parent_id = :category_id', array(':category_id' => $categoryId));
            if (!empty($categories) && is_array($categories)) {
                $childCategoryIds = array($categoryId);
                foreach ($categories as $category) {
                    $childCategoryIds[] = $category['id'];
                }
                if (!empty($childCategoryIds) && is_array($childCategoryIds)) {
                    $tableNameAuctions = CConfig::get('db.prefix') . Auctions::model()->getTableName();
                    $condition = $tableNameAuctions . '.date_from <= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND ' . $tableNameAuctions . '.date_to >= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND status = 1 AND ' . $tableNameAuctions . '.category_id IN(' . implode(',', $childCategoryIds) . ')';
                    $countAuctions = Auctions::model()->count($condition);
                }
            }

        }

        return $countAuctions;
    }

    /**
     * Performs redirection
     * @param string $newLocation
     */
    private static function _redirect($newLocation = '')
    {
        $newLocation = A::app()->getRequest()->getBaseUrl() . $newLocation;
        // 301 - Moved Permanently
        header('location: ' . $newLocation);
        exit;
    }
}