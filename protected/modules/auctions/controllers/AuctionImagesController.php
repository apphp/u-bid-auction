<?php
/**
 * Auction Images controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                             PRIVATE
 * -----------                         ------------------
 * __construct
 * indexAction
 * manageAction
 * addAction
 * addMultipleAction
 * editAction
 * changeStatusAction
 * deleteAction
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
	\Modules\Auctions\Models\AuctionImages,
	\Modules\Auctions\Models\Auctions;

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
	\Countries,
	\A;

class AuctionImagesController extends CController
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
			Website::setMetaTags(array('title'=>A::t('auctions', 'Auctions Management')));
			// set backend mode
			Website::setBackend();

			$this->_view->actionMessage = '';
			$this->_view->errorField = '';

			$this->_view->tabs = AuctionsComponent::prepareTab('auctions');
			$this->_view->backendPath = $this->_backendPath;
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
		$this->redirect('auctionImages/manage');
	}

	/**
	 * Manage  auction images action handler
	 * @param int $auctionId
	 * @return void
	 */
	public function manageAction($auctionId = 0)
	{

		Website::prepareBackendAction('manage', 'auction', 'auctions/manage');
        Website::setMetaTags(array('title'=>A::t('auctions', 'Auction Images')));

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');


        $actionMessage = '';
		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
		}

        $paramSubTabs = array(
            'parentTab' => 'images',
            'activeTab' => 'auctions',
            'additionText' => $auction->auction_name.' | '.A::t('auctions', 'Images'),
        );

		$this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
		$this->_view->allowMultiImageUpload = ModulesSettings::model()->param('auctions', 'allow_multi_image_upload');
		$this->_view->actionMessage = $actionMessage;
		$this->_view->auctionId = $auction->id;
		$this->_view->auctionName = $auction->auction_name;
		$this->_view->render('auctionImages/backend/manage');
	}

	/**
	 * Add auction image action handler
	 * @param int $auctionId
	 * @return void
	 */
	public function addAction($auctionId = 0)
	{
        Website::setMetaTags(array('title'=>A::t('auctions', 'Add Image')));
		Website::prepareBackendAction('manage', 'auction', 'auctionImages/manage/auctionId/'.$auctionId);

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');

        $paramSubTabs = array(
            'parentTab' => 'add_or_edit_image',
            'activeTab' => 'images',
            'additionText' => A::t('auctions', 'Add Image'),
            'id' => $auction->id,
            'name' => $auction->auction_name.' | '.A::t('auctions', 'Images'),
        );

        $this->_view->subTabs       = AuctionsComponent::prepareSubTab($paramSubTabs);
		$this->_view->auctionId     = $auction->id;
		$this->_view->auctionName   = $auction->auction_name;
		$this->_view->imageMaxSize  = ModulesSettings::model()->param('auctions', 'image_max_size');
		$this->_view->render('auctionImages/backend/add');
	}

	/**
	 * Add auction multiple-image action handler
	 * @param int $auctionId
	 * @access public
	 * @return void
	 */
	public function addMultipleAction($auctionId = 0)
	{
		Website::prepareBackendAction('add', 'auction', 'auctionImages/manage/auctionId/'.$auctionId);
        Website::setMetaTags(array('title'=>A::t('auctions', 'Add Images')));

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');

		$allowMultiImageUpload = ModulesSettings::model()->param('auctions', 'allow_multi_image_upload');
		$maxImages = ModulesSettings::model()->param('auctions', 'auction_maximum_images_upload');
		$imageMaxSize = ModulesSettings::model()->param('auctions', 'image_max_size');

		// Block access to multi image upload
		if(!$allowMultiImageUpload){
			$this->redirect('auctionImages/manage/auctionId/'.$auctionId);
		}

		if(A::app()->getRequest()->getPost('act') == 'send'){
			$fieldsImages = array();
			for($i = 1; $i <= $maxImages; $i++){
				$fieldsImages['auction_image'][] = array('title'=>A::t('auctions', 'Image').' #'.$i, 'validation'=>array('required'=>false, 'type'=>'image', 'targetPath'=>'assets/modules/auctions/images/auctionimages/', 'maxSize'=>$imageMaxSize, 'fileName'=>'a'.$auctionId.'_'.CHash::getRandomString(10), 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif'));
			}

			$result = CWidget::create('CFormValidation', array('fields'=>$fieldsImages, 'multiArray'=>true));

			if($result['error']){
				$alert     = $result['errorMessage'];
				$alertType = 'validation';
			}else{
				// Add images here
				if(!empty($result['uploadedFiles'])){
					$errorSave = false;
					$width     = '200px';
					$height    = '200px';
					$directory = 'thumbs'.DS;
					$maxOrder  = AuctionImages::model()->max('sort_order', 'auction_id = :auction_id', array('i:auction_id'=>$auctionId));

					foreach($result['uploadedFiles'] as $pathToImage){
						// Create thumbnail
						$imageName         = basename($pathToImage);
						$path              = APPHP_PATH.DS.str_ireplace($imageName, '', $pathToImage);
						$thumbFileExt      = substr(strrchr($imageName, '.'), 1);
						$thumbFileName     = str_replace('.'.$thumbFileExt, '', $imageName);
						$thumbFileFullName = $thumbFileName.'_thumb.'.$thumbFileExt;
						CFile::copyFile($path.$imageName, $path.$directory.$thumbFileFullName);
						$thumbFileRealName = CImage::resizeImage($path.$directory, $thumbFileFullName, $width, $height);

						if(!empty($thumbFileRealName)){
							$image = new AuctionImages();
							$image->auction_id = $auctionId;
							$image->image_file = $imageName;
							$image->image_file_thumb = $thumbFileRealName;
							$image->sort_order = ++$maxOrder;
							$image->is_active  = 1;
							if(!$image->save()){
								if(APPHP_MODE == 'demo'){
									$alert     = CDatabase::init()->getErrorMessage();
									$alertType = 'warning';
								}else{
									$alert     = A::t('auctions', 'The error occurred while adding new record!');
									$alertType = 'error';
								}
								$errorSave = true;
								break;
							}
						}
					}
					if(!$errorSave){
						$alertType = 'success';
						$alert = A::t('auctions', 'New {item_type} has been successfully added!', array('{item_type}'=>A::t('auctions', 'Images')));
						A::app()->getSession()->setFlash('alert', $alert);
						A::app()->getSession()->setFlash('alertType', $alertType);
						$this->redirect('auctionImages/manage/auctionId/'.$auctionId);
					}
				}elseif(A::app()->getRequest()->getPost('act') == 'send'){
					$alertType = 'validation';
					$alert = A::t('auctions', 'You have to chose at least one image for uploading! Please re-enter.');
				}
			}

			if(!empty($alert)){
				$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
			}
		}

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
		}
        $paramSubTabs = array(
            'parentTab' => 'add_or_edit_image',
            'activeTab' => 'images',
            'additionText' => A::t('auctions', 'Add Images'),
            'id' => $auction->id,
            'name' => $auction->auction_name.' | '.A::t('auctions', 'Images'),
        );

        $this->_view->subTabs       = AuctionsComponent::prepareSubTab($paramSubTabs);
		$this->_view->maxImages     = $maxImages;
		$this->_view->auctionId     = $auctionId;
		$this->_view->auctionName   = $auction->auction_name;

		$this->_view->render('auctionImages/backend/addMultiple');
	}

	/**
	 * Edit auction image action handler
     * @param int $auctionId
     * @param int $id
     * @param string $delete
     * @return void
	 */
	public function editAction($auctionId = 0, $id = 0, $delete = '')
	{
		Website::prepareBackendAction('manage', 'auction', 'auctionImages/manage/auctionId/'.$auctionId);
        Website::setMetaTags(array('title'=>A::t('auctions', 'Edit Image')));
        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
        $findParams = array(
            'auction_id' => $auction->id,
        );
        $image = AuctionsComponent::checkRecordAccess($id, 'AuctionImages', true, 'auctionImages/manage/auctionId/'.$auctionId, $findParams);

		// Delete the image file
		if($delete === 'image'){
			$imagePath = 'assets/modules/auctions/images/auctionimages/'.$image->image_file;
			$imageThumbPath = 'assets/modules/auctions/images/auctionimages/thumbs/'.$image->image_file_thumb;
			$image->image_file = '';
			$image->image_file_thumb = '';
			if($image->save()){
				// Delete the images
				if(CFile::deleteFile($imagePath) && CFile::deleteFile($imageThumbPath)){
					$alert = A::t('auctions', 'Image has been successfully deleted!');
					$alertType = 'success';
				}else{
					$alert = A::t('auctions', 'An error occurred while deleting an image! Please try again later.');
					$alertType = 'warning';
				}
			}else{
				$alert = A::t('auctions', 'An error occurred while deleting an image! Please try again later.');
				$alertType = 'error';
			}

			if(!empty($alert)){
				$this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
			}
		}

        $paramSubTabs = array(
            'parentTab' => 'add_or_edit_image',
            'activeTab' => 'images',
            'additionText' => A::t('auctions', 'Edit Image'),
            'id' => $auction->id,
            'name' => $auction->auction_name.' | '.A::t('auctions', 'Images'),
        );

        $this->_view->subTabs       = AuctionsComponent::prepareSubTab($paramSubTabs);
		$this->_view->imageMaxSize = ModulesSettings::model()->param('auctions', 'image_max_size');
		$this->_view->id = $image->id;
		$this->_view->auctionId = $auction->id;
		$this->_view->auctionName = $auction->auction_name;
		$this->_view->render('auctionImages/backend/edit');
	}

	/**
	 * Delete auction image action handler
	 * @param int $auctionId
	 * @param int $id
	 * @return void
	 */
	public function deleteAction($auctionId = 0, $id = 0)
	{
		// set backend mode
		Website::setBackend();
		Website::prepareBackendAction('manage', 'auction', 'auctionImages/manage/auctionId/'.$auctionId);
        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
        $findParams = array(
            'auction_id' => $auction->id,
        );
        $image = AuctionsComponent::checkRecordAccess($id, 'AuctionImages', true, 'auctionImages/manage/auctionId/'.$auctionId, $findParams);

		$alert = '';
		$alertType = '';
		$actionMessage = '';


		if($image->delete()){
            if($image->getError()){
                $alert = $image->getErrorMessage();
                $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            }

            $imagePath          = 'assets/modules/auctions/images/auctionimages/'.$image->image_file;
            $imageThumbPath     = 'assets/modules/auctions/images/auctionimages/thumbs/'.$image->image_file_thumb;
            $imageDelete        = CFile::deleteFile($imagePath);
            $imageThumbDelete   = CFile::deleteFile($imageThumbPath);
            if(!$imageDelete || !$imageThumbDelete){
                $alert .= '<br/>'.A::t('auctions', 'An error occurred while deleting an image! Please try again later.');
                $alertType = 'warning';
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

		$this->redirect('auctionImages/manage/auctionId/'.$auctionId);
	}

	/**
	 * Change Auction Image status
	 * @param int $auctionId
	 * @param int $id
	 * @param int $page 	the page number
	 */
	public function changeStatusAction($auctionId = 0, $id = 0, $page = 1)
	{
		Website::prepareBackendAction('edit', 'auction', 'auctionImages/manage/auctionId/'.$auctionId);
        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
        $findParams = array(
            'auction_id' => $auction->id,
        );
        $image = AuctionsComponent::checkRecordAccess($id, 'AuctionImages', true, 'auctionImages/manage/auctionId/'.$auctionId, $findParams);

		$changeResult = AuctionImages::model()->updateByPk($id, array('is_active'=>($image->is_active == 1 ? '0' : '1')));
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

		if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
		}

		$this->redirect('auctionImages/manage/auctionId/'.$auctionId.(!empty($page) ? '?page='.(int)$page : 1));
	}
}
