<?php
/**
 * This file contains auctions interfaces for ApPHP Directy CMF
 *
 * PUBLIC: (static)			PROTECTED:					PRIVATE: (static)
 * ---------------         	---------------            	---------------
 * init 												_classMap
 * 
 */

// Module
use \Modules\Auctions\Components\AuctionsComponent;
use \Modules\Auctions\Models\AuctionTypes;

class AuctionType
{
	
	/** @var object */    
    private static $_instance;

	/**
	 * Initializes the database class
	 * @param int $auctionTypeId
	 * @return class
	 */
	public static function init($auctionTypeId = 0)
	{
        //if(self::$_instance == null){

            $auctionTypeInfo = self::_classMap($auctionTypeId);
            $auctionTypeClass = $auctionTypeInfo['class'];
			$classFile = $auctionTypeInfo['file'];
			$classPath = $auctionTypeInfo['path'];
			
			if(file_exists($classPath.$classFile)){
				CLoader::library('AuctionGateway.php', 'libauction/auctions/');
				CLoader::library($classFile, 'libauction/auctions/');
				self::$_instance = new $auctionTypeClass();
			}else{
				CDebug::AddMessage('errors', 'auction-type-missing-class', A::t('core', 'Unable to find class "{class}".', array('{class}'=>'libraries/libauction/auctions/'.$auctionTypeClass)));
			}			
		//}

        return self::$_instance;
	}
	
	/**
	 * Detects class name and path
	 * @param int $auctionTypeId
     * @return array
	*/
	private static function _classMap($auctionTypeId = 0)
	{
		$result = array();

		if(!empty($auctionTypeId)){
            $auctionType = AuctionTypes::model()->findByPk($auctionTypeId);
            if($auctionType){
                $result['class'] = $auctionType->class_name ? $auctionType->class_name : '';
                $result['file']  = $auctionType->file_name ? $auctionType->file_name : '';
                $result['path']  = APPHP_PATH.DS.'protected'.DS.'libraries'.DS.'libauction'.DS.'auctions'.DS;
            }
        }
		
		return $result;		
	}

}
