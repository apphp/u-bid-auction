<?php
/**
 * ShipmentAddress model
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

use Modules\Auctions\Models\Auctions;

// Framework
use \A,
    \CActiveRecord;

use \Countries;

class ShipmentAddress extends CActiveRecord
{

    /** @var string */
    protected $_table = 'auction_shipment_address';

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
     * This method is invoked after saving a record successfully
     * @param int $id
     */
    protected function _afterSave($id = 0)
    {
        $this->_isError = false;

        // If this page is home page - remove this flag from all other pages
        if($this->is_default){
            if(!$this->_db->update($this->_table, array('is_default'=>0), 'id != :id', array(':id'=>$id))){
                $this->_isError = true;
            }
        }
    }
}
