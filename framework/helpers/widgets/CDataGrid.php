<?php
/**
 * CDataGrid widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE (static):
 * ----------               ----------                  ----------
 * init
 *
 *
 */

class CDataGrid extends CWidgs
{
	
	const NL = "\n";
	/** @var array */
	private static $_allowedActs = array('send', 'change');
	/** @var bool */
	private static $_clientValidation = true;
	/** @var bool */
	private static $_clientValidationOnlyRequired = true;
	
	/**
	 *
	 * Usage: (in view file)
	 *  echo CWidget::create('CDataForm', array(
	 *       'model'			=> 'tableName',
	 *       'action'			=> 'locations/add | locations/edit/id/1',
	 *       'fields'=>array(
	 *           'field_1'=>array('type'=>'textbox',        'title'=>'Username',   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'username', 'minLength'=>6, 'maxLength'=>32), 'htmlOptions'=>array()),
	 *       ),
	 *       'buttons'=>array(
	 *          'submit'=>array('type'=>'submit', 'value'=>'Send', 'htmlOptions'=>array('name'=>'')),
	 *          'submitUpdate'=>array('type'=>'submit', 'value'=>'Update', 'htmlOptions'=>array('name'=>'btnUpdate')),
	 *          'submitUpdateClose'=>array('type'=>'submit', 'value'=>'Update & Close', 'htmlOptions'=>array('name'=>'btnUpdateClose')),
	 *          'reset'=>array('type'=>'reset', 'value'=>'Reset', 'htmlOptions'=>array()),
	 *          'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
	 *          'custom'=>array('type'=>'button', 'value'=>'Custom', 'htmlOptions'=>array('onclick'=>"jQuery(location).attr('href','categories/index');")),
	 *       ),
	 *       'buttonsPosition'=>'bottom',
	 *       'messagesSource'=>'core',
	 * 		 'customMessages'=>array('insert'=>array('success'=>'', 'error'=>''), 'update'=>array('success'=>'', 'error'=>'')),
	 *       'showAllErrors'=>false,
	 *		 'alerts'=>array('type'=>standard|flash, 'itemName'=>A::t('app', 'Field Name').' #'.$id),
	 *		 'clientValidation'=>array('enabled'=>true, 'onlyRequired'=>false),
	 *       'return'=>true,
	 *  ));
	 */
	public static function init($params = array())
	{
		parent::init($params);
		
		$baseUrl = A::app()->getRequest()->getBaseUrl();
		$cRequest = A::app()->getRequest();
		$output = '';
		
		$model = self::params('model', '');
//		$resetBeforeStart = (bool)self::params('resetBeforeStart', false);
//		$primaryKey = (int)self::params('primaryKey', '');
//		$operationType = self::params('operationType', 'add', 'in_array', array('edit', 'add'));
//		$action = self::params('action', '');
//		$successUrl = self::params('successUrl', '');
//		$successCallbackAdd = self::params('successCallback.add', '');
//		$successCallbackEdit = self::params('successCallback.edit', '');
//		$cancelUrl = self::params('cancelUrl', '');
//		$method = self::params('method', 'post');
//		$htmlOptions = (array)self::params('htmlOptions', array(), 'is_array');
//		$requiredFieldsAlert = self::params('requiredFieldsAlert', false);
//		$fieldSets = self::params('fieldSets', array(), 'is_array');
//		$fieldWrapperTag = self::params('fieldWrapper.tag', 'div');
//		$fieldWrapperClass = self::params('fieldWrapper.class', 'row');
//		$linkType = (int)self::params('linkType', 0); /* Link type: 0 - standard, 1 - SEO */
//		$return = (bool)self::params('return', true);

//		$fields = self::params('fields', array(), 'is_array');
//		$translationInfo = self::params('translationInfo', array());
//		$languages = self::keyAt('languages', $translationInfo, array());
//
//		$relation = self::keyAt('relation', $translationInfo, array());
//		$keyFrom = isset($relation[0]) ? $relation[0] : '';
//		$keyTo = isset($relation[1]) ? $relation[1] : '';
//
//		$translationFields = self::params('translationFields', array());
//		$msgSource = self::params('messagesSource', 'core');
//		$customMessages = self::params('customMessages', array());
//		$showAllErrors = (bool)self::params('showAllErrors', false);
//		$alertType = self::params('alerts.type', 'standard');
//		$alertItemName = self::params('alerts.itemName', '');
//		$buttonsPosition = self::params('buttonsPosition', 'bottom');
//		$buttons = self::params('buttons', array());
//		if (self::issetKey('cancel', $buttons) && !empty($cancelUrl)) {
//			$buttons['cancel']['htmlOptions']['onclick'] = 'jQuery(location).attr(\'href\',\'' . $baseUrl . $cancelUrl . '\');';
//		}
		

//		if ($return) return $output;
//		else echo $output;
	}
	
}
