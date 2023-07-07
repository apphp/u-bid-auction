<?php
/**
 * Bids History model
 *
 * PUBLIC:                 	PROTECTED                	PRIVATE
 * ---------------         	---------------          	---------------
 * __construct              _relations
 *                          _customFields
 * STATIC:
 * model

 * 
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \A,
    \CActiveRecord,
    \CConfig;

class BidsHistory extends CActiveRecord
{

    /** @var string */
    protected $_table = 'auction_bids_history';
    /** @var string */
    protected $_tableAuctions = 'auctions';
    /** @var string */
    protected $_tableAuctionTranslations = 'auction_translations';
    /** @var string */
    protected $_tableAuctionTypeTranslation = 'auction_type_translations';
    /** @var string */
    protected $_tableMembers = 'auction_members';

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
        return [
            'auction_id'      => [
                self::HAS_MANY,
                $this->_tableAuctionTranslations,
                'auction_id',
                'condition' => CConfig::get('db.prefix').$this->_tableAuctionTranslations.".language_code = '".A::app()->getLanguage()."'",
                'joinType'  => self::INNER_JOIN,
                'fields'    => [
                    'name' => 'auction_name',
                ]
            ],
            'auction_type_id' => [
                self::HAS_MANY,
                $this->_tableAuctionTypeTranslation,
                'auction_type_id',
                'condition' => CConfig::get('db.prefix').$this->_tableAuctionTypeTranslation.".language_code = '".A::app()->getLanguage()
                    ."'",
                'joinType'  => self::INNER_JOIN,
                'fields'    => [
                    'name' => 'auction_type',
                ]
            ],
            'member_id'       => [
                self::HAS_MANY,
                $this->_tableMembers,
                'id',
                'condition' => "",
                'joinType'  => self::LEFT_OUTER_JOIN,
                'fields'    => [
                    'last_name'  => 'last_name',
                    'first_name' => 'first_name',
                ]
            ],
        ];
    }

    /**
     * Used to define custom fields
     */
    protected function _customFields()
    {
        return [
            "IF(last_name = '' AND first_name = '', 'without account', CONCAT(first_name, ' ', last_name))" => 'member_name',
        ];
    }

    /**
     * Get Current Winner Id
     *
     * @param  int  $auctionId
     * @return int $memberId
     */
    public function getCurrentWinnerId($auctionId = 0)
    {
        if (!$auctionId) {
            return null;
        }

        $memberId             = 0;
        $bidsHistoryTableName = CConfig::get('db.prefix').$this->getTableName();
        $bidsHistory          = $this->find(
            ['condition' => $bidsHistoryTableName.'.auction_id = :auction_id', 'orderBy' => 'created_at DESC'],
            [':auction_id' => $auctionId]
        );
        if ($bidsHistory) {
            $memberId = $bidsHistory->member_id;
        }

        return $memberId;
    }

    /**
     * Get last auction bid
     *
     * @param  int  $auctionId
     * @return object
     */
    public function getLastBid($auctionId = 0)
    {
        if (!$auctionId) {
            return null;
        }

        $bidsHistoryTableName = CConfig::get('db.prefix').$this->getTableName();
        $bidsHistory          = $this->find(
            ['condition' => $bidsHistoryTableName.'.auction_id = :auctionId', 'orderBy' => 'created_at DESC'],
            [':auctionId' => $auctionId]
        );

        return $bidsHistory;
    }
}