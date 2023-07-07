<?php
/**
 * Taxes model
 *
 * PUBLIC:                  PROTECTED                  PRIVATE
 * ---------------          ---------------            ---------------
 * __construct              _relations
 *                          _afterDelete
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \CActiveRecord;

class Taxes extends CActiveRecord
{

    /** @var string */
    protected $_table = 'auction_taxes';
    /** @var string */
    protected $_tableCoutries = 'auction_tax_countries';

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
        return array();
    }

    /**
     * This method is invoked after deleting a record successfully
     * @param string $pk
     * @return void
     */
    protected function _afterDelete($pk = '')
    {
        $this->_isError = false;
        // delete country names from translation table
        if(false === $this->_db->delete($this->_tableCoutries, 'tax_id = :tax_id', array(':tax_id'=>$pk))){
            $this->_isError = true;
        }
    }
}
