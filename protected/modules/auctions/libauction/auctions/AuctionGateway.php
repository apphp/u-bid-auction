<?php
/**
 * AuctionGateway is the abstract class that must be implemented by auction provider classes
 * 
 * PUBLIC (abstract):		PROTECTED:					PRIVATE:		        
 * ---------------         	---------------            	---------------
 * drawAuctionForm          _auctionLog
 */

abstract class AuctionGateway
{
	/** @string */
	protected $_logMode;
	/** @string */
	protected $_logTo;
	/** @string */
	protected $_logData;
	/** @string */
	protected $_auctionModel;
	
	/**
	 * Draws auction form
	 */
	abstract public function drawAuctionForm();

    /**
     * Add bid to the auction
     */
    abstract public function addBid();

	/**
	 * Register script files
	 */
	abstract public function registerFiles();

    /**
     * Auto Update Auctions
     */
	abstract public function autoUpdateAuction();

	/**
	 * Auction Log
     * @param string $msg
	 */
	protected function _auctionLog($msg = '')
	{		
		if($this->_logMode){
			$this->_logData .= '<br />'."\n".$msg;
		}    
	}
	
}
