<?php
/**
 * Auction Images model
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
use \A,
	\CActiveRecord;

class AuctionImages extends CActiveRecord
{

	/** @var string */
	protected $_table = 'auction_images';

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
}
