<?php
/**
 * Packages controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                             PRIVATE
 * -----------                         ------------------
 * __construct
 * indexAction
 * manageAction
 * addAction
 * editAction
 * changeStatusAction
 * deleteAction
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
	\Modules\Auctions\Models\Packages;

// Framework
use \Modules,
	\ModulesSettings,
	\CAuth,
	\CController,
	\CDatabase,
	\CFile,
	\CHash,
	\CImage,
	\Website,
	\CWidget;

// Application
use \Bootstrap,
	\Currencies,
    \A;

class PackagesController extends CController
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
			Website::setMetaTags(array('title'=>A::t('auctions', 'Packages Management')));
			// Set backend mode
			Website::setBackend();

			$this->_view->actionMessage = '';
			$this->_view->errorField = '';
			$this->_view->backendPath = $this->_backendPath;

			$this->_view->tabs = AuctionsComponent::prepareTab('packages');
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
		$this->_view->typeFormat        = $settings->number_format;
		$this->_view->pricePrependCode  = $prependCode;
		$this->_view->priceAppendCode   = $appendCode;
	}

	/**
	 * Controller default action handler
	 */
	public function indexAction()
	{
		$this->redirect('packages/manage');
	}

	/**
	 * Manage  package action handler
	 * @param int $packageId
	 * @return void
	 */
	public function manageAction($packageId = 0)
	{

		Website::prepareBackendAction('manage', 'package', 'packages/manage');

		$actionMessage = '';
		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
		}

		$this->_view->actionMessage = $actionMessage;
		$this->_view->render('packages/backend/manage');
	}

	/**
	 * Add package image action handler
	 * @param int $packageId
	 * @return void
	 */
	public function addAction($packageId = 0)
	{
        Website::setMetaTags(array('title'=>A::t('auctions', 'Add Package')));
		Website::prepareBackendAction('manage', 'package', 'packages/manage');

		$this->_view->render('packages/backend/add');
	}

	/**
	 * Edit package action handler
     * @param int $id
     * @return void
	 */
	public function editAction($id = 0)
	{
		Website::prepareBackendAction('manage', 'package', 'packages/manage');
        Website::setMetaTags(array('title'=>A::t('auctions', 'Edit Package')));
        $package = AuctionsComponent::checkRecordAccess($id, 'Packages', true, 'packages/manage');

		$this->_view->id = $package->id;
		$this->_view->package = $package;
		$this->_view->render('packages/backend/edit');
	}

	/**
	 * Delete package action handler
	 * @param int $id
     * @param int $page
	 * @return void
	 */
	public function deleteAction($id = 0, $page = 1)
	{
		// set backend mode
		Website::setBackend();
		Website::prepareBackendAction('manage', 'package', 'packages/manage');
        $package = AuctionsComponent::checkRecordAccess($id, 'Packages', true, 'packages/manage');

        $alert = '';
		$alertType = '';
		$actionMessage = '';

        if($package->is_default){
            $alert = A::t('auctions', 'You cannot delete the package by default');
            $alertType = 'warning';
        }elseif($package->delete()){
            if($package->getError()){
                $alert = $package->getErrorMessage();
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
				$alert = A::t('auctions', 'An error occurred while deleting an image! Please try again later.');
				$alertType = 'error';
			}
		}

		if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
		}

		$this->redirect('packages/manage'.(!empty($page) ? '?page='.(int)$page : 1));
	}

	/**
	 * Change Packages status
	 * @param int $id
	 * @param int $page 	the page number
	 */
	public function changeStatusAction($id = 0, $page = 1)
	{
		Website::prepareBackendAction('edit', 'package', 'packages/manage');
        $package = AuctionsComponent::checkRecordAccess($id, 'Packages', true, 'packages/manage');

        if(!$package->is_default){
            $changeResult = Packages::model()->updateByPk($id, array('is_active'=>($package->is_active == 1 ? '0' : '1')));
            if($changeResult){
                $alert = A::t('app', 'Status has been successfully changed!');
                $alertType = 'success';
            }else{
                if(APPHP_MODE == 'demo'){
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                }else{
                    $alert = A::t('app', 'Status changing error');
                    $alertType = 'error';
                }
            }
        }else{
            $alert = A::t('auctions', 'The default entry cannot change the status!');
            $alertType = 'warning';
        }

		if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
		}

		$this->redirect('packages/manage'.(!empty($page) ? '?page='.(int)$page : 1));
	}


    /*   FRONTEND ACTIONS   */

    /**
     * Show all package action handler
     */
    public function packagesAction()
    {
        // block access to this controller for not-logged patients
        if(CAuth::getLoggedRole() != 'member'){
            Website::setLastVisitedPage();
            A::app()->getSession()->setFlash('alert', A::t('auctions', 'You need to login to continue.'));
            A::app()->getSession()->setFlash('alertType', 'danger');
            CAuth::handleLogin('members/login', 'member');
        }

        $this->_view->_activeMenu = 'packages/packages';

        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('auctions', 'Bid Packages')));
        // set frontend settings
        Website::setFrontend();

        $beforePrice            = '';
        $afterPrice             = '';
        $packages               = Packages::model()->findAll(array('condition'=>'is_active = 1', 'order'=>'price ASC'));
        $countPackages          = count($packages);
        $checkFreePlanInOrders  = AuctionsComponent::checkFreePlanInOrders();

        // Hide free package if there is an order with a free package from a member
        if($checkFreePlanInOrders){
            foreach($packages as $key => $package){
                if($package['price'] == 0){
                    unset($packages[$key]);
                    $countPackages--;
                }
            }
        }

        $actionMessage = '';
        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->beforePrice   = $beforePrice;
        $this->_view->afterPrice    = $afterPrice;
        $this->_view->packages      = $packages;
        $this->_view->countPackages = $countPackages;
        $this->_view->actionMessage = $actionMessage;

        A::app()->view->setLayout('no_columns');
        $this->_view->render('packages/packages');
    }
}
