<?php
/**
 * StatisticsController controller
 * This controller intended to Backend modes
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct              _getLastYears
 * indexAction
 * manageAction
 *
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent;
use \Modules\Auctions\Models\Auctions;
use \Modules\Auctions\Models\Orders;

// Global
use \A,
    \CAuth,
    \CLocale,
    \CController;

// Application
use \Website,
    \Bootstrap,
    \Modules;

class StatisticsController extends CController
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
            // Set meta tags according to active manufacturer
            Website::setMetaTags(array('title'=>A::t('auctions', 'Statistics')));

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
			$this->_view->backendPath = $this->_backendPath;

            $this->_view->tabs = AuctionsComponent::prepareTab('statistics');
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
        $this->redirect('statistics/manage');
    }

    /**
     * Manage action handler
     * @return void
     */
    public function manageAction()
    {
        Website::setBackend();
        Website::prepareBackendAction('manage', 'statistic', 'statistics/manage');

        $selectedYear = A::app()->getRequest()->getQuery('year', 'int');
        $currentYear  = CLocale::date('Y');
        $validYears   = $this->_getLastYears(5, $currentYear);

        // Prepare selected year
        if(empty($selectedYear) || !in_array($selectedYear, $validYears)){
            $selectedYear = $currentYear;
        }

        $fromDate     = $selectedYear.'-01-01 00:00:00';
        $toDate       = $selectedYear.'-12-31 23:59:59';

        // Prepare orders data
        $orders = Orders::model()->findAll("created_at >= :from_date AND created_at <= :to_date AND status > 0", array(':from_date'=>$fromDate, ':to_date'=>$toDate));

        $ordersCount  = array_fill(1, 12, 0);
        $ordersIncome = array_fill(1, 12, 0);
        if(!empty($orders)){
            foreach($orders as $order){
                $month = (int)substr($order['created_at'], 5, 2);

                $ordersCount[$month]++;
                $ordersIncome[$month] += $order['total_price'];
            }
        }

        // Prepare appointments data
        $auctions       = Auctions::model()->findAll();
        $allAuctions = array();
        $createdAuctions = array();
        $closedAuctions = array();

        foreach($auctions as $auction){
            if($auction['status'] == 1){
                $month = (int)substr($auction['date_from'], 5, 2);
                $createdAuctions[$month]++;
            }elseif($auction['status'] == 3){
                $month = (int)substr($auction['date_from'], 5, 2);
                $closedAuctions[$month]++;
            }
        }

        $this->_view->currentYear       = $currentYear;
        $this->_view->selectedYear      = $selectedYear;
        $this->_view->ordersCount       = $ordersCount;
        $this->_view->ordersIncome      = $ordersIncome;
        $this->_view->createdAuctions   = $createdAuctions;
        $this->_view->closedAuctions    = $closedAuctions;

        $this->_view->render('statistics/backend/manage');
    }

    /* *
     * Get last year
     * @param int $countYears
     * @param int $startYear
     * @return array
     * */
    private function _getLastYears($countYears = 5, $startYear = '')
    {
        if(empty($startYear)){
            $startYear = CLocale::date('Y');
        }
        $lastYears = array();

        for($i= $startYear; $i >= $startYear - $countYears; $i--){
            $lastYears[$i] = $i;
        }

        return $lastYears;
    }

}
