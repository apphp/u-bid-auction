<?php
/**
 * Auction Types controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                              PRIVATE
 * -----------                          ------------------
 * __construct
 * indexAction
 * manageAction
 * [x]addAction
 * editAction
 * changeStatusAction
 * [x]deleteAction
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
	\Modules\Auctions\Models\AuctionTypes;

// Framework
use \Modules,
	\ModulesSettings,
	\CAuth,
	\CController,
	\CDatabase,
	\Website,
	\CWidget;

// Application
use \Bootstrap,
	\A;

class AuctionTypesController extends CController
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
			// set meta tags according to active auctions
			Website::setMetaTags(array('title'=>A::t('auctions', 'Auction Types Management')));
			// set backend mode
			Website::setBackend();

			$this->_view->actionMessage = '';
			$this->_view->errorField = '';
			$this->_view->backendPath = $this->_backendPath;

			$this->_view->tabs = AuctionsComponent::prepareTab('auction_types');
		}

		$settings                       = Bootstrap::init()->getSettings();
		$this->_view->dateFormat        = $settings->date_format;
		$this->_view->timeFormat        = $settings->time_format;
		$this->_view->dateTimeFormat    = $settings->datetime_format;
        $this->_view->typeFormat        = $settings->number_format;
	}

	/**
	 * Controller default action handler
	 */
	public function indexAction()
	{
		$this->redirect('auctionTypes/manage');
	}

	/**
	 * Manage action handler
	 */
	public function manageAction()
	{
		Website::prepareBackendAction('manage', 'auction_type', 'auctionTypes/manage');

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create(
				'CMessage', array($alertType, $alert, array('button'=>true))
			);
		}


		$this->_view->render('auctionTypes/backend/manage');
	}

	/**
	 * Add new action handler
	 * @return void
	 */
//	public function addAction()
//	{
//		Website::prepareBackendAction('add', 'auction_type', 'auctionTypes/manage');
//        Website::setMetaTags(array('title'=>A::t('auctions', 'Add Auction Type')));
//
//		$this->_view->render('auctionTypes/backend/add');
//	}

	/**
	 * Edit auctions action handler
	 * @param int $id
	 * @return void
	 */
	public function editAction($id = 0)
	{
		Website::prepareBackendAction('edit', 'auction_type', 'auctionTypes/manage');
        Website::setMetaTags(array('title'=>A::t('auctions', 'Edit Auction Type')));
        $auctionType = AuctionsComponent::checkRecordAccess($id, 'AuctionTypes', true, 'auctionTypes/manage');

		$this->_view->id = $auctionType->id;
		$this->_view->auctionType = $auctionType;
		$this->_view->render('auctionTypes/backend/edit');
	}

	/**
	 * Change status handler action
	 * @param int $id
	 * @param int $page
	 * @return void
	 */
	public function changeStatusAction($id = 0, $page = 1)
	{
		Website::prepareBackendAction('edit', 'auction_type', 'auctionTypes/managae');
        $auctionType = AuctionsComponent::checkRecordAccess($id, 'AuctionTypes', true, 'auctionTypes/manage');

        if(!$auctionType->is_default){
            $changeResult = AuctionTypes::model()->updateByPk($id, array('is_active'=>($auctionType->is_active == 1 ? '0' : '1')));
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

		$this->redirect('auctionTypes/manage'.(!empty($page) ? '?page='.(int)$page : 1));
	}

	/**
	 * Delete action handler
	 * @param int $id
     * @param int $page
     * @return void
	 */
//	public function deleteAction($id = 0, $page = 0)
//	{
//		Website::prepareBackendAction('delete', 'auction_type', 'auctionTypes/manage');
//
//                $auctionType = AuctionsComponent::checkRecordAccess($id, 'AuctionTypes', true, 'auctionTypes/manage');
//
//		$alert = '';
//		$alertType = '';
//
//		if($auctionType->delete()){
//			if($auctionType->getError()){
//				$alert = $auctionType->getErrorMessage();
//				$alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
//				$alertType = 'warning';
//			}else{
//				$alert = A::t('app', 'Delete Success Message');
//				$alertType = 'success';
//			}
//		}else{
//			if(APPHP_MODE == 'demo'){
//				$alert = CDatabase::init()->getErrorMessage();
//				$alertType = 'warning';
//			}else{
//				$alert = $auctionType->getError() ? $auctionType->getErrorMessage() : A::t('app', 'Delete Error Message');
//				$alertType = 'error';
//			}
//		}
//
//		if(!empty($alert)){
//			A::app()->getSession()->setFlash('alert', $alert);
//			A::app()->getSession()->setFlash('alertType', $alertType);
//		}
//
//		$this->redirect('auctionTypes/manage'.(!empty($page) ? '?page='.(int)$page : ''));
//	}
}
