<?php
/**
 * Packages model
 *
 * PUBLIC:                 	PROTECTED                	PRIVATE
 * ---------------         	---------------          	---------------
 * __construct              _relations
 *                          _afterSave
 * STATIC:                  _afterDelete
 * model                    _beforeSave

 * 
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \A,
    \CActiveRecord,
    \CConfig;

class Packages extends CActiveRecord
{
	/** @var string */
	protected $_table = 'auction_packages';
    /** @var string */
    protected $_tableTranslation = 'auction_package_translations';

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
            'id' => array(
                self::HAS_MANY,
                $this->_tableTranslation,
                'package_id',
                'condition'=>CConfig::get('db.prefix').$this->_tableTranslation.".language_code = '".A::app()->getLanguage()."'",
                'joinType'=>self::INNER_JOIN,
                'fields'=>array(
                    'name',
                    'description',
                )
            ),
        );
    }

    /**
     * This method is invoked before saving a record
     * @param int $id
     * @return bool
     */
    protected function _beforeSave($id = 0)
    {
        if($this->is_default && !$this->is_active){
            $alert = A::t('auctions', 'The default entry cannot be inactive!');
        }

        if(!empty($alert)){
            $this->_error = true;
            $this->_errorMessage = $alert;
            return false;
        }else{
            return true;
        }
    }

    /**
     * This method is invoked after saving a record successfully
     * @param int $id
     * @return void
     */
    protected function _afterSave($id = 0)
    {
        $this->_isError = false;

        // if this group is default - remove default flag in all other languages
        if($this->is_default){

            if(!$this->_db->update($this->_table, array('is_default'=>0), 'id != :id', array(':id'=>$id))){
                $this->_isError = true;
            }
        }
    }

    /**
     * This method is invoked after deleting a record successfully
     * @param int $id
     * @return void
     */
    protected function _afterDelete($id = 0)
    {
        $this->_isError = false;
        // delete package from translation table
        if(!$this->_db->delete($this->_tableTranslation, 'package_id = :package_id', array(':package_id'=>$id))){
            $this->_isError = true;
        }
    }

    /**
     * Used to define custom fields
     * This method should be override
     */
    protected function _customFields()
    {
        return array(
            'ROUND(price/bids_amount, 2)' => 'price_one_bid',
        );
    }
}
