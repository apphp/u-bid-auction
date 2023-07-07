<?php
/**
 * Shipments model
 *
 * PUBLIC:                 	PROTECTED                	PRIVATE
 * ---------------         	---------------          	---------------
 * __construct              _relations
 *                          _beforeSave
 * STATIC:                  _afterSave
 * model                    _customFields
 *
 * 
 * 
 * 
 * 
 * 
 * 
 *
 */

namespace Modules\Auctions\Models;

// Module
use Modules\Auctions\Components\AuctionsComponent;

// Framework
use \A,
	\Accounts,
	\CConfig,
	\CHtml,
	\CLocale,
	\CActiveRecord;

// CMF
use \Website,
    \Bootstrap;

class Shipments extends CActiveRecord
{

	/** @var string */
	protected $_table = 'auction_shipments';
    /** @var string */
    protected $_tableAuctions = 'auctions';
    /** @var string */
    protected $_tableAuctionTranslations = 'auction_translations';
    /** @var string */
    protected $_tableMembers = 'auction_members';
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
            'auction_id' => array(
                self::HAS_MANY,
                $this->_tableAuctionTranslations,
                'auction_id',
                'condition'=>CConfig::get('db.prefix').$this->_tableAuctionTranslations.".language_code = '".A::app()->getLanguage()."'",
                'joinType'=>self::INNER_JOIN,
                'fields'=>array(
                    'name'=>'auction_name',
                )
            ),
            'member_id' => array(
                self::HAS_MANY,
                $this->_tableMembers,
                'id',
                'condition'=>"",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array(
                    'last_name'=>'last_name',
                    'first_name'=>'first_name',
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
        $shipment = new Shipments();
        $shipment->findByPk($pk);
        if($shipment){
            $this->_oldStatus = $shipment->shipping_status;
        }
        return true;
    }

    /**
     * This method is invoked after saving a record successfully
     * @param int $id
     */
    protected function _afterSave($id = 0)
    {
        if(!$this->isNewRecord()){
            //If status = 1 (Shipped) update bids amount for member account
            if ($this->_oldStatus != $this->shipping_status) {
                // Find Member
                $tableNameAccount = CConfig::get('db.prefix') . Accounts::model()->getTableName();
                $member = Members::model()->findByPk($this->member_id, $tableNameAccount . '.is_active = 1 AND ' . $tableNameAccount . '.is_removed = 0');
                if ($member) {
                    // Find Auction
                    $auction = AuctionsComponent::checkRecordAccess($this->auction_id, 'Auctions', true, 'auctions/manage');
                    $auction->shipping_status = $this->shipping_status;
                    if ($auction->save()) {
                        if($this->shipping_status == 1){
                            // Find Order
                            $tableNameOrder = CConfig::get('db.prefix') . Orders::model()->getTableName();
                            $order = Orders::model()->find($tableNameOrder . '.member_id = :member_id AND ' . $tableNameOrder . '.auction_id = :auction_id', array(':member_id' => $member->id, ':auction_id' => $auction->id));
                            if ($order) {
                                // Find Shipping Address
                                $tableNameShipmentAddress = CConfig::get('db.prefix') . ShipmentAddress::model()->getTableName();
                                $shippingAddress = ShipmentAddress::model()->findByPk($order->shipment_address_id, $tableNameShipmentAddress . '.member_id = :member_id', array(':member_id' => $member->id));
                                if ($shippingAddress) {
                                    $dateFormat = Bootstrap::init()->getSettings()->date_format;
                                    $languageCode   = $member->language_code ? $member->language_code : A::app()->getLanguage();

                                    //Create table apppointment details for email message
                                    $shipmentDetails = '';
                                    $shipmentDetails .= CHtml::openTag('table');
                                    if ($auction->auction_name) {
                                        $shipmentDetails .= CHtml::openTag('tr');
                                        $shipmentDetails .= CHtml::tag('td', '', A::t('auctions', 'Auction Name'));
                                        $shipmentDetails .= CHtml::tag('td', '', $auction->auction_name);
                                        $shipmentDetails .= CHtml::closeTag('tr');
                                    }
                                    if ($this->carrier) {
                                        $shipmentDetails .= CHtml::openTag('tr');
                                        $shipmentDetails .= CHtml::tag('td', '', A::t('auctions', 'Carrier'));
                                        $shipmentDetails .= CHtml::tag('td', '', $this->carrier);
                                        $shipmentDetails .= CHtml::closeTag('tr');
                                    }
                                    if ($this->tracking_number) {
                                        $shipmentDetails .= CHtml::openTag('tr');
                                        $shipmentDetails .= CHtml::tag('td', '', A::t('auctions', 'Tracking Number'));
                                        $shipmentDetails .= CHtml::tag('td', '', $this->tracking_number);
                                        $shipmentDetails .= CHtml::closeTag('tr');
                                    }
                                    if ($this->shipped_date) {
                                        $shipmentDetails .= CHtml::openTag('tr');
                                        $shipmentDetails .= CHtml::tag('td', '', A::t('auctions', 'Shipped Date'));
                                        $shipmentDetails .= CHtml::tag('td', '', CLocale::date($dateFormat, $this->shipped_date) );
                                        $shipmentDetails .= CHtml::closeTag('tr');
                                    }
                                    if ($this->shipping_comment) {
                                        $shipmentDetails .= CHtml::openTag('tr');
                                        $shipmentDetails .= CHtml::tag('td', '', A::t('auctions', 'Description'));
                                        $shipmentDetails .= CHtml::openTag('td');
                                        $shipmentDetails .= CHtml::tag('p', '', $this->shipping_comment);
                                        $shipmentDetails .= CHtml::closeTag('td');
                                        $shipmentDetails .= CHtml::closeTag('tr');
                                    }
                                    $shipmentDetails .= CHtml::closeTag('table');

                                    $params = array(
                                        '{MEMBER_NAME}' => $member->full_name,
                                        '{AUCTION_NAME}' => $auction->auction_name,
                                        '{SHIPMENT_DETAILS}' => $shipmentDetails,
                                    );
                                    // Send email
                                    Website::sendEmailByTemplate(
                                        $member->email,
                                        'member_auction_shipped',
                                        $languageCode,
                                        $params
                                    );

                                    Website::sendEmailByTemplate(
                                        $member->email,
                                        'admin_auction_shipped',
                                        $languageCode,
                                        $params
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Used to define custom fields
     */
    protected function _customFields()
    {
        return array(
            "IF(last_name = '' AND first_name = '', 'without account', CONCAT(first_name, ' ', last_name))" => 'member_name',
        );
    }
}
