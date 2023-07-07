<?php
/**
 * Categories model
 *
 * PUBLIC:                 	PROTECTED                	PRIVATE
 * ---------------         	---------------          	---------------
 * __construct              _relations
 *                          _afterDelete
 * STATIC:
 * model

 * 
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \A,
	\CAuth,
	\CActiveRecord,
	\CConfig,
	\CHtml,
	\CLocale;

class Categories extends CActiveRecord
{

	/** @var string */
	protected $_table = 'auction_categories';
	/** @var string */
    protected $_tableTranslation = 'auction_category_translations';
	/** @var string */
    protected $_tableAuctions = 'auctions';

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
                'category_id',
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
     * @return boolean
     */
    protected function _beforeDelete($id = 0)
    {
        $tableNameAuctions = CConfig::get('db.prefix').Auctions::model()->getTableName();
        $countAuctions = Auctions::model()->count($tableNameAuctions.'.category_id = '.$id);
        if($countAuctions > 0){
            $alert = A::t('auctions', 'You can not delete this category, it has active auctions!');
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
     * This method is invoked after deleting a record successfully
     * @param int $id
     */
    protected function _afterDelete($id = 0)
    {
        $this->_isError = false;
        // Delete category names from translation table
        if(!$this->_db->delete($this->_tableTranslation, 'category_id = :category_id', array(':category_id'=>$id))){
            $this->_isError = true;
        }
    }
}
