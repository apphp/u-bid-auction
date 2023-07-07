<?php
/**
 * Categories controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                             PRIVATE
 * -----------                         ------------------
 * __construct                         _prepareCategoryCounts
 * indexAction                         _prepareAuctionCounts
 * manageAction
 * addAction
 * editAction
 * deleteAction
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
	\Modules\Auctions\Models\Auctions,
	\Modules\Auctions\Models\Categories;

// Framework
use \Modules,
	\ModulesSettings,
	\CAuth,
	\CConfig,
	\CController,
	\CDatabase,
	\CFile,
	\Website,
	\CWidget;

// Application
use \Bootstrap,
	\A;

class CategoriesController extends CController
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
			// set meta tags according to active categories
			Website::setMetaTags(array('title'=>A::t('auctions', 'Categories Management')));
			// set backend mode
			Website::setBackend();

			$this->_view->actionMessage = '';
			$this->_view->errorField = '';
			$this->_view->backendPath = $this->_backendPath;

			$this->_view->tabs = AuctionsComponent::prepareTab('categories');
		}
		
		$settings                       = Bootstrap::init()->getSettings();
		$this->_view->dateFormat        = $settings->date_format;
		$this->_view->timeFormat        = $settings->time_format;
		$this->_view->dateTimeFormat    = $settings->datetime_format;
        $this->_view->typeFormat        = $settings->number_format;
	}

	/**
	 * Controller default action handler
     * @return void
	 */
	public function indexAction()
	{
		$this->redirect('categories/manage');
	}

	/**
	 * Manage action handler
     * @param int $parentId
     * @return void
	 */
	public function manageAction($parentId = 0)
	{
		Website::prepareBackendAction('manage', 'category', 'categories/manage');

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

        $parentId = (int)$parentId;
        $parentCategoryName = '';
        $subTabs = '';

        // Validate parent category
        if($parentId > 0){
            $findParams = array(
                'parent_id' => 0,
            );
            $parentCategory = AuctionsComponent::checkRecordAccess($parentId, 'Categories', true, 'categories/manage', $findParams);
            $parentCategoryName = $parentCategory->name;
            $paramSubTabs = array(
                'parentTab' => 'sub_categories',
                'activeTab' => 'categories',
                'additionText' => $parentCategoryName,
            );
            $subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        }

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create(
				'CMessage', array($alertType, $alert, array('button'=>true))
			);
		}

        $this->_view->subTabs               = $subTabs;
        $this->_view->arrSubCategoryIds     = $this->_prepareCategoryCounts();
        $this->_view->arrAuctionCountIds    = $this->_prepareAuctionCounts();
        $this->_view->parentCategoryName    = $parentCategoryName;
        $this->_view->parentId              = $parentId;

		$this->_view->render('categories/backend/manage');
	}

	/**
	 * Add new category action handler
     * @param int $parentId
	 * @return void
	 */
	public function addAction($parentId = 0)
	{
		Website::prepareBackendAction('add', 'category', 'categories/manage');

        $parentId = (int)$parentId;
        $parentCategoryName = '';
        $subTabs = '';

        // Validate parent category
        if($parentId > 0){
            $findParams = array(
                'parent_id' => 0,
            );
            $parentCategory = AuctionsComponent::checkRecordAccess($parentId, 'Categories', true, 'categories/manage', $findParams);

            $parentCategoryName = $parentCategory->name;
            $paramSubTabs = array(
                'parentTab' => 'add_or_edit_sub_category',
                'activeTab' => 'sub_category',
                'additionText' => A::t('auctions', 'Add Category'),
                'id' => $parentId,
                'name' => $parentCategoryName,
            );
        }else{
            $paramSubTabs = array(
                'parentTab' => 'sub_categories',
                'activeTab' => 'categories',
                'additionText' => A::t('auctions', 'Add Category'),
            );
        }
        $subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);

        $this->_view->subTabs               = $subTabs;
        $this->_view->parentCategoryName    = $parentCategoryName;
        $this->_view->parentId              = $parentId;

		$this->_view->render('categories/backend/add');
	}

	/**
	 * Edit category action handler
	 * @param int $id
	 * @param string $delete
	 * @return void
	 */
	public function editAction($id = 0, $delete = '')
	{
		Website::prepareBackendAction('edit', 'category', 'categories/manage');

        $category = AuctionsComponent::checkRecordAccess($id, 'Categories', true, 'categories/manage');
        $parentId = $category->parent_id;

        $parentCategoryName = '';
        $subTabs = '';

        // Validate parent category
        if($parentId > 0){
            $findParams = array(
                'parent_id' => 0,
            );
            $parentCategory = AuctionsComponent::checkRecordAccess($parentId, 'Categories', true, 'categories/manage', $findParams);
            $parentCategoryName = $parentCategory->name;
            $paramSubTabs = array(
                'parentTab' => 'add_or_edit_sub_category',
                'activeTab' => 'sub_category',
                'additionText' => A::t('auctions', 'Edit Category'),
                'id' => $parentId,
                'name' => $parentCategoryName,
            );
        }else{
            $paramSubTabs = array(
                'parentTab' => 'sub_categories',
                'activeTab' => 'categories',
                'additionText' => A::t('auctions', 'Edit Category'),
            );
        }
        $subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);

        if($delete == 'image' && $category->icon){
            $icon = $category->icon;
            $iconThumb = $category->icon_thumb;
            $iconPath = 'assets/modules/auctions/images/categories/'.$icon;
            $iconThumbPath = 'assets/modules/auctions/images/categories/thumbs/'.$iconThumb;
            $category->icon = '';
            $category->icon_thumb = '';

            if($category->save()){
                // Delete the icon
                if(CFile::deleteFile($iconPath) && CFile::deleteFile($iconThumbPath)){
                    $alert = A::t('auctions', 'Image has been successfully deleted!');
                    $alertType = 'success';
                }else{
                    $alert = A::t('auctions', 'An error occurred while deleting an image! Please try again later.');
                    $alertType = 'warning';
                }
            }else{
                if(APPHP_MODE == 'demo'){
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                }else{
                    if($category->getErrorMessage()){
                        $alert = $category->getErrorMessage();
                        $alertType = 'error';
                    }else{
                        $alert = A::t('auctions', 'An error occurred while deleting an image! Please try again later.');
                        $alertType = 'error';
                    }
                }
            }
        }

        if(!empty($alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
        }

        $this->_view->subTabs               = $subTabs;
        $this->_view->parentCategoryName    = $parentCategoryName;
        $this->_view->parentId              = $parentId;
		$this->_view->id                    = $id;
		$this->_view->render('categories/backend/edit');
	}

	/**
	 * Delete category action handler
	 * @param int $id
	 * @param int $parentId
     * @param int $page
     * @return void
	 */
	public function deleteAction($id = 0, $parentId = 0, $page = 0)
	{
		Website::prepareBackendAction('delete', 'category', 'categories/manage');

		$alert = '';
		$alertType = '';

        $issetSubCategories = (Categories::model()->find('parent_id= :id', array(':id'=>$id)) ? true : false);
        if($issetSubCategories){
            $alert = A::t('auctions', 'You can not delete this category, it has subcategories');
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', 'warning');

            $this->redirect('categories/manage');
        }

        $category = AuctionsComponent::checkRecordAccess($id, 'Categories', true, 'categories/manage');
        $parentId = $category->parent_id;

		if($category->delete()){
			if($category->getError()){
				$alert = $category->getErrorMessage();
				$alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
				$alertType = 'warning';
			}else{
				$alert = A::t('app', 'Delete Success Message');
				$alertType = 'success';
			}

            $icon               = $category->icon;
            $iconThumb          = $category->icon_thumb;
            $iconDelete         = true;
            $iconThumbDelete    = true;

            if(!empty($icon)){
                $iconPath       = 'assets/modules/auctions/images/categories/'.$icon;
                $iconDelete      = CFile::deleteFile($iconPath);
            }
            if(!empty($iconThumb)){
                $iconThumbPath  = 'assets/modules/auctions/images/categories/thumbs/'.$iconThumb;
                $iconThumbDelete = CFile::deleteFile($iconThumbPath);
            }
            if(!$iconDelete || !$iconThumbDelete){
                $alert .= '<br/>'.A::t('auctions', 'An error occurred while deleting an image! Please try again later.');
                $alertType = 'warning';
            }
		}else{
			if(APPHP_MODE == 'demo'){
				$alert = CDatabase::init()->getErrorMessage();
				$alertType = 'warning';
			}else{
				$alert = $category->getError() ? $category->getErrorMessage() : A::t('app', 'Delete Error Message');
				$alertType = 'error';
			}
		}

		if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
		}

		$this->redirect('categories/manage'.($parentId > 0 ? '/parentId/'.$parentId : '').(!empty($page) ? '?page='.(int)$page : ''));
	}

    /**
     * Prepares array of total counts for each category
     * @return array $categoryCounts
     */
    private function _prepareCategoryCounts()
    {
        $tableNameCategories = CConfig::get('db.prefix').Categories::model()->getTableName();
        $categories = Categories::model()->count(array('condition'=>'', 'select'=>$tableNameCategories.'.parent_id', 'count'=>'*', 'groupBy'=>'parent_id', 'allRows'=>true));
        $categoryCounts = array();
        if(!empty($categories)){
            foreach($categories as $category){
                $categoryCounts[$category['parent_id']] = $category['cnt'];
            }
        }

        return $categoryCounts;
    }

    /**
     * Prepares array of total counts for each auctions
     * @return array $auctionCounts
     */
    private function _prepareAuctionCounts()
    {
        $auctionCounts = array();

        $tableNameAuctions = CConfig::get('db.prefix').Auctions::model()->getTableName();
        $auctions = Auctions::model()->count(array('condition'=>'', 'select'=>$tableNameAuctions.'.category_id', 'groupBy'=>'category_id', 'allRows'=>true));
        if(!empty($auctions)){
            foreach($auctions as $auction){
                $auctionCounts[$auction['category_id']] = $auction['cnt'];
            }
        }

        return $auctionCounts;
    }
}
