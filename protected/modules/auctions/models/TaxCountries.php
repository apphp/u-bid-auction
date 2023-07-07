<?php
/**
 * Categories model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * ---------------         ---------------            ---------------
 * __construct             _relations
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \A,
	\CActiveRecord,
    \CConfig;

class TaxCountries extends CActiveRecord
{

    /** @var string */
    protected $_table = 'auction_tax_countries';
    /** @var string */
    protected $_tableCountryTranslation = 'country_translations';

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
            'country_code' => array(
                self::HAS_MANY,
                $this->_tableCountryTranslation,
                'country_code',
                'condition'=> CConfig::get('db.prefix').$this->_tableCountryTranslation.".language_code = '".A::app()->getLanguage()."'",
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('name'=>'country_name')
            ),
        );
    }
}
