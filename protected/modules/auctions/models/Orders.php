<?php
/**
 * Orders model
 *
 * PUBLIC:                    PROTECTED                    PRIVATE
 * ---------------            ---------------            ---------------
 * __construct              _relations
 *                          _customFields
 * STATIC:
 * model
 * paymentHandler
 *
 *
 */

namespace Modules\Auctions\Models;

use Modules\Auctions\Components\AuctionsComponent;
use Modules\Auctions\Models\Members;
// Framework
use \A,
    \CActiveRecord,
    \CAuth,
    \CConfig,
    \CHash,
    \CLocale;

//app
use \Accounts,
    \Bootstrap,
    \LocalTime,
    \ModulesSettings,
    \PaymentProviders,
    \Website;

class Orders extends CActiveRecord
{

    /** @var string */
    protected $_table = 'auction_orders';
    /** @var string */
    protected $_tableMembers = 'auction_members';
    /** @var string */
    protected $_tablePayment = 'payment_providers';
    /** @var string */
    protected $_tableCurrencies = 'currencies';
    /** @var string */
    protected $_tablePackages = 'auction_packages';
    /** @var string */
    protected $_tablePackageTranslation = 'auction_package_translations';
    /** @var int */
    protected $_oldStatus = 0;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     */
    public static function model()
    {
        return parent::model(__CLASS__);
    }

    /**
     * Defines relations between different tables in database and current $_table
     */
    protected function _relations()
    {
        return array(
            'member_id' => array(
                self::HAS_MANY,
                $this->_tableMembers,
                'id',
                'condition' => "",
                'joinType' => self::LEFT_OUTER_JOIN,
                'fields' => array(
                    'first_name',
                    'last_name',
                )
            ),
            'currency' => array(
                self::HAS_MANY,
                $this->_tableCurrencies,
                'code',
                'joinType' => self::LEFT_OUTER_JOIN,
                'fields' => array(
                    'symbol',
                    'symbol_place'
                )
            ),
        );
    }

    /**
     * This method is invoked before saving a record
     * @param int $pk
     * @return bool
     */
    protected function _beforeSave($pk = 0)
    {
        $order = new Orders();
        $order->findByPk($pk);
        if ($order) {
            $this->_oldStatus = $order->status;
        }
        return true;
    }

    /**
     * This method is invoked after saving a record successfully
     * @param int $id
     */
    protected function _afterSave($id = 0)
    {
        if (!$this->isNewRecord()) {
            //If status = 2 (Paid) update bids amount for member account
            if ($this->status == 2 && $this->_oldStatus != $this->status) {
                $tableNameAccount = CConfig::get('db.prefix') . Accounts::model()->getTableName();
                $member = Members::model()->findByPk($this->member_id, $tableNameAccount . '.is_active = 1 AND is_removed = 0');
                $package = Packages::model()->findByPk($this->package_id);

                $status = A::t('auctions', 'Paid');
                $dateTimeFormat = Bootstrap::init()->getSettings()->datetime_format;
                $languageCode = $member->language_code ? $member->language_code : A::app()->getLanguage();

                if (!empty($member) && !empty($package)) {
                    $member->bids_amount += $package->bids_amount;
                    if ($member->save()) {
                        // Send email
                        Website::sendEmailByTemplate(
                            $member->email,
                            'member_paid_order',
                            $languageCode,
                            array(
                                '{FIRST_NAME}' => $member->first_name,
                                '{LAST_NAME}' => $member->last_name,
                                '{ORDER_NUMBER}' => $this->order_number,
                                '{PACKAGE}' => $package->name,
                                '{STATUS}' => $status,
                                '{DATE_CREATED}' => CLocale::date($dateTimeFormat, strtotime($this->created_at), true),
                                '{STATUS_CHANGED}' => CLocale::date($dateTimeFormat, strtotime($this->status_changed), true),
                            )
                        );
                    }
                }
                $this->payment_date = date('Y-m-d H:i:s');
                $this->save();
            }
        }
    }

    /**
     * Used to define custom fields
     */
    protected function _customFields()
    {
        $numberFormat = Bootstrap::init()->getSettings('number_format');

        $sqlFormatTotalPrice = 'FORMAT(total_price, 2)';
        if ($numberFormat == 'european') {
            // Fix to correct format in European format
            $sqlFormatTotalPrice = "REPLACE(REPLACE(REPLACE(" . $sqlFormatTotalPrice . ", ',', 'x'), '.', ','), 'x', '.')";
        }

        return array(
            "IF(last_name = '' AND first_name = '', 'without account', CONCAT(first_name, ' ', last_name))" => 'member_name',
            "IF(symbol_place = 'before', CONCAT(symbol, " . $sqlFormatTotalPrice . "), CONCAT(" . $sqlFormatTotalPrice . ", symbol))" => 'format_price',
            "package_id" => 'bids_amount_id'
        );
    }

    /**
     * Payment handler
     * @param string $type
     * @param array $orderInfo
     * @return bool
     * */
    public function paymentHandler($type, $orderInfo = array())
    {
        if (($type == 'completed' && empty($orderInfo)) || !is_array($orderInfo)) {
            $this->_errorMessage = A::t('app', 'Input incorrect parameters');
            return false;
        }

        $return = true;
        $orderNumber = !empty($orderInfo['order_number']) ? $orderInfo['order_number'] : '';
        $transactionNumber = !empty($orderInfo['transaction_number']) ? $orderInfo['transaction_number'] : '';

        // Status Pending
        if ($type == 'pending') {
            // Get Order Number
            $lastPackageOrderNumber = A::app()->getSession()->get('lastPackageOrderNumber');
            if (empty($orderNumber) && empty($lastPackageOrderNumber)) {
                $accountId = CAuth::getLoggedId();
                $member = Members::model()->find('account_id = :account_id', array(':account_id' => $accountId));
                if (!$member) {
                    $this->_errorMessage = A::t('auctions', 'You are not logged in to your account');
                    return false;
                }
                $order = $this->find('member_id = :member_id AND status = 0', array(':member_id' => $member->id));
            } elseif (!empty($lastPackageOrderNumber)) {
                $order = $this->find('order_number = :order_number', array(':order_number' => $lastPackageOrderNumber));
            } else {
                $order = $this->find('order_number = :order_number', array(':order_number' => $orderNumber));
            }

            if ($order) {
                $order->status = 1;
            } else {
                $this->_errorMessage = A::t('auctions', 'Order cannot be found in the database');
                $return = false;
            }
        } elseif ($type == 'completed' && !empty($orderNumber)) {
            // Status Completed
            $order = $this->find('order_number = :order_number', array(':order_number' => $orderNumber));
            if ($order) {
                $order->status = 2;
                $order->transaction_number = $transactionNumber;

                // Fore The Auction Orders
                if ($order->order_type == 1) {

                    $auction = AuctionsComponent::checkRecordAccess($order->auction_id, 'Auctions', true, 'auctions/manage');
                    $resultSendEmail = array();
                    $currentDateTime = LocalTime::currentDateTime('Y-m-d H:i:s');
                    $sendEmailAdminAuctionClosed = (int)ModulesSettings::model()->param('auctions', 'send_email_admin_auction_closed');
                    $sendEmailMemberAuctionClosed = (int)ModulesSettings::model()->param('auctions', 'send_email_member_auction_closed');

                    if ($auction->status == 1) {// 1 - Active
                        $auction->status = 3;
                        $auction->paid_status = 1;// Paid
                        $auction->date_to = $currentDateTime;
                        $auction->won_date = $currentDateTime;
                        $auction->winner_member_id = $order->member_id;
                        $auction->paid_status_changed = $currentDateTime;
                        $auction->status_changed = CLocale::date('Y-m-d H:i:s');
                        if ($auction->save()) {
                            $shipment = new Shipments();

                            $shipment->auction_id = $auction->id;
                            $shipment->member_id = $order->member_id;
                            $shipment->tracking_number = '';
                            $shipment->created_at = $currentDateTime;
                            $shipment->shipping_status = 0;// Pending
                            $shipment->last_update_shipping_status = $currentDateTime;
                            $shipment->shipping_comment = '';

                            if ($shipment->save()) {
                                $member = Members::model()->findByPk($order->member_id);
                                if ($member) {
                                    $params = array(
                                        '{MEMBER_NAME}' => $member->full_name,
                                        '{AUCTION_NAME}' => $auction->auction_name,
                                    );

                                    if ($sendEmailAdminAuctionClosed) {
                                        $resultSendEmail['admin'][$auction->id] = Website::sendEmailByTemplate(
                                            Bootstrap::init()->getSettings()->general_email,
                                            'admin_auction_buy_now',
                                            A::app()->getLanguage(),
                                            $params
                                        );
                                    }
                                    if ($sendEmailMemberAuctionClosed) {
                                        $resultSendEmail['member'][$auction->id] = Website::sendEmailByTemplate(
                                            $member->email,
                                            'member_auction_buy_now',
                                            $member->language_code,
                                            $params
                                        );
                                    }
                                }
                            }
                        }
                    } elseif ($auction->status == 3) {// 3 - Won
                        $auction->paid_status = 1;// Paid
                        $auction->paid_status_changed = $currentDateTime;
                        if ($auction->save()) {
                            $shipment = new Shipments();

                            $shipment->auction_id = $auction->id;
                            $shipment->member_id = $order->member_id;
                            $shipment->tracking_number = '';
                            $shipment->created_at = $currentDateTime;
                            $shipment->shipping_status = 0;// Pending
                            $shipment->last_update_shipping_status = $currentDateTime;
                            $shipment->shipping_comment = '';

                            if ($shipment->save()) {
                                $member = Members::model()->findByPk($order->member_id);
                                if ($member) {
                                    $params = array(
                                        '{MEMBER_NAME}' => $member->full_name,
                                        '{AUCTION_NAME}' => $auction->auction_name,
                                    );

                                    if ($sendEmailAdminAuctionClosed) {
                                        $resultSendEmail['admin'][$auction->id] = Website::sendEmailByTemplate(
                                            Bootstrap::init()->getSettings()->general_email,
                                            'admin_auction_paid',
                                            A::app()->getLanguage(),
                                            $params
                                        );
                                    }
                                    if ($sendEmailMemberAuctionClosed) {
                                        $resultSendEmail['member'][$auction->id] = Website::sendEmailByTemplate(
                                            $member->email,
                                            'member_auction_paid',
                                            $member->language_code,
                                            $params
                                        );
                                    }
                                }
                            }
                        }
                    } else {
                        $this->_errorMessage = A::t('auctions', 'The auction is closed!');
                        $return = false;
                    }
                }
            } else {
                $this->_errorMessage = A::t('auctions', 'Order cannot be found in the database');
                $return = false;
            }
        } elseif ($type == 'canceled' && !empty($orderNumber)) {
            // Status Rejected
            $order = $this->find('order_number = :order_number', array(':order_number' => $orderNumber));
            if ($order) {
                $order->status = 5;
            } else {
                $this->_errorMessage = A::t('auctions', 'Order cannot be found in the database');
                $return = false;
            }
        } else {
            $this->_errorMessage = A::t('app', 'Input incorrect parameters');
            $return = false;
        }

        if ($return) {
            $order->status_changed = date('Y-m-d H:i:s');

            $tableNameAccount = CConfig::get('db.prefix') . Accounts::model()->getTableName();
            $member = Members::model()->findByPk($order->member_id, $tableNameAccount . '.is_active = 1');
            $providersList = array();
            $paymentProviders = PaymentProviders::model()->findAll('is_active = 1');
            if (!empty($paymentProviders) && is_array($paymentProviders)) {
                foreach ($paymentProviders as $onePayment) {
                    $providersList[$onePayment['id']] = $onePayment['name'];
                }
            }

            $statusList = array(
                '0' => A::t('auctions', 'Preparing'),
                '1' => A::t('auctions', 'Pending'),
                '2' => A::t('auctions', 'Paid'),
                '3' => A::t('auctions', 'Completed'),
                '4' => A::t('auctions', 'Refunded'),
                '5' => A::t('auctions', 'Canceled')
            );
            $status = $statusList[$order->status];
            $paymentType = $providersList[$order->payment_id];
            $dateTimeFormat = Bootstrap::init()->getSettings()->datetime_format;

            if (!empty($member)) {
                // Send email
                $emailResult = Website::sendEmailByTemplate(
                    $member->email,
                    'member_success_order',
                    $member->language_code,
                    array(
                        '{FIRST_NAME}' => $member->first_name,
                        '{LAST_NAME}' => $member->last_name,
                        '{ORDER_NUMBER}' => $order->order_number,
                        '{STATUS}' => $status,
                        '{DATE_CREATED}' => CLocale::date($dateTimeFormat, strtotime($order->created_at), true),
                        '{DATE_PAYMENT}' => $order->payment_date == null ? A::t('auctions', 'Not paid yet', array(), null, $member->language_code) : CLocale::date($dateTimeFormat, strtotime($order->payment_date)),
                        '{PAYMENT_TYPE}' => $paymentType,
                        '{CURRENCY}' => $order->currency,
                        '{PRICE}' => $order->total_price
                    )
                );

                $order->email_sent = $emailResult ? 1 : 0;
            }

            // Send notification to admin about order
            Website::sendEmailByTemplate(
                Bootstrap::init()->getSettings()->general_email,
                'member_success_order_for_admin',
                A::app()->getLanguage(),
                array(
                    '{FIRST_NAME}' => $member->first_name,
                    '{LAST_NAME}' => $member->last_name,
                    '{USERNAME}' => $member->username,
                    '{ORDER_NUMBER}' => $order->order_number,
                    '{STATUS}' => $status,
                    '{DATE_CREATED}' => CLocale::date($dateTimeFormat, strtotime($order->created_at), true),
                    '{DATE_PAYMENT}' => $order->payment_date == null ? A::t('auctions', 'Not paid yet', array(), null, $member->language_code) : CLocale::date($dateTimeFormat, strtotime($order->payment_date)),
                    '{PAYMENT_TYPE}' => $paymentType,
                    '{CURRENCY}' => $order->currency,
                    '{PRICE}' => $order->total_price
                )
            );

            $order->save();

            return true;
        } else {
            return false;
        }
    }

}

