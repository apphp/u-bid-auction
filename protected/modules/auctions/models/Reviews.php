<?php
/**
 * Reviews model
 *
 * PUBLIC:                 	PROTECTED                	PRIVATE
 * ---------------         	---------------          	---------------
 * __construct
 *
 * STATIC:
 * model

 * 
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \CActiveRecord;

class Reviews extends CActiveRecord
{

	/** @var string */
	protected $_table = 'auction_reviews';
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
        return array(
            'member_id' => array(
                self::HAS_MANY,
                $this->_tableMembers,
                'id',
                'condition' => "",
                'joinType' => self::LEFT_OUTER_JOIN,
                'fields' => array(
                    'last_name' => 'last_name',
                    'first_name' => 'first_name',
                )
            ),
        );
	}
}
