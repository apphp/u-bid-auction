<?php
/**
 * TaxesController controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct              _prepareCountriesCounts
 * indexAction              _prepareCountryNames
 * manageAction
 * addAction
 * editAction
 * deleteAction
 * changeStatusAction
 * manageCountriesAction
 * addCountryAction
 * editCountryAction
 * deleteCountryAction
 *
 */

namespace Modules\Auctions\Controllers;

// Modules
use Modules\Auctions\Components\AuctionsComponent,
    Modules\Auctions\Models\TaxCountries,
    Modules\Auctions\Models\Taxes;

// Framework
use \CAuth,
    \A,
    \CController,
    \CWidget,
    \CDatabase,
    \CFile,
    \CConfig;

// Application
use \Modules,
    \Bootstrap,
    \Countries,
    \Website;

class TaxesController extends CController
{
	
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
		if(!Modules::model()->isInstalled('auctions')){
			if(CAuth::isLoggedInAsAdmin()){
				$this->redirect($this->_backendPath.'modules/index');
			}else{
				$this->redirect(Website::getDefaultPage());
			}
		}
	
		if(CAuth::isLoggedInAsAdmin()){
            // Set meta tags according to active auctions
            Website::setMetaTags(array('title'=>A::t('auctions', 'Taxes Management')));
            // Set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
			$this->_view->backendPath = $this->_backendPath;

            $this->_view->tabs = AuctionsComponent::prepareTab('taxes');
        }

        $appendCode  = '';
        $prependCode = '';
        $symbol      = A::app()->getCurrency('symbol');
        $symbolPlace = A::app()->getCurrency('symbol_place');

        if($symbolPlace == 'before'){
            $prependCode = $symbol;
        }else{
            $appendCode = $symbol;
        }

        $settings                       = Bootstrap::init()->getSettings();
        $this->_view->dateFormat        = $settings->date_format;
        $this->_view->timeFormat        = $settings->time_format;
        $this->_view->dateTimeFormat    = $settings->datetime_format;
        $this->_view->numberFormat      = $settings->number_format;
        $this->_view->pricePrependCode  = $prependCode;
        $this->_view->priceAppendCode   = $appendCode;
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('taxes/manage');
    }

    /**
     * Manage action handler
     * @return void
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'tax', 'taxes/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
        }

        $this->_view->arrContriesIds = $this->_prepareCountriesCounts();
        $this->_view->render('taxes/backend/manage');
    }

    /**
     * Add new action handler
     * @return void
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', 'tax', 'taxes/manage');

        $this->_view->render('taxes/backend/add');
    }

    /**
     * Edit tax action handler
     * @param int $id
     * @return void
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'tax', 'taxes/manage');
        $tax = AuctionsComponent::checkRecordAccess($id, 'Taxes', true, 'taxes/manage');

        $this->_view->id = $id;
        $this->_view->render('taxes/backend/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @param int $page
     * @return void
     */
    public function deleteAction($id = 0, $page = 1)
    {
        Website::prepareBackendAction('delete', 'auction', 'shipments/manage');

        $tax = AuctionsComponent::checkRecordAccess($id, 'Taxes', true, 'taxes/manage');

        $alert = '';
        $alertType = '';

        if($tax->delete()){
            if($tax->getError()){
                $alert = $tax->getErrorMessage();
                $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = $tax->getError() ? $tax->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('taxes/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Change status action handler
     * @param int $id
     * @param unt $page
     * @return void
     */
    public function changeStatusAction($id = 0, $page = 1)
    {
        // Block access if admin has no active privilege to change ban lists
        Website::prepareBackendAction('edit', 'tax', 'taxes/manage');
        $tax = AuctionsComponent::checkRecordAccess($id, 'Taxes', true, 'taxes/manage');

        if(Taxes::model()->updateByPk($id, array('is_active'=>($tax->is_active == 1 ? '0' : '1')))){
            $alert = A::t('app', 'Status has been successfully changed!');
            $alertType = 'success';
        }else{
            $alert = ((APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error'));
            $alertType = 'error';
        }

        A::app()->getSession()->setFlash('alert', $alert);
        A::app()->getSession()->setFlash('alertType', $alertType);

        $this->redirect('taxes/manage'.(!empty($page) ? '?page='.(int)$page : 1));
    }

    /**
     * Manage Countries action handler
     * @param int $taxId
     * @return void
     */
    public function manageCountriesAction($taxId = 0)
    {
        Website::prepareBackendAction('manage', 'tax', 'taxes/manage');

        $tax = AuctionsComponent::checkRecordAccess($taxId, 'Taxes', true, 'taxes/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
        }

        $paramSubTabs = array(
            'parentTab' => 'tax_countries',
            'activeTab' => 'taxes',
            'additionText' => $tax->name,
        );
        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->taxName = $tax->name;
        $this->_view->taxId = $taxId;
        $this->_view->render('taxes/backend/taxCountryManage');
    }

    /**
     * Add new action handler
     * @param int $taxId
     * @return void
     */
    public function addCountryAction($taxId = 0)
    {
        Website::setBackend();
        Website::prepareBackendAction('add', 'tax', 'taxes/manageCountries');

        $tax = AuctionsComponent::checkRecordAccess($taxId, 'Taxes', true, 'taxes/manage');

        $paramSubTabs = array(
            'parentTab' => 'country',
            'activeTab' => 'tax_country',
            'additionText' => A::t('auctions', 'Add Country'),
            'id' => $taxId,
            'name' => $tax->name,
        );
        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->taxName = $tax->name;
        $this->_view->taxId = $taxId;
        $this->_view->arrCountryNames = $this->_prepareCountryNames();
        $this->_view->render('taxes/backend/taxCountryAdd');
    }

    /**
     * Edit taxes action handler
     * @param int $taxId
     * @param int $id
     * @return void
     */
    public function editCountryAction($taxId = 0, $id = 0)
    {
        Website::prepareBackendAction('edit', 'tax', 'taxes/manageCountries');

        $tax = AuctionsComponent::checkRecordAccess($taxId, 'Taxes', true, 'taxes/manage');
        $findParams = array(
            'tax_id' => $tax->id,
        );
        $country = AuctionsComponent::checkRecordAccess($id, 'TaxCountries', true, 'taxes/manage', $findParams);

        $paramSubTabs = array(
            'parentTab' => 'country',
            'activeTab' => 'tax_country',
            'additionText' => A::t('auctions', 'Edit Country'),
            'id' => $taxId,
            'name' => $tax->name,
        );
        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->id = $id;
        $this->_view->taxId = $taxId;
        $this->_view->taxName = $tax->name;
        $this->_view->arrCountryNames = $this->_prepareCountryNames();
        $this->_view->render('taxes/backend/taxCountryEdit');
    }

    /**
     * Delete Country action handler
     * @param int $id
     * @param int $taxId
     * @return void
     */
    public function deleteCountryAction($taxId = 0, $id = 0)
    {
        Website::prepareBackendAction('delete', 'tax', 'taxes/manage');

        $alert = '';
        $alertType = '';

        $tax = AuctionsComponent::checkRecordAccess($taxId, 'Taxes', true, 'taxes/manage');
        $findParams = array(
            'tax_id' => $tax->id,
        );
        $country = AuctionsComponent::checkRecordAccess($id, 'TaxCountries', true, 'taxes/manage', $findParams);

        if($country->delete()){
            if($country->getError()){
                $alert = $country->getErrorMessage();
                $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = $country->getError() ? $country->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if(!empty($alert)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('taxes/manageCountries/taxId/'.$taxId.(!empty($page) ? '?page='.(int)$page : ''));
    }


    /**
     * Prepares array of total counts for each categories
     * @return array
     */
    private function _prepareCountriesCounts()
    {
        $taxCountriesTableName = CConfig::get('db.prefix').TaxCountries::model()->getTableName();
        $result = TaxCountries::model()->count(array('condition'=>'', 'select'=>$taxCountriesTableName.'.tax_id', 'count'=>'*', 'groupBy'=>'tax_id', 'allRows'=>true));
        $countriesCounts = array();
        if(!empty($result) && is_array($result)){
            foreach($result as $count){
                $countriesCounts[$count['tax_id']] = $count['cnt'];
            }
        }
        return $countriesCounts;
    }

    /**
     * Prepares array of total counts for each categories
     * @return array
     */
    private function _prepareCountryNames()
    {
        $result = Countries::model()->findAll('is_active = 1');
        $arrCountryNames = array();
        foreach($result as $country){
            $arrCountryNames[$country['country_code']] = $country['country_name'];
        }
        return $arrCountryNames;
    }
}
