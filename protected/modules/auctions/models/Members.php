<?php
/**
 * Members model
 *
 * PUBLIC:                 	PROTECTED                	PRIVATE
 * ---------------         	---------------          	---------------
 * __construct              _relations
 * isLogin                  _beforeSave
 *                          _afterSave
 *                          _afterDelete
 * STATIC:
 * model
 * getErrorField
 * isLogin
 *
 *
 *
 */

namespace Modules\Auctions\Models;

// Framework
use \A,
	\CConfig,
    \CAuth,
    \CHash,
	\CActiveRecord;

// App
use \Website,
    \LocalTime,
    \Accounts,
    \ModulesSettings;

class Members extends CActiveRecord
{

	/** @var string */
	protected $_table = 'auction_members';
    /** @var string */
    protected $_role = 'member';
    /** @var string */
    protected $_tableAccounts = 'accounts';
    /** @var bool */
    private $_sendApprovalEmail = false;
    /** @var bool */
    private $_sendActivationEmail = false;
    /** @var bool */
    private $_sendPasswordChangedEmail = false;

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
     * @return array
     */
    protected function _relations()
    {
        return array(
            'account_id' => array(
                self::HAS_ONE,
                $this->_tableAccounts,
                'id',
                'condition'=> CConfig::get('db.prefix').$this->_tableAccounts.".role = '".$this->_role."'",
                'joinType'=>self::INNER_JOIN,
                'fields'=>array(
                    'role'=>'role',
                    'email'=>'email',
                    'avatar'=>'avatar',
                    'language_code'=>'language_code',
                    'username'=>'username',
                    'created_at'=>'created_at',
                    'created_ip'=>'created_ip',
                    'last_visited_at'=>'last_visited_at',
                    'last_visited_ip'=>'last_visited_ip',
                    'notifications'=>'notifications',
                    'notifications_changed_at'=>'notifications_changed_at',
                    'password_changed_at'=>'password_changed_at',
                    'is_active'=>'is_active',
                    'is_removed'=>'is_removed',
                    'comments'=>'comments'
                )
            ),
        );
    }

    /**
     * Checked is login by member
     */
    public static function isLogin()
    {
        return CAuth::isLoggedInAs('member');
    }

    /**
     * This method is invoked before saving a record
     * @param string $id
     * @return bool
     */
    protected function _beforeSave($id = 0)
    {
        $cRequest       = A::app()->getRequest();
        $username       = $cRequest->getPost('username');
        $password       = $cRequest->getPost('password');
        $avatar         = $cRequest->getPost('avatar');
        $salt           = $cRequest->getPost('salt');
        $email          = $cRequest->getPost('email');
        $languageCode   = $cRequest->getPost('language_code', 'alpha', A::app()->getLanguage());
        $isActive       = (int)$cRequest->getPost('is_active', 'int', 1);
        $isRemoved      = (int)$cRequest->getPost('is_removed', 'int');
        $comments       = $cRequest->getPost('comments');
        $ipAddress      = $cRequest->getUserHostAddress();
        $notifications  = (int)$cRequest->getPost('notifications', 'int');
        if($notifications !== 0 && $notifications !== 1) $notifications = 0;
        $approvalType   = ModulesSettings::model()->param('auctions', 'member_approval_type');
        $changePassword = ModulesSettings::model()->param('auctions', 'change_member_password');
        $username  = empty($username) && $this->isColumnExists('username') ? $this->username : $username;
        $email     = empty($email) && $this->isColumnExists('email') ? $this->email : $email;
        $avatar    = $this->isColumnExists('avatar') && $this->avatar != '' ? $this->avatar : $avatar;
        if($id > 0){
            $account = Accounts::model()->findByPk((int)$this->account_id);
            $salt = $account->salt;
        }else{
            $salt = !empty($salt) ? $salt : CHash::getRandomString(33);
        }

        if(CConfig::get('password.encryption')){
            $encryptAlgorithm = CConfig::get('password.encryptAlgorithm');
            $encryptSalt = $salt;
            if(!empty($password)){
                $password = CHash::create($encryptAlgorithm, $password, $encryptSalt);
            }
        }

        // Check if member with the same email already exists
        $memberExists = $this->_db->select('SELECT * FROM '.CConfig::get('db.prefix').'accounts WHERE role = :role AND email = :email AND id != :id', array(':role'=>$this->_role, ':email'=>$email, ':id'=>$this->account_id));
        if(!empty($email) && !empty($memberExists)){
            $this->_error = true;
            $this->_errorMessage = A::t('auctions', 'Member with such email already exists!');
            $this->_errorField = 'email';
            return false;
        }

        if($id > 0){
            // UPDATE MEMBER
            // Update accounts table
            if(CAuth::isLoggedInAsAdmin()){
                //Do not update the status if the account is disabled
                if($account->is_removed && $isActive && $isRemoved){
                    $this->_error = true;
                    $this->_errorMessage = A::t('auctions', 'Status changing error! Account has been disabled!');
                    $this->_errorField = 'is_active';
                    return false;
                }
                $account->comments      = $comments;

                // Approval by admin (previously created by member)
                if($approvalType == 'by_admin' && $account->registration_code != '' && $isActive){
                    $account->registration_code = '';
                    $this->_sendApprovalEmail = true;
                }

                // password changed by admin
                if($changePassword && $account->password != $password && !empty($password) && $isActive){
                    $this->_sendPasswordChangedEmail = true;
                }
            }
            $account->is_active     = $isActive;
            $account->is_removed    = $isRemoved;
            // Logical deleting
            if($isRemoved == 1){
                $account->is_active = 0;
            }

            // Password was changed
            if($password !== ''){
                $account->password_changed_at = LocalTime::currentDateTime();
            }

            if(!empty($password) && $changePassword){
                $account->password = $password;
            }elseif(!empty($password) && !$changePassword){
                $this->_error = true;
                $this->_errorMessage = A::t('auctions', 'The admin can not change the password for the member.');
                $this->_errorField = 'password';
                return false;
            }
            if(!empty($salt) && $changePassword) $account->salt = $salt;
            if(!empty($avatar)) $account->avatar = $avatar;
            $account->email = $email;
            $account->language_code = $languageCode;
            if($account->notifications != $notifications){
                $account->notifications = $notifications;
                $account->notifications_changed_at = LocalTime::currentDateTime();
            }

            if($account->save()){
                // update existing member
                if($this->birth_date == '') $this->birth_date = null;
                return true;
            }
            return false;
        }else{
            // NEW ACCOUNT
            // Check if member with the same username already exists
            if($this->_db->select('SELECT * FROM '.CConfig::get('db.prefix').'accounts WHERE role = :role AND username = :username', array(':role'=>$this->_role, ':username'=>$username))){
                $this->_error = true;
                $this->_errorMessage = A::t('auctions', 'Member with such username already exists!');
                $this->_errorField = 'username';
                return false;
            }

            // Insert new member
            if($accountId = $this->_db->insert($this->_tableAccounts, array(
                'role'              => $this->_role,
                'username'          => $username,
                'password'          => $password,
                'salt'              => $salt,
                'email'             => $email,
                'language_code'     => $languageCode,
                'created_at'        => LocalTime::currentDateTime(),
                'created_ip'        => $ipAddress,
                'notifications'     => $notifications,
                'registration_code' => '',
                'is_active'         => $isActive,
                'comments'          => $comments
            ))){
                $this->account_id = $accountId;
                if($this->birth_date == '') $this->birth_date = null;

                // Account activated by admin (previously created by admin)
                if(CAuth::isLoggedInAsAdmin() && $isActive){
                    $this->_sendActivationEmail = true;
                }
                return true;
            }
            return false;
        }
    }

    /**
     * This method is invoked after saving a record successfully
     * @param string $pk
     * @return void
     * You may override this method
     */
    protected function _afterSave($pk = '')
    {
        $cRequest       = A::app()->getRequest();
        $email          = $cRequest->getPost('email');
        $firstName      = $cRequest->getPost('first_name');
        $lastName       = $cRequest->getPost('last_name');
        $username = $cRequest->getPost('username', '', $this->username);
        $password       = $cRequest->getPost('password');
        $languageCode   = $cRequest->getPost('language_code');
        $isActive       = (int)$cRequest->getPost('is_active', 'int');

        // send email to member on creating new account by admininstrator (if member is active)
        if($this->_sendActivationEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'member_new_account_created_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName,
                    '{USERNAME}' => $username,
                    '{PASSWORD}' => $password,
                )
            );
        }

        // send email to patient on admin changes patient password
        if($this->_sendPasswordChangedEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'member_password_changed_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName,
                    '{USERNAME}' => $username,
                    '{PASSWORD}' => $password,
                )
            );
        }

        // send email to member on admin approval
        if($this->_sendApprovalEmail){
            $result = Website::sendEmailByTemplate(
                $email,
                'member_account_approved_by_admin',
                $languageCode,
                array(
                    '{FIRST_NAME}' => $firstName,
                    '{LAST_NAME}' => $lastName
                )
            );
        }
    }

    /**
     * This method is invoked after deleting a record successfully
     * @param string $pk
     * @return void
     */
    protected function _afterDelete($pk = '')
    {
        // delete record from accounts table
        if(false === $this->_db->delete($this->_tableAccounts, 'id = '.(int)$this->account_id)){
            $this->_error = true;
            $this->_errorMessage = A::t('auctions', 'An error occurred while deleting member account! Please try again later.');
        }
    }

    /**
     * Used to define custom fields
     */
    protected function _customFields()
    {
        return array("CONCAT(first_name, ' ', last_name)" => 'full_name');
    }

    /**
     * Returns error field
     * @return boolean
     */
    public function getErrorField()
    {
        return $this->_errorField;
    }

     /**
     * Return Bids Amount For Member
     * @return double
     */
    public static function getBidsAmount()
    {
        $bidsAmount = 0;

        $memberId = CAuth::getLoggedRoleId();
        $member = self::model()->findByPk($memberId);

        if($member){
            $bidsAmount = $member->bids_amount;
        }

        return $bidsAmount;
    }

    /**
     * Set Bids Amount For Member
     * @param double $bidsAmount
     * @return bool
     */
    public static function setBidsAmount($bidsAmount = 0.00)
    {
        $memberId = CAuth::getLoggedRoleId();
        $forceSave = APPHP_MODE == 'demo' ? true : false;
        $updateMember = self::model()->update(
            $memberId,
            array(
                'bids_amount' => $bidsAmount
            ),
            true,
            $forceSave
        );

        return $updateMember;
    }


}
