<?php
/**
 * Watchlist model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations
 * model (static)
 * search
 *
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \A,
    \CActiveRecord,
    \CHtml,
    \Website,
    \CConfig;


class Watchlist extends CActiveRecord
{

    /** @var string */
    protected $_table = 'auction_watchlist';
    /** @var string */
    protected $_tableAuctions = 'auctions';
    /** @var string */
    protected $_tableAuctionTranslation = 'auction_translations';


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
            // '0' => array(
                // self::HAS_MANY,
                // $this->_tableAuctionTranslation,
                // 'auction_id',
                // 'parent_key'=>'auction_id',
                // 'condition'=> CConfig::get('db.prefix').$this->_tableAuctionTranslation.".language_code = '".A::app()->getLanguage()."'",
                // 'joinType'=>self::LEFT_OUTER_JOIN,
                // 'fields'=>array('name'=>'name', 'description'=> 'description')
            // ),
            // '1' => array(
                // self::HAS_MANY,
                // $this->_tableAuctions,
                // 'id',
                // 'parent_key'=>'auction_id',
                // 'joinType'=>self::LEFT_OUTER_JOIN,
                // 'fields'=>array(),
            // ),
        );
    }
}
