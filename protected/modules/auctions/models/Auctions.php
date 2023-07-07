<?php
/**
 * Auctions model
 *
 * PUBLIC:                    PROTECTED                    PRIVATE
 * ---------------            ---------------            ---------------
 * __construct              _relations
 *                          _afterDelete
 * STATIC:                  _beforeSave
 * model
 *
 *
 */

namespace Modules\Auctions\Models;

use \Modules\Setup\Models\Setup;

// Framework
use \A,
    \CActiveRecord,
    \CArray,
    \CAuth,
    \CConfig,
    \CDatabase,
    \CFile,
    \CHash,
    \CLocale,
    \CLoader,
    \ModulesSettings;


// Application
use \Bootstrap,
    \LocalTime,
    \PaymentProviders,
    \PaymentProvider,
    \Website;

class Auctions extends CActiveRecord
{

    /** @var string */
    protected $_table = 'auctions';
    /** @var string */
    protected $_tableTranslation = 'auction_translations';
    /** @var string */
    protected $_tableAuctionImages = 'auction_images';
    /** @var string */
    protected $_tableCategories = 'auction_categories';
    /** @var string */
    protected $_tableCategoriesTranslation = 'auction_category_translations';
    /** @var string */
    protected $_tableAuctionTypes = 'auction_types';
    /** @var string */
    protected $_tableAuctionTypesTranslation = 'auction_type_translations';
    /** @var string */
    protected $_tableMembers = 'auction_members';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the specified AR class
     * @return Auctions
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
                'auction_id',
                'condition' => CConfig::get('db.prefix') . $this->_tableTranslation . ".language_code = '" . A::app()->getLanguage() . "'",
                'joinType' => self::INNER_JOIN,
                'fields' => array(
                    'name' => 'auction_name',
                    'description',
                    'short_description',
                )
            ),
            'winner_member_id' => array(
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

    /**
     * This method is invoked before saving a record (after validation, if any)
     * @param int $pk
     * @return boolean
     */
    protected function _beforeSave($pk = 0)
    {
        // Set current bid for new auctions
        if (empty($pk) && $this->current_bid == 0 && $this->start_price != '') {
            $this->current_bid = $this->start_price;
        }

        return true;
    }

    /**
     * This method is invoked after deleting a record successfully
     * @param int $id
     */
    protected function _afterDelete($id = 0)
    {
        $this->_isError = false;
        // Delete auction names from translation table
        if (!$this->_db->delete($this->_tableTranslation, 'auction_id = :auction_id', array(':auction_id' => $id))) {
            $this->_isError = true;
        }
        // Delete auction images from images table and server
        $fullTableNameAuctionImage = CConfig::get('db.prefix') . $this->_tableAuctionImages;
        $sql = 'SELECT * FROM ' . $fullTableNameAuctionImage . ' WHERE ' . $fullTableNameAuctionImage . '.auction_id = ' . $id;
        $images = $this->_db->select($sql);
        foreach ($images as $image) {
            $iconPath = 'assets/modules/auctions/images/auctionimages/' . $image['image_file'];
            $iconThumbPath = 'assets/modules/auctions/images/auctionimages/thumbs/' . $image['image_file_thumb'];
            // Delete auction images from images table
            if ($this->_db->delete($this->_tableAuctionImages, 'id = :id', array(':id' => $image['id']))) {
                // Delete Image
                if (!CFile::deleteFile($iconPath) || !CFile::deleteFile($iconThumbPath)) {
                    $this->_isError = true;
                }
            } else {
                $this->_isError = true;
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


    /**
     * Returns the table name value
     * @param bool $usePrefix
     * @return string
     */
    public function getTableTranslationsName($usePrefix = false)
    {
        return ($usePrefix ? $this->_dbPrefix : '') . $this->_tableTranslation;
    }

    /**
     * Closed auctions and send emails. Update Auctions date in the demo mode
     */
    public static function cron()
    {
        $resultSendEmail = array();
        $currentDateTime = LocalTime::currentDateTime('Y-m-d H:i:s');
        $sendEmailAdminAuctionClosed = (int)ModulesSettings::model()->param('auctions', 'send_email_admin_auction_closed');
        $sendEmailMemberAuctionClosed = (int)ModulesSettings::model()->param('auctions', 'send_email_member_auction_closed');
        $tableNameAuctions = CConfig::get('db.prefix') . Auctions::model()->getTableName();
        $bidsHistoryTableName = CConfig::get('db.prefix') . BidsHistory::model()->getTableName();

        // Search Closed Auctions
        $closedAuctions = Auctions::model()->findAll(array('condition' => $tableNameAuctions . '.date_to <= "' . $currentDateTime . '" AND status = 1'));
        if (!empty($closedAuctions) && is_array($closedAuctions)) {
            foreach ($closedAuctions as $auction) {
                // Search latest bid in the bids history
                $bidHistory = BidsHistory::model()->find(array('condition' => $bidsHistoryTableName . '.auction_id = :auction_id', 'orderBy' => 'created_at DESC'), array(':auction_id' => $auction['id']));
                // If found  latest bid -> close auction and create new shipment
                if ($bidHistory) {
                    $updateAuction = Auctions::model()->updateByPk($auction['id'], array(
                        'winner_member_id' => $bidHistory->member_id,
                        'date_to' => $currentDateTime,
                        'won_date' => $currentDateTime,
                        'status' => 3,
                        'paid_status' => 0,
                        'paid_status_changed' => $currentDateTime,
                        'status_changed' => $currentDateTime,
                    ));

                    if ($updateAuction) {
                        $member = Members::model()->findByPk($bidHistory->member_id);
                        if ($member) {
                            $params = array(
                                '{MEMBER_NAME}' => $member->full_name,
                                '{AUCTION_NAME}' => $auction['auction_name'],
                            );

                            if ($sendEmailAdminAuctionClosed) {
                                $resultSendEmail['admin'][$auction['id']] = Website::sendEmailByTemplate(
                                    Bootstrap::init()->getSettings()->general_email,
                                    'admin_auction_closed',
                                    A::app()->getLanguage(),
                                    $params
                                );
                            }
                            if ($sendEmailMemberAuctionClosed) {
                                $resultSendEmail['member'][$auction['id']] = Website::sendEmailByTemplate(
                                    $member->email,
                                    'member_auction_closed',
                                    $member->language_code,
                                    $params
                                );
                            }
                        }
                    }
                    // If not found  latest bid -> change auction status to inactive
                } else {
                    $updateAuction = Auctions::model()->updateByPk($auction['id'], array(
                        'status' => 2,
                        'status_changed' => $currentDateTime,
                    ));

                    if ($updateAuction) {
                        if ($sendEmailAdminAuctionClosed) {
                            $resultSendEmail['admin'][$auction['id']] = Website::sendEmailByTemplate(
                                Bootstrap::init()->getSettings()->general_email,
                                'admin_auction_suspended',
                                A::app()->getLanguage(),
                                array(
                                    '{AUCTION_NAME}' => $auction['auction_name'],
                                )
                            );
                        }
                    }
                }
            }
        }

        // Update Auctions date in the 'demo' or 'hidden' mode
		if (APPHP_MODE == 'demo' || APPHP_MODE == 'hidden') {
            $sqlFile = APPHP_PATH . '/protected/modules/auctions/data/demo.install.mysql.sql';
            if (file_exists($sqlFile)) {
                // Read file
                $sqlDump = file($sqlFile);
                if (!empty($sqlDump)) {
                    // Replace placeholders
                    $sqlDump = str_ireplace('<DB_PREFIX>', CConfig::get('db.prefix'), $sqlDump);
                    $sqlDump = str_ireplace('<CURRENT_DATE>', date('Y-m-d', time() + (date('I', time()) ? 3600 : 0)), $sqlDump);
                    $sqlDump = str_ireplace('<CURRENT_DATETIME>', date('Y-m-d H:i:s', time() + (date('I', time()) ? 3600 : 0)), $sqlDump);
                    $sqlDump = str_ireplace('<SITE_BO_URL>', CConfig::get('defaultBackendDirectory') . '/', $sqlDump);
                    // Run the sql
                    if (!empty($sqlDump) && is_array($sqlDump)) {
                        $query = '';
                        foreach ($sqlDump as $sqlLine) {
                            $tsl = trim(utf8_decode($sqlLine));
                            if (($sqlLine != '') && (substr($tsl, 0, 2) != '--') && (substr($tsl, 0, 1) != '?') && (substr($tsl, 0, 1) != '#')) {
                                $query .= $sqlLine;
                                if (preg_match("/;\s*$/", $sqlLine)) {
                                    if (strlen(trim($query)) > 5) {
                                        $model = new CDatabase();
                                        $result = $model->customExec($query, array(), true);
                                    }
                                    $query = '';
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
