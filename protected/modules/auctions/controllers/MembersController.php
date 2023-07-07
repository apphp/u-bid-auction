<?php
/**
 * Members controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                              PRIVATE
 * -----------                          ------------------
 * __construct                          _getLangList
 * indexAction                          _getParamsFormRegistration
 * manageAction                         _getGenders
 * addAction                            _getStates
 * editAction                           _getAddressesCounts
 * changeStatusAction                   _logout
 * deleteAction                         _outputAjax
 * bidsHistoryAction                    _outputJson
 * ordersHistoryAction
 * loginAction
 * logoutAction
 * registrationAction
 * confirmRegistrationAction
 * termsConditionsAction
 * restorePasswordAction
 * shipmentAddressAction
 * addShipmentAddressAction
 * editShipmentAddressAction
 * deleteShipmentAddressAction
 * dashboardAction
 * myAccountAction
 * editMyAccountAction
 * removeAccountAction
 * ajaxRegistrationAction
 *
 *
 *
 *
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
    \Modules\Auctions\Models\Members,
    \Modules\Auctions\Models\Orders,
    \Modules\Auctions\Models\ShipmentAddress;

// Framework
use \Modules,
    \ModulesSettings,
    \CAuth,
    \CConfig,
    \CHash,
    \CController,
    \CDatabase,
    \Website,
    \CValidator,
    \CWidget;

// Application
use \Accounts,
    \Bootstrap,
    \Languages,
    \States,
    \A;

class MembersController extends CController
{
    private $_checkBruteforce;
    private $_redirectDelay;
    private $_badLogins;
    private $_settings;
    private $_backendPath = '';

    /**
     * Class default constructor
     */
    public function __construct()
    {

        parent::__construct();

        // Get BackEnd path
        $this->_backendPath = Website::getBackendPath();

        // Block access if the module is not installed
        if (!Modules::model()->isInstalled('auctions')) {
            if (CAuth::isLoggedInAsAdmin()) {
                $this->redirect($this->_backendPath . 'modules/index');
            } else {
                $this->redirect(Website::getDefaultPage());
            }
        }

        if (CAuth::isLoggedInAsAdmin()) {
            // set meta tags according to active auctions
            Website::setMetaTags(array('title' => A::t('auctions', 'Members Management')));
            // set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
            $this->_view->backendPath = $this->_backendPath;

            $this->_view->tabs = AuctionsComponent::prepareTab('members');
        }

        $this->_settings = Bootstrap::init()->getSettings();
        $this->_cSession = A::app()->getSession();
        $this->_view->dateFormat = $this->_settings->date_format;
        $this->_view->timeFormat = $this->_settings->time_format;
        $this->_view->dateTimeFormat = $this->_settings->datetime_format;
        $this->_view->langList = $this->_getLangList();
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('members/manage');
    }

    /**
     * Manage action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'member', 'members/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }
        $this->_view->arrAddresses = $this->_getAddressesCounts();
        $this->_view->render('members/backend/manage');
    }

    /**
     * Add new action handler
     * @return void
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', 'member', 'members/manage');

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
            $this->_view->defaultCountryCode = isset($countriesAndDefaultCountry['default_country_code']) ? $countriesAndDefaultCountry['default_country_code'] : '';
        }


        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $this->_view->defaultCountryCode;
            $this->_view->stateCode = '';
        }

        // prepare salt
        $this->_view->salt = '';
        if (A::app()->getRequest()->getPost('password') != '') {
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
        }

        $this->_view->paramsFormRegistration = $this->_getParamsFormRegistration();
        $this->_view->genders = $this->_getGenders();

        $this->_view->render('members/backend/add');
    }

    /**
     * Edit member action handler
     * @param int $id
     * @return void
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'member', 'members/manage');

        $member = AuctionsComponent::checkRecordAccess($id, 'Members', true, 'members/manage');

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();

        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $member->country_code;
            $this->_view->stateCode = $member->state;
        }

        // prepare salt
        $this->_view->salt = '';
        if (A::app()->getRequest()->getPost('password') != '') {
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
            A::app()->getRequest()->setPost('salt', $this->_view->salt);
        }

        $this->_view->paramsFormRegistration = $this->_getParamsFormRegistration();
        $this->_view->genders = $this->_getGenders();
        $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
        $this->_view->changePassword = ModulesSettings::model()->param('auctions', 'change_member_password');
        $this->_view->id = $member->id;
        $this->_view->render('members/backend/edit');
    }

    /**
     * Change status action handler
     * @param int $id
     * @param int $page
     * @return void
     */
    public function changeStatusAction($id = 0, $page = 1)
    {
        // Block access if admin has no active privilege to change ban lists
        Website::prepareBackendAction('edit', 'member', 'members/manage');
        $member = AuctionsComponent::checkRecordAccess($id, 'Members', true, 'members/manage');
        $account = AuctionsComponent::checkRecordAccess($member->account_id, 'TaxCountries', true, 'members/manage');

        $allowUpdateRecord = true;
        $updateStatus = false;
        $updateRegistrationCode = false;

        if ($account->registration_code != '' && $account->is_active == false) {
            $emailResult = Website::sendEmailByTemplate(
                $member->email,
                'member_account_approved_by_admin',
                $member->language_code,
                array(
                    '{FIRST_NAME}' => $member->first_name,
                    '{LAST_NAME}' => $member->last_name
                )
            );
            $updateRegistrationCode = Accounts::model()->updateByPk($member->account_id, array('registration_code' => ''));
            if (!$updateRegistrationCode) {
                $allowUpdateRecord = false;
            }
        }

        //Do not update the status if the account is disabled
        if ($account->is_removed) {
            $allowUpdateRecord = false;
        }

        if ($allowUpdateRecord) {
            $updateStatus = Accounts::model()->updateByPk($member->account_id, array('is_active' => ($member->is_active == true ? false : true)));
        }

        if ($allowUpdateRecord && $updateStatus) {
            $alert = A::t('app', 'Status has been successfully changed!');
            $alertType = 'success';
        } elseif ($account->is_removed) {
            $alert = A::t('auctions', 'Status changing error! Account has been disabled!');
            $alertType = 'error';
        } else {
            $alert = ((APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error'));
            $alertType ((APPHP_MODE == 'demo') ? 'warning' : 'error');
        }
        if (!$emailResult && $updateRegistrationCode) {
            $alert .= (!empty($alert) ? '<br/>' : '') . A::t('auctions', 'Account has been successfully approved, but email not sent! Please try again later.');
        }

        A::app()->getSession()->setFlash('alert', $alert);
        A::app()->getSession()->setFlash('alertType', $alertType);

        $this->redirect('members/manage' . (!empty($page) ? '?page=' . (int)$page : 1));
    }

    /**
     * Delete action handler
     * @param int $id
     * @param int $page
     * @return void
     */
    public function deleteAction($id = 0, $page = 1)
    {
        Website::prepareBackendAction('delete', 'member', 'members/manage');
        $member = AuctionsComponent::checkRecordAccess($id, 'Members', true, 'members/manage');

        $alert = '';
        $alertType = '';

        if ($member->delete()) {
            if ($member->getError()) {
                $alert = $member->getErrorMessage();
                $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
                $alertType = 'warning';
            } else {
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            }

            $result = Website::sendEmailByTemplate(
                $member->email,
                'member_account_removed_by_admin',
                $member->language_code,
                array(
                    '{FIRST_NAME}' => $member->first_name,
                    '{LAST_NAME}' => $member->last_name,
                )
            );
        } else {
            if (APPHP_MODE == 'demo') {
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            } else {
                $alert = $member->getError() ? $member->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if (!empty($alert)) {
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('members/manage' . (!empty($page) ? '?page=' . (int)$page : 1));
    }

    /**
     * Bids History action handler
     * @param int $memberId
     * @return void
     */
    public function bidsHistoryAction($memberId = 0)
    {
        Website::prepareBackendAction('manage', 'member', 'members/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/manage');

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        $this->_view->id = $member->id;
        $this->_view->render('members/backend/bidsHistory');
    }

    /**
     * Orders History action handler
     * @param int $memberId
     * @return void
     */
    public function ordersHistoryAction($memberId = 0)
    {
        Website::prepareBackendAction('manage', 'member', 'members/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/manage');

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }
        $this->_view->arrStatus = array('0' => A::t('auctions', 'Preparing'), '1' => A::t('auctions', 'Pending'), '2' => A::t('auctions', 'Paid'), '3' => A::t('auctions', 'Completed'), '4' => A::t('auctions', 'Refunded'));
        $this->_view->id = $member->id;
        $this->_view->render('members/backend/ordersHistory');
    }


    /*   FRONTEND ACTIONS   */

    /**
     * Orders History action handler
     * @param string $type
     * @return void
     */
    public function loginAction($type = '')
    {
        // Redirect logged in members
        CAuth::handleLoggedIn('members/dashboard', 'member');
        Website::setFrontend();

        // Social login
        // if(!empty($type)){
        //     $lowType = strtolower($type);

        //     $config = array();
        //     $config['returnUrl'] = 'members/login';
        //     $config['model'] = 'Members';

        //     SocialLogin::config($config);
        //     SocialLogin::login($lowType);
        // }

        // $this->_view->buttons = SocialLogin::drawButtons(array(
        //         'facebook'=>'members/login/type/facebook',
        //         'twitter'=>'members/login/type/twitter',
        //         'google'=>'members/login/type/google')
        // );

        $this->_view->allowRememberMe = ModulesSettings::model()->param('auctions', 'member_allow_remember_me');
        $this->_view->allowRegistration = ModulesSettings::model()->param('auctions', 'member_allow_registration');
        $this->_view->allowResetPassword = ModulesSettings::model()->param('auctions', 'member_allow_restore_password');

        //#000
        $this->_checkBruteforce = CConfig::get('validation.bruteforce.enable');
        $this->_redirectDelay = (int)CConfig::get('validation.bruteforce.redirectDelay', 3);
        $this->_badLogins = (int)CConfig::get('validation.bruteforce.badLogins', 5);
        $alert = '';
        $alertType = '';
        $errors = array();
        $cRequest = A::app()->getRequest();

        $member = new Accounts();

        // Check if access is blocked to this IP address
        $ipBanned = Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors);
        if ($ipBanned) {
            // do nothing
            $this->_view->actionMessage = CWidget::create('CMessage', array($errors['alertType'], $errors['alert']));
        } else {
            // -------------------------------------------------
            // Perform auto-login "remember me"
            // --------------------------------------------------
            if (!CAuth::isLoggedIn()) {
                if ($this->_view->allowRememberMe) {
					parse_str(A::app()->getCookie()->get('memberAuth'), $output);
					if (!empty($output['usr']) && !empty($output['hash'])) {
						$username = CHash::decrypt($output['usr'], CConfig::get('password.hashKey'));
						$password = $output['hash'];

                        // Check if access is blocked to this username
                        $usernameBanned = Website::checkBan('username', $username);
                        if ($usernameBanned) {
                            // do nothing
                        } elseif ($member->login($username, $password, 'member', true, true)) {
                            // Save member role ID
                            $memb = Members::model()->find('account_id = :account_id', array(':account_id' => (int)$member->id));
                            if ($memb) {
                                A::app()->getSession()->set('loggedRoleId', $memb->id);
                            }
                            $this->redirect('members/dashboard');
                        }
                    }
                }
            }

            $this->_view->username = $cRequest->getPost('user_name');
            $this->_view->password = $cRequest->getPost('password');
            $this->_view->remember = $cRequest->getPost('remember');
            $alert = A::app()->getSession()->getFlash('alert');
            $alertType = A::app()->getSession()->getFlash('alertType');
            if (empty($alert)) {
                $alert = A::t('auctions', 'Member Login Message');
                $alertType = 'info';
            }

            // -------------------------------------------------
            // Handle form submission
            // --------------------------------------------------
            if ($cRequest->getPost('act') == 'send') {
                // perform login form validation
                $fields = array();
                $fields['user_name'] = array('title' => A::t('auctions', 'Username'), 'validation' => array('required' => true, 'type' => 'any', 'minLength' => 4, 'maxLength' => 32));
                $fields['password'] = array('title' => A::t('auctions', 'Password'), 'validation' => array('required' => true, 'type' => 'any', 'minLength' => 4, 'maxLength' => 25));
                if ($this->_view->allowRememberMe) $fields['remember'] = array('title' => A::t('auctions', 'Remember Me'), 'validation' => array('required' => false, 'type' => 'set', 'source' => array(0, 1)));
                $result = CWidget::create('CFormValidation', array(
                    'fields' => $fields
                ));

                if ($result['error']) {
                    $alert = $result['errorMessage'];
                    $alertType = 'validation';
                    $this->_view->errorField = $result['errorField'];
                } else {
                    // Check if access is blocked to this username
                    $usernameBanned = Website::checkBan('username', $this->_view->username, $errors);
                    if ($usernameBanned) {
                        // do nothing
                        $alert = $errors['alert'];
                        $alertType = $errors['alertType'];
                    } else {
                        if (CAuth::getLoggedId() == '') {
                            $lastVisitedPage = Website::getLastVisitedPage();
                        }

                        if ($member->login($this->_view->username, $this->_view->password, 'member', false, ($this->_view->allowRememberMe && $this->_view->remember))) {
                            if ($this->_view->allowRememberMe && $this->_view->remember) {
                                // Username may be decoded
                                $usernameHash = CHash::encrypt($this->_view->username, CConfig::get('password.hashKey'));
                                // Password cannot be decoded, so we save ID + username + salt + HTTP_USER_AGENT
                                $httpUserAgent = A::app()->getRequest()->getUserAgent();
                                $passwordHash = CHash::create(CConfig::get('password.encryptAlgorithm'), $member->id . $member->username . $member->getPasswordSalt() . $httpUserAgent);
                                A::app()->getCookie()->set('memberAuth', 'usr=' . $usernameHash . '&hash=' . $passwordHash, (time() + 3600 * 24 * 14));
                            }
                            //#001 clean login attempts counter
                            if ($this->_checkBruteforce) {
                                A::app()->getSession()->remove('memberLoginAttempts');
                                A::app()->getCookie()->remove('memberLoginAttemptsAuth');
                            }

                            // Save member role ID
                            $memb = Members::model()->find('account_id = :account_id', array(':account_id' => (int)$member->id));
                            if ($memb) {
                                A::app()->getSession()->set('loggedRoleId', $memb->id);
                            }

                            if (!empty($lastVisitedPage) && !preg_match('/(login|registration|Home\/index|index\/index)/i', $lastVisitedPage)) {
                                $this->redirect($lastVisitedPage, true);
                            }
                            $this->redirect('members/dashboard');
                        } else {
                            $alert = $member->getErrorDescription();
                            $alertType = 'error';
                            $this->_view->errorField = 'username';
                        }
                    }
                }

                if (!empty($alert)) {
                    $this->_view->username = $cRequest->getPost('user_name');
                    $this->_view->password = $cRequest->getPost('password');
                    if ($this->_view->allowRememberMe) $this->_view->remember = $cRequest->getPost('remember', 'string');
                    $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));

                    //#002 increment login attempts counter
                    if ($this->_checkBruteforce && $this->_view->username != '' && $this->_view->password != '') {
                        $logAttempts = A::app()->getSession()->get('memberLoginAttempts', 1);
                        if ($logAttempts < $this->_badLogins) {
                            A::app()->getSession()->set('memberLoginAttempts', $logAttempts + 1);
                        } else {
                            A::app()->getCookie()->set('memberLoginAttemptsAuth', md5(uniqid()));
                            sleep($this->_redirectDelay);
                            $this->redirect('members/login');
                        }
                    }
                }
            } else {
                //#003 validate login attempts coockie
                if ($this->_checkBruteforce) {
                    $logAttempts = A::app()->getSession()->get('memberLoginAttempts', 0);
                    $logAttemptsAuthCookie = A::app()->getCookie()->get('memberLoginAttemptsAuth');
                    $logAttemptsAuthPost = $cRequest->getPost('memberLoginAttemptsAuth');
                    if ($logAttempts >= $this->_badLogins) {
                        if ($logAttemptsAuthCookie != '' && $logAttemptsAuthCookie == $logAttemptsAuthPost) {
                            A::app()->getSession()->remove('memberLoginAttempts');
                            A::app()->getCookie()->remove('memberLoginAttemptsAuth');
                            $this->redirect('members/login');
                        }
                    } elseif ($logAttempts == 0 && !empty($logAttemptsAuthPost)) {
                        // If the lifetime of the session ended, and confirm the button has not been pressed
                        A::app()->getCookie()->remove('memberLoginAttemptsAuth');
                        $this->redirect('members/login');
                    }
                }
                $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
            }
        }
        // Logged out messages
        if (A::app()->getSession()->hasFlash('msgLoggedOut')) {
            $this->_view->actionMessage = A::app()->getSession()->getFlash('msgLoggedOut');
        }

        A::app()->view->setLayout('no_columns');
        $this->_view->render('members/login');
    }

    /**
     * Member logout action handler
     * @return void
     */
    public function logoutAction()
    {
        if (CAuth::isLoggedInAs('member')) {
            $this->_logout();
            $this->_cSession->startSession();
            $this->_cSession->setFlash('msgLoggedOut', CWidget::create('CMessage', array('info', A::t('auctions', 'Member Logout Message'))));
        }

        $this->redirect('members/login');
    }

    /**
     * Member registration action handler
     * @return void
     */
    public function registrationAction()
    {
        // redirect logged in members
        CAuth::handleLoggedIn('members/dashboard', 'member');

        // check if action allowed
        if (!ModulesSettings::model()->param('auctions', 'member_allow_registration')) {
            $this->redirect(Website::getDefaultPage());
        }

        // set frontend mode
        Website::setFrontend();

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
            $this->_view->defaultCountryCode = isset($countriesAndDefaultCountry['default_country_code']) ? $countriesAndDefaultCountry['default_country_code'] : '';
        }
        $approvalType = ModulesSettings::model()->param('auctions', 'member_approval_type');

        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $this->_view->defaultCountryCode;
            $this->_view->stateCode = '';
        }

        if ($approvalType == 'by_admin') {
            $messageSuccess = A::t('auctions', 'Account successfully created! Admin approval required.');
            $messageInfo = A::t('auctions', 'Admin approve registration? Click here to proceed.', array('{url}' => 'members/login'));
        } elseif ($approvalType == 'by_email') {
            $messageSuccess = A::t('auctions', 'Account successfully created! Email confirmation required.');
            $messageInfo = A::t('auctions', 'Already confirmed your registration? Click here to proceed.', array('{url}' => 'members/login'));
        } else {
            $messageSuccess = A::t('auctions', 'Account successfully created!');
            $messageInfo = A::t('auctions', 'Click here to proceed.', array('{url}' => 'members/login'));
        }

        if (APPHP_MODE == 'demo') {
            $messageError = A::t('core', 'This operation is blocked in Demo Mode!');
        } else {
            $messageError = A::t('auctions', 'An error occurred while registration process! Please try again later.');
        }

        $this->_view->paramsFormRegistration = $this->_getParamsFormRegistration();
        $this->_view->messageSuccess = $messageSuccess;
        $this->_view->messageInfo = $messageInfo;
        $this->_view->messageError = $messageError;
        $this->_view->langList = $this->_getLangList();
        $this->_view->genders = $this->_getGenders();
        $this->_view->verificationCaptcha = ModulesSettings::model()->param('auctions', 'member_verification_allow');

        A::app()->view->setLayout('no_columns');
        $this->_view->render('members/registration');
    }

    /**
     * Member confirm registration action handler
     * @param string $code
     * @return void
     */
    public function confirmRegistrationAction($code)
    {
        // redirect logged in directory
        CAuth::handleLoggedIn('members/dashboard', 'member');

        // set frontend mode
        Website::setFrontend();

        if ($member = Accounts::model()->find('is_active = 0 AND registration_code = :code', array(':code' => $code))) {
            $member->is_active = 1;
            $member->registration_code = '';
            if ($member->save()) {
                $alertType = 'success';
                $alert = A::t('auctions', 'Account registration confirmed');
            } else {
                if (APPHP_MODE == 'demo') {
                    $alertType = 'warning';
                    $alert = CDatabase::init()->getErrorMessage();
                } else {
                    $alertType = 'error';
                    $alert = A::t('auctions', 'Account registration error');
                }
            }
        } else {
            $alertType = 'warning';
            $alert = A::t('auctions', 'Account registration wrong code');
        }

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
        }

        A::app()->view->setLayout('no_columns');
        $this->_view->render('members/confirmRegistration');
    }

    /**
     * Show page Terms & Conditions
     * @return void
     */
    public function termsConditionsAction()
    {
        // set frontend settings
        Website::setFrontend();

        $this->_view->render('members/termsconditions');
    }

    /**
     * Member restore password action handler
     * @return void
     */
    public function restorePasswordAction()
    {
        // Redirect logged in members
        CAuth::handleLoggedIn('members/dashboard', 'member');

        // Check if action allowed
        if (!ModulesSettings::model()->param('auctions', 'member_allow_restore_password')) {
            $this->redirect(Website::getDefaultPage());
        }

        // Set frontend mode
        Website::setFrontend();

        $errors = array();
        $cRequest = A::app()->getRequest();
        $actionMessage = '';

        if ($cRequest->getPost('act') == 'send') {

            // Check if access is blocked to this IP address
            $ipBanned = Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors);
            if ($ipBanned) {
                $alert = $errors['alert'];
                $alertType = $errors['alertType'];
            } else {
                $email = $cRequest->getPost('email');
                $alertType = '';
                $alert = '';

                // Check if access is blocked to this email
                $emailBanned = Website::checkBan('email_address', $email, $errors);
                if ($emailBanned) {
                    // do nothing
                    $alert = $errors['alert'];
                    $alertType = $errors['alertType'];
                } else {
                    if (empty($email)) {
                        $alertType = 'validation';
                        $alert = A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}' => A::t('auctions', 'Email')));
                    } elseif (!empty($email) && !CValidator::isEmail($email)) {
                        $alertType = 'validation';
                        $alert = A::t('auctions', 'You must provide a valid email address!');
                    } elseif (APPHP_MODE == 'demo') {
                        $alertType = 'warning';
                        $alert = A::t('core', 'This operation is blocked in Demo Mode!');
                    } else {
                        $account = Accounts::model()->find('role = :role AND email = :email', array(':role' => 'member', ':email' => $email));
                        if (empty($account)) {
                            $alertType = 'error';
                            $alert = A::t('auctions', 'Sorry, but we were not able to find a member with that login information!');
                        } elseif (!$account->is_active || $account->is_removed) {
                            $alertType = 'error';
                            $alert = A::t('app', 'Login Inactive Message');
                        } else {
                            $username = $account->username;
                            $preferedLang = $account->language_code;
                            // generate new password
                            if (CConfig::get('password.encryption')) {
                                $password = CHash::getRandomString(8);
                                $account->password = CHash::create(CConfig::get('password.encryptAlgorithm'), $password, $account->salt);
                                if (!$account->save()) {
                                    $alertType = 'error';
                                    if (APPHP_MODE == 'debug') {
                                        $alert = Accounts::model()->getErrorMessage();
                                    } else {
                                        $alert = A::t('app', 'An error occurred while password recovery process! Please try again later.');
                                    }
                                }
                            } else {
                                $password = $account->password;
                            }

                            if (!$alert) {
                                $result = Website::sendEmailByTemplate(
                                    $email,
                                    'member_password_forgotten',
                                    $preferedLang,
                                    array(
                                        '{USERNAME}' => $username,
                                        '{PASSWORD}' => $password
                                    )
                                );
                                if ($result) {
                                    $alertType = 'success';
                                    $alert = A::t('app', 'A new password has been sent! Check your e-mail address linked to the account for the confirmation link, including the spam or junk folder.');
                                } else {
                                    $alertType = 'error';
                                    $alert = A::t('app', 'An error occurred while password recovery process! Please try again later.');
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($alert)) {
                $actionMessage = CWidget::create('CMessage', array($alertType, $alert));
            }
        }

        $this->_view->actionMessage = $actionMessage;

        A::app()->view->setLayout('no_columns');
        $this->_view->render('members/restorePassword');
    }

    /**
     * Shipment Address action handler
     * @param int $memberId
     * @return void
     */
    public function shipmentAddressAction($memberId = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'member', 'backend/dashboard');

        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $actionMessage = '';

        if (!empty($alert)) {
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button' => true)));
        }

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
            $this->_view->defaultCountryCode = isset($countriesAndDefaultCountry['default_country_code']) ? $countriesAndDefaultCountry['default_country_code'] : '';
        }

        $this->_view->memberId = $memberId;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->render('members/backend/shipmentAddress');
    }

    /**
     * Add address (for admin) action handler
     * @param int $memberId
     * @return void
     */
    public function addShipmentAddressAction($memberId = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'member', 'backend/dashboard');

        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/manage');

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
            $this->_view->defaultCountryCode = isset($countriesAndDefaultCountry['default_country_code']) ? $countriesAndDefaultCountry['default_country_code'] : '';
        }


        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $this->_view->defaultCountryCode;
            $this->_view->stateCode = '';
        }

        $this->_view->memberId = $member->id;
        $this->_view->render('members/backend/addShipmentAddress');
    }

    /**
     * Edit address (for admin) action handler
     * @param int $shipmentId
     * @param int $memberId
     * @return void
     */
    public function editShipmentAddressAction($shipmentId = 0, $memberId = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'members', 'backend/dashboard');

        $address = AuctionsComponent::checkRecordAccess($shipmentId, 'ShipmentAddress', true, 'members/manage');
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/manage');
        if ($member->id !== $address->member_id) {
            $this->redirect('members/manage');
        }

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();

        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $member->country_code;
            $this->_view->stateCode = $member->state;
        }

        $this->_view->address = $address;
        $this->_view->memberId = $member->id;
        $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
        $this->_view->render('members/backend/editShipmentAddress');
    }

    /**
     * Delete address (for admin) action handler
     * @param int $shipmentId
     * @param int $memberId
     * @return void
     */
    public function deleteShipmentAddressAction($shipmentId = 0, $memberId = 0)
    {
        // Set backend mode
        Website::setBackend();
        Website::prepareBackendAction('manage', 'members', 'backend/dashboard');

        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/manage');
        $address = AuctionsComponent::checkRecordAccess($shipmentId, 'ShipmentAddress', true, 'members/manage');
        if ($member->id !== $address->member_id) {
            $this->redirect('members/manage');
        }

        $alert = '';
        $alertType = '';

        if ($address->is_default == 1) {
            $alert = A::t('auctions', 'You cannot delete the default address');
            $alertType = 'warning';
        } else {
            if ($address->delete()) {
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            } else {
                if (APPHP_MODE == 'demo') {
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                } else {
                    $alert = A::t('app', 'Delete Error Message');
                    $alertType = 'error';
                }
            }
        }

        if (!empty($alert)) {
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('members/shipmentAddress/memberId/' . $address->member_id);
    }


    /**
     * Dashboard action handler
     * @return void
     */
    public function dashboardAction()
    {
        // block access to this controller for not-logged members
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'Dashboard')));

        A::app()->view->setLayout('no_columns');
        $this->_view->render('members/dashboard');
    }

    /**
     * My Account Action Handler
     * @return void
     */
    public function myAccountAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Account')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();

        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/logout');
        $countriesAndDefaultCountry = AuctionsComponent::getCountries();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = !empty($alertType) ? CWidget::create('CMessage', array($alertType, $alert)) : '';


        $this->_view->states = $this->_getStates();
        $this->_view->genders = $this->_getGenders();
        $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
        $this->_view->member = $member;
        $this->_view->id = $member->id;
        $this->_view->render('members/myAccount');
    }


    /**
     * My Account Action Handler
     * @return void
     */
    public function editMyAccountAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'Edit Account')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();

        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/logout');
        $countriesAndDefaultCountry = AuctionsComponent::getCountries();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = !empty($alertType) ? CWidget::create('CMessage', array($alertType, $alert)) : '';

        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $member->country_code;
            $this->_view->stateCode = $member->state;
        }

        // prepare salt
        $this->_view->salt = '';
        if (A::app()->getRequest()->getPost('password') != '') {
            $this->_view->salt = CConfig::get('password.encryptSalt') ? CHash::salt() : '';
            A::app()->getRequest()->setPost('salt', $this->_view->salt);
        }

        $this->_view->paramsFormRegistration = $this->_getParamsFormRegistration();
        $this->_view->genders = $this->_getGenders();
        $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
        $this->_view->member = $member;
        $this->_view->id = $member->id;
        $this->_view->render('members/editMyAccount');
    }

    /**
     * Member remove account action handler
     * @return void
     */
    public function removeAccountAction()
    {
        // block access to this controller for not-logged members
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'Remove Account')));
        // set frontend settings
        Website::setFrontend();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/logout');
        $alertType = '';
        $alert = '';
        $this->_view->accountRemoved = false;

        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            if ($cRequest->getPost('act') != 'send') {
                $this->redirect('members/myAccount');
            } elseif (APPHP_MODE == 'demo') {
                $alertType = 'warning';
                $alert = A::t('core', 'This operation is blocked in Demo Mode!');
            } else {
                // add removing account here
                $removalType = ModulesSettings::model()->param('auctions', 'member_removal_type');
                $this->_view->accountRemoved = true;
                if ($removalType == 'logical') {
                    A::app()->getRequest()->setPost('is_removed', true);
                    A::app()->getRequest()->setPost('is_active', false);
                    if (!$member->save()) {
                        $this->_view->accountRemoved = false;
                    }
                } elseif ($removalType == 'physical') {
                    if (!$member->delete()) {
                        $this->_view->accountRemoved = false;
                    }
                }

                if ($this->_view->accountRemoved) {
                    $alertType = 'success';
                    $alert = A::t('auctions', 'Your account has been successfully removed!');

                    $result = Website::sendEmailByTemplate(
                        CAuth::getLoggedEmail(),
                        'member_account_removed_by_member',
                        CAuth::getLoggedLang(),
                        array(
                            '{FIRST_NAME}' => $member->first_name,
                            '{LAST_NAME}' => $member->last_name,
                        )
                    );

                    $this->_logout();
                } else {
                    $alertType = 'error';
                    $alert = A::t('auctions', 'An error occurred while deleting your account! Please try again later.');
                }
            }
        }

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
        }

        A::app()->view->setLayout('no_columns');
        $this->_view->render('members/removeAccount');
    }

    /**
     * My Shipment Address action handler
     * @return void
     */
    public function myShipmentAddressAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Shipment Address')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $actionMessage = '';

        if (!empty($alert)) {
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button' => true)));
        }

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
            $this->_view->defaultCountryCode = isset($countriesAndDefaultCountry['default_country_code']) ? $countriesAndDefaultCountry['default_country_code'] : '';
        }

        $this->_view->memberId = $member->id;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->render('members/myShipmentAddress');
    }

    /**
     * Add address (for member) action handler
     * @return void
     */
    public function addMyShipmentAddressAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'Add Address')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
            $this->_view->defaultCountryCode = isset($countriesAndDefaultCountry['default_country_code']) ? $countriesAndDefaultCountry['default_country_code'] : '';
        }


        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $this->_view->defaultCountryCode;
            $this->_view->stateCode = '';
        }

        $this->_view->memberId = $member->id;
        $this->_view->render('members/addMyShipmentAddress');
    }

    /**
     * Edit address (for admin) action handler
     * @param int $shipmentId
     * @param int $memberId
     * @return void
     */
    public function editMyShipmentAddressAction($shipmentId = 0)
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'Edit Address')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');
        $address = AuctionsComponent::checkRecordAccess($shipmentId, 'ShipmentAddress', true, 'members/dashboard');
        if ($member->id !== $address->member_id) {
            $this->redirect('members/dashboard');
        }

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();

        $cRequest = A::app()->getRequest();
        if ($cRequest->isPostRequest()) {
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        } else {
            $this->_view->countryCode = $member->country_code;
            $this->_view->stateCode = $member->state;
        }

        $this->_view->address = $address;
        $this->_view->memberId = $member->id;
        $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
        $this->_view->render('members/editMyShipmentAddress');
    }

    /**
     * Delete address (for admin) action handler
     * @param int $shipmentId
     * @param int $memberId
     * @return void
     */
    public function deleteMyShipmentAddressAction($shipmentId = 0)
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');
        $address = AuctionsComponent::checkRecordAccess($shipmentId, 'ShipmentAddress', true, 'members/manage');
        if ($member->id !== $address->member_id) {
            $this->redirect('members/manage');
        }

        $alert = '';
        $alertType = '';

        if ($address->is_default == 1) {
            $alert = A::t('auctions', 'You cannot delete the default address');
            $alertType = 'warning';
        } else {
            if ($address->delete()) {
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            } else {
                if (APPHP_MODE == 'demo') {
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                } else {
                    $alert = A::t('app', 'Delete Error Message');
                    $alertType = 'error';
                }
            }
        }

        if (!empty($alert)) {
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('members/myShipmentAddress/memberId/' . $address->member_id);
    }


    /** AJAX **/

    /*
     * Registration Member
     * @return json
     * */
    public function ajaxRegistrationAction()
    {
        // Block access if this is not AJAX request
        $cRequest = A::app()->getRequest();
        if (!$cRequest->isAjaxRequest()) {
            $this->redirect('home/index');
        }

        $arr = array();
        $errors = array();

        // Check if access is blocked to this IP address
        $ipBanned = Website::checkBan('ip_address', $cRequest->getUserHostAddress(), $errors);
        if ($ipBanned) {
            // Do nothing
            $arr[] = '"status": "0"';
            $arr[] = '"error": "' . $errors['alert'] . '"';
        } else {
            if (APPHP_MODE == 'demo') {
                $arr[] = '"status": "0"';
            } else {
                $countriesAndDefaultCountry = AuctionsComponent::getCountries();
                $countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
                $verificationCaptcha = ModulesSettings::model()->param('auctions', 'member_verification_allow');
                $paramsFormRegistration = $this->_getParamsFormRegistration();


                // Perform registration form validation
                $fields = array();
                $fields['first_name'] = array('title' => A::t('auctions', 'First Name'), 'validation' => array('required' => $paramsFormRegistration['first_name']['required'], 'type' => 'text', 'maxLength' => 50));
                $fields['last_name'] = array('title' => A::t('auctions', 'Last Name'), 'validation' => array('required' => $paramsFormRegistration['last_name']['required'], 'type' => 'text', 'maxLength' => 50));
                $fields['gender'] = array('title' => A::t('auctions', 'Gender'), 'validation' => array('required' => $paramsFormRegistration['gender']['required'], 'type' => 'set', 'source' => array_keys($this->_getGenders())));
                $fields['birth_date'] = array('title' => A::t('auctions', 'Birth Date'), 'validation' => array('required' => $paramsFormRegistration['birth_date']['required'], 'type' => 'date', 'maxLength' => 10, 'minValue' => '1900-00-00', 'maxValue' => date('Y-m-d')));
                $fields['website'] = array('title' => A::t('auctions', 'Website'), 'validation' => array('required' => $paramsFormRegistration['website']['required'], 'type' => 'text', 'maxLength' => 125));
                $fields['company'] = array('title' => A::t('auctions', 'Company'), 'validation' => array('required' => $paramsFormRegistration['company']['required'], 'type' => 'text', 'maxLength' => 125));
                $fields['phone'] = array('title' => A::t('auctions', 'Phone'), 'validation' => array('required' => $paramsFormRegistration['phone']['required'], 'type' => 'phoneString', 'maxLength' => 50));
                $fields['fax'] = array('title' => A::t('auctions', 'Fax'), 'validation' => array('required' => $paramsFormRegistration['fax']['required'], 'type' => 'phoneString', 'maxLength' => 50));
                $fields['address'] = array('title' => A::t('auctions', 'Address'), 'validation' => array('required' => $paramsFormRegistration['address']['required'], 'type' => 'text', 'maxLength' => 125));
                $fields['address_2'] = array('title' => A::t('auctions', 'Address (line 2)'), 'validation' => array('required' => $paramsFormRegistration['address_2']['required'], 'type' => 'text', 'maxLength' => 125));
                $fields['city'] = array('title' => A::t('auctions', 'City'), 'validation' => array('required' => $paramsFormRegistration['city']['required'], 'type' => 'text', 'maxLength' => 50));
                $fields['zip_code'] = array('title' => A::t('auctions', 'Zip Code'), 'validation' => array('required' => $paramsFormRegistration['zip_code']['required'], 'type' => 'zipCode', 'maxLength' => 50));
                $fields['country_code'] = array('title' => A::t('auctions', 'Country'), 'validation' => array('required' => $paramsFormRegistration['country_code']['required'], 'type' => 'set', 'source' => array_keys($countries)));
                $fields['state'] = array('title' => A::t('auctions', 'State/Province'), 'validation' => array('required' => $paramsFormRegistration['state']['required'], 'type' => 'text', 'maxLength' => 50));
                $fields['email'] = array('title' => A::t('auctions', 'Email'), 'validation' => array('required' => $paramsFormRegistration['email']['required'], 'type' => 'email', 'maxLength' => 100));
                $fields['confirm_password'] = array('title' => A::t('auctions', 'Confirm Password'), 'validation' => array('required' => $paramsFormRegistration['confirm_password']['required'], 'type' => 'confirm', 'confirmField' => 'password', 'minLength' => 6, 'maxLength' => 25));
                $fields['language_code'] = array('title' => A::t('auctions', 'Preferred Language'), 'validation' => array('required' => $paramsFormRegistration['language_code']['required'], 'type' => 'set', 'source' => array_keys($this->_getLangList())));
                $fields['username'] = array('title' => A::t('auctions', 'Username'), 'validation' => array('required' => true, 'type' => 'login', 'minLength' => 6, 'maxLength' => 32));
                $fields['password'] = array('title' => A::t('auctions', 'Password'), 'validation' => array('required' => true, 'type' => 'password', 'minLength' => 6, 'maxLength' => 25));
                $fields['notifications'] = array('title' => A::t('auctions', 'Notifications'), 'validation' => array('required' => false, 'type' => 'set', 'source' => array('1')));
                $fields['i_agree'] = array('title' => A::t('auctions', 'Terms & Conditions'), 'validation' => array('required' => true, 'type' => 'set', 'source' => array('1')));
                $captcha = $cRequest->getPost('captcha');

                $result = CWidget::create('CFormValidation', array(
                    'fields' => $fields
                ));
                if ($result['error']) {
                    $arr[] = '"status": "0"';
                    $arr[] = '"error": "' . $result['errorMessage'] . '"';
                    $arr[] = '"error_field": "' . $result['errorField'] . '"';
                } elseif ($verificationCaptcha && $captcha === '') {
                    $arr[] = '"status": "0"';
                    $arr[] = '"error_field": "captcha"';
                    $arr[] = '"error": "' . A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}' => A::t('auctions', 'Captcha'))) . '"';
                } elseif ($verificationCaptcha && $captcha != A::app()->getSession()->get('captcha')) {
                    $arr[] = '"status": "0"';
                    $arr[] = '"error_field": "captcha"';
                    $arr[] = '"error": "' . A::t('auctions', 'Sorry, the code you have entered is invalid! Please try again.') . '"';
                } else {
                    $username = $cRequest->getPost('username');
                    $password = $cRequest->getPost('password');

                    // Check if access is blocked to this username
                    $usernameBanned = Website::checkBan('username', $username, $errors);
                    if ($usernameBanned) {
                        // Do nothing
                        $arr[] = '"status": "0"';
                        $arr[] = '"error": "' . $errors['alert'] . '"';
                    } else {
                        // Password encryption
                        if (CConfig::get('password.encryption')) {
                            $encryptAlgorithm = CConfig::get('password.encryptAlgorithm');
                            $hashKey = CConfig::get('password.hashKey');
                            $passwordEncrypted = CHash::create($encryptAlgorithm, $password, $hashKey);
                        } else {
                            $passwordEncrypted = $password;
                        }


                        $member = new Members();
                        $member->first_name = !empty($cRequest->getPost('first_name')) ? $cRequest->getPost('first_name') : '';
                        $member->last_name = !empty($cRequest->getPost('last_name')) ? $cRequest->getPost('last_name') : '';
                        $member->gender = !empty($cRequest->getPost('gender')) ? $cRequest->getPost('gender') : '';
                        $member->birth_date = !empty($cRequest->getPost('birth_date')) ? $cRequest->getPost('birth_date') : '';
                        $member->website = !empty($cRequest->getPost('website')) ? $cRequest->getPost('website') : '';
                        $member->company = !empty($cRequest->getPost('company')) ? $cRequest->getPost('company') : '';
                        $member->phone = !empty($cRequest->getPost('phone')) ? $cRequest->getPost('phone') : '';
                        $member->fax = !empty($cRequest->getPost('fax')) ? $cRequest->getPost('fax') : '';
                        $member->address = !empty($cRequest->getPost('address')) ? $cRequest->getPost('address') : '';
                        $member->address_2 = !empty($cRequest->getPost('address_2')) ? $cRequest->getPost('address_2') : '';
                        $member->city = !empty($cRequest->getPost('city')) ? $cRequest->getPost('city') : '';
                        $member->zip_code = !empty($cRequest->getPost('zip_code')) ? $cRequest->getPost('zip_code') : '';
                        $member->country_code = !empty($cRequest->getPost('country_code')) ? $cRequest->getPost('country_code') : '';
                        $member->state = !empty($cRequest->getPost('state')) ? $cRequest->getPost('state') : '';

                        $accountCreated = false;
                        if ($member->save()) {
                            $member = $member->refresh();
                            $approvalType = ModulesSettings::model()->param('auctions', 'member_approval_type');

                            // update accounts table
                            $account = Accounts::model()->findByPk((int)$member->account_id);
                            if ($approvalType == 'by_admin') {
                                $account->registration_code = CHash::getRandomString(20);
                                $account->is_active = 0;
                            } elseif ($approvalType == 'by_email') {
                                $account->registration_code = CHash::getRandomString(20);
                                $account->is_active = 0;
                            } else {
                                $account->registration_code = '';
                                $account->is_active = 1;
                            }
                            if ($account->save()) {
                                $accountCreated = true;
                            }
                        }

                        if (!$accountCreated) {
                            $arr[] = '"status": "0"';
                            if (APPHP_MODE == 'demo') {
                                $arr[] = '"error": "' . A::t('core', 'This operation is blocked in Demo Mode!') . '"';
                            } else {
                                $arr[] = '"error": "' . (($member->getError() != '') ? $member->getErrorMessage() : A::t('auctions', 'An error occurred while creating member account! Please try again later.')) . '"';
                                $arr[] = '"error_field": "' . $member->getErrorField() . '"';
                            }
                        } else {

                            $firstName = $member->first_name;
                            $lastName = $member->last_name;
                            $memberEmail = $cRequest->getPost('email');
                            $emailResult = true;

                            // Send notification to admin about new registration
                            if (ModulesSettings::model()->param('auctions', 'member_new_registration_alert')) {
                                $adminLang = '';
                                if ($defaultLang = Languages::model()->find('is_default = 1')) {
                                    $adminLang = $defaultLang->code;
                                }
                                $emailResult = Website::sendEmailByTemplate(
                                    $this->_settings->general_email,
                                    'member_account_created_notify_admin',
                                    $adminLang,
                                    array(
                                        '{FIRST_NAME}' => $firstName,
                                        '{LAST_NAME}' => $lastName,
                                        '{MEMBER_EMAIL}' => $memberEmail,
                                        '{USERNAME}' => $username
                                    )
                                );
                            }

                            // Send email to member according to approval type
                            if (!empty($memberEmail)) {
                                if ($approvalType == 'by_admin') {
                                    // approval by admin
                                    $emailResult = Website::sendEmailByTemplate(
                                        $memberEmail,
                                        'member_account_created_admin_approval',
                                        A::app()->getLanguage(),
                                        array('{FIRST_NAME}' => $firstName,
                                            '{LAST_NAME}' => $lastName,
                                            '{USERNAME}' => $username,
                                            '{PASSWORD}' => $password
                                        )
                                    );
                                } elseif ($approvalType == 'by_email') {
                                    // confirmation by email
                                    $emailResult = Website::sendEmailByTemplate(
                                        $memberEmail,
                                        'member_account_created_email_confirmation',
                                        A::app()->getLanguage(),
                                        array('{FIRST_NAME}' => $firstName,
                                            '{LAST_NAME}' => $lastName,
                                            '{USERNAME}' => $username,
                                            '{PASSWORD}' => $password,
                                            '{REGISTRATION_CODE}' => $account->registration_code
                                        )
                                    );
                                } else {
                                    // auto approval
                                    $emailResult = Website::sendEmailByTemplate(
                                        $memberEmail,
                                        'member_account_created_auto_approval',
                                        A::app()->getLanguage(),
                                        array('{FIRST_NAME}' => $firstName,
                                            '{LAST_NAME}' => $lastName,
                                            '{USERNAME}' => $username,
                                            '{PASSWORD}' => $password
                                        )
                                    );
                                }
                            }

                            if (!$emailResult) {
                                $arr[] = '"status": "1"';
                                $arr[] = '"error": "' . A::t('auctions', 'Your account has been successfully created, but email not sent! Please try again later.') . '"';
                            } else {
                                $arr[] = '"status": "1"';
                            }

                        }
                    }
                }
            }
        }

        if (empty($arr)) {
            $arr = '';
        }

        $this->_outputAjax($arr, false);
    }

    /**
     * Remove product in cart
     * @return void
     */
    public function ajaxAddShipmentAddressInOrderAction()
    {
        $cRequest = A::app()->getRequest();
        if (!$cRequest->isAjaxRequest()) {
            $this->redirect(Website::getDefaultPage());
        }

        $arr = array();
        $outputFields = array();
        $alert = '';
        $html = '';
        $saveInfoShipping = array();
        $cRequest = A::app()->getRequest();
        $cSession = A::app()->getSession();
        $act = $cRequest->getPost('act');
        $orderNumber = $cRequest->getPost('order_number');
        // Prepare Countries
        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
        }


        if ($act == 'send') {
            if (CAuth::isLoggedInAs('member')) {
                $accountTableName = CConfig::get('db.prefix') . Accounts::model()->getTableName();
                $member = Members::model()->findByPk(CAuth::getLoggedRoleId(), $accountTableName.'.is_active = 1 AND ' . $accountTableName . '.is_removed = 0');
                if ($member) {
                    $orderTableName = CConfig::get('db.prefix') . Orders::model()->getTableName();
                    $order = Orders::model()->find($orderTableName.'.order_number = :order_number', array(':order_number' => $orderNumber));
                    if ($order) {
                        $isNewAddress = (bool)$cRequest->getPost('new_address');
                        if ($isNewAddress) {
                            $validation = CWidget::create('CFormValidation', array('fields' => array(
                                'first_name' => array('title' => A::t('auctions', 'First Name'), 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32)),
                                'last_name' => array('title' => A::t('auctions', 'Last Name'), 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32)),
                                'company' => array('title' => A::t('auctions', 'Company'), 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 128)),
                                'phone' => array('title' => A::t('auctions', 'Phone'), 'validation' => array('required' => true, 'type' => 'phone', 'maxLength' => 32)),
                                'fax' => array('title' => A::t('auctions', 'Fax'), 'validation' => array('required' => false, 'type' => 'phone', 'maxLength' => 32)),
                                'address' => array('title' => A::t('auctions', 'Address'), 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 64)),
                                'address_2' => array('title' => A::t('auctions', 'Address (line 2)'), 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 64)),
                                'city' => array('title' => A::t('auctions', 'City'), 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 64)),
                                'zip_code' => array('title' => A::t('auctions', 'Zip Code'), 'validation' => array('required' => true, 'type' => 'zipCode', 'maxLength' => 32)),
                                'country_code' => array('title' => A::t('auctions', 'Country'), 'validation' => array('required' => true, 'type' => 'set', 'source' => array_keys($countries))),
                                'state' => array('title' => A::t('auctions', 'State/Province'), 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 64)),
                            )));
                            if ($validation['error']) {
                                $status = '0';
                                $alert = $validation['errorMessage'];
                                $errorField = $validation['errorField'];
                            } else {
                                $newAddress = new ShipmentAddress();
                                $newAddress->first_name = $cRequest->getPost('first_name');
                                $newAddress->last_name = $cRequest->getPost('last_name');
                                $newAddress->company = $cRequest->getPost('company');
                                $newAddress->phone = $cRequest->getPost('phone');
                                $newAddress->fax = $cRequest->getPost('fax');
                                $newAddress->address = $cRequest->getPost('address');
                                $newAddress->address_2 = $cRequest->getPost('address_2');
                                $newAddress->city = $cRequest->getPost('city');
                                $newAddress->zip_code = $cRequest->getPost('zip_code');
                                $newAddress->country_code = $cRequest->getPost('country_code');
                                $newAddress->state = $cRequest->getPost('state');
                                $newAddress->member_id = $member->id;
                                $newAddress->save();

                                $arr[] = '"addressId": "' . $newAddress->getPrimaryKey() . '"';
                                $order->shipment_address_id = $newAddress->getPrimaryKey();
                                $order->save();
                                $status = '1';
                            }
                        } else {
                            $validation = CWidget::create('CFormValidation', array('fields' => array(
                                'address_id' => array('title' => A::t('auctions', 'Shipping Address'), 'validation' => array('required' => true, 'type' => 'integer', 'maxLength' => 11))
                            )));
                            if ($validation['error']) {
                                $status = '0';
                                $alert = $validation['errorMessage'];
                                $errorField = $validation['errorField'];
                            } else {
                                $addressId = (int)$cRequest->getPost('address_id');
                                $address = ShipmentAddress::model()->findByPk($addressId);
                                if ($address && $member->id == $address->member_id) {
                                    $order->shipment_address_id = $address->id;
                                    $order->save();
                                    $status = '1';
                                } else {
                                    $status = '0';
                                    $alert = A::t('auctions', 'Address cannot be found in the database');
                                }
                            }
                        }
                    } else {
                        $status = '0';
                        $alert = A::t('auctions', 'Order cannot be found in the database');
                    }
                } else {
                    $status = '0';
                    $alert = A::t('auctions', 'Member with this ID does not exist in the database');
                }
            } else {
                $status = '0';
                $alert = A::t('auctions', 'To make a payment, you need to sign in or register');
            }
        } else {
            $status = '0';
            $alert = A::t('auctions', 'Error token');
        }

        // Ajax
        $alert = str_replace(array('"', "\r\n", "\n", "\t"), array('\"', '<br/>', '<br/>', ' '), $alert);
        $arr[] = '"status": "' . $status . '"';
        $arr[] = '"message": "' . $alert . '"';
        if (!empty($errorField)) {
            $arr[] = '"error_field": "' . $errorField . '"';
        }
        if (!empty($html)) {
            $html = str_replace(array('"', "\r\n", "\n", "\t"), array('\"', '', '', ''), $html);
            $arr[] = '"html": "' . $html . '"';
        }
        $this->_outputAjax($arr, false);
    }

    /**
     * Get Language List
     * @return array
     */
    private function _getLangList()
    {

        // Prepare languages
        $langList = array();

        $languagesResult = Languages::model()->findAll(array('condition' => 'is_active = 1', 'orderBy' => 'sort_order ASC'));
        if (is_array($languagesResult) && !empty($languagesResult)) {
            foreach ($languagesResult as $lang) {
                $langList[$lang['code']] = $lang['name_native'];
            }
        }

        return $langList;
    }

    /**
     * Form Registration Settings
     * required - required field for completion(true->required|false->not required)
     * disabled - indicates whether to hide or show the field (false->show|true->hide)
     * @return array
     */
    private function _getParamsFormRegistration()
    {

        $paramsFormRegistration = array(
            // Field Settings
            'first_name' => array('required' => true, 'disabled' => false),
            'last_name' => array('required' => true, 'disabled' => false),
            'gender' => array('required' => true, 'disabled' => false),
            'birth_date' => array('required' => false, 'disabled' => true),
            'phone' => array('required' => false, 'disabled' => true),
            'fax' => array('required' => false, 'disabled' => true),
            'website' => array('required' => false, 'disabled' => true),
            'company' => array('required' => false, 'disabled' => true),
            'address' => array('required' => false, 'disabled' => true),
            'address_2' => array('required' => false, 'disabled' => true),
            'city' => array('required' => false, 'disabled' => true),
            'zip_code' => array('required' => false, 'disabled' => true),
            'country_code' => array('required' => true, 'disabled' => false),
            'state' => array('required' => false, 'disabled' => false),
            'email' => array('required' => true, 'disabled' => false),
            'confirm_password' => array('required' => true, 'disabled' => false),
            'language_code' => array('required' => false, 'disabled' => true),
            'captcha' => array('disabled' => false),
            // Separator Settings
            'separatorPersonal' => array('disabled' => false),
            'separatorContact' => array('disabled' => true),
            'separatorAddress' => array('disabled' => false),
            'separatorAccount' => array('disabled' => false),
        );

        return $paramsFormRegistration;
    }


    /**
     * Get Genders
     * @return array
     */
    private function _getGenders()
    {

        $genders = array(
            'm' => A::t('auctions', 'Male'),
            'f' => A::t('auctions', 'Female'),
        );

        return $genders;
    }

    /**
     * Get States
     * @return array
     */
    private function _getStates()
    {

        $states = array();

        $statesResult = States::model()->findAll(array('condition' => 'is_active = 1', 'order' => 'sort_order DESC, state_name ASC'), array());
        if (!empty($statesResult)) {
            foreach ($statesResult as $state) {
                $states[$state['code']] = $state['state_name'];
            }
        }

        return $states;
    }

    /**
     * Prepares array addresses of total counts for each member
     * @return array
     */
    private function _getAddressesCounts()
    {
        $tableAddress = CConfig::get('db.prefix') . ShipmentAddress::model()->getTableName();
        $result = ShipmentAddress::model()->count(array('condition' => '', 'select' => $tableAddress . '.member_id', 'count' => '*', 'groupBy' => 'member_id', 'allRows' => true));
        $addressCounts = array();
        if (!empty($result) && is_array($result)) {
            foreach ($result as $countAddresses) {
                $addressCounts[$countAddresses['member_id']] = $countAddresses['cnt'];
            }
        }
        return $addressCounts;
    }

    /**
     * Member logout
     * @return void
     */
    private function _logout()
    {
        A::app()->getSession()->endSession();
        A::app()->getCookie()->remove('memberAuth');
        // clear cache
        if (CConfig::get('cache.enable')) CFile::emptyDirectory('protected/tmp/cache/');
    }

    /**
     * Outputs data to browser
     * @param array $data
     * @param bool $returnArray
     * @return void
     */
    private function _outputAjax($data = array(), $returnArray = true)
    {
        $json = '';
        if ($returnArray) {
            $json .= '[';
            $json .= array($data) ? implode(',', $data) : '';
            $json .= ']';
        } else {
            $json .= '{';
            $json .= implode(',', $data);
            $json .= '}';
        }

        $this->_outputJson($json);
    }

    /**
     * Outputs json to browser
     * @param string $json
     * @return void
     */
    private function _outputJson($json)
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        echo $json;

        exit;
    }
}
