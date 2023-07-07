<?php
/**
 * This file implements functionality penny auction
 *
 * Usage:
 *
 * CLoader::library('libauction/AuctionType.php');
 * $classic = AuctionType::init(2);
 * echo $classic->drawAuctionForm(array(
 *
 * ));
 *
 *
 *
 * PUBLIC:                  PROTECTED:                  PRIVATE:
 * ---------------          ---------------             ---------------
 * drawAuctionForm
 * addBid
 * autoUpdateAuction
 * registerFiles
 *
 */

// Module
use \Modules\Auctions\Components\AuctionsComponent,
    \Modules\Auctions\Models\Auctions,
    \Modules\Auctions\Models\BidsHistory,
    \Modules\Auctions\Models\Members;

class PennyAuction extends AuctionGateway
{
    /** @str */
    const NL = "\n";

    /**
     * Draws auction form
     * @param array $params
     * @return string
     */
    public function drawAuctionForm($params = array())
    {
        $this->registerFiles();
        $auctionForm = AuctionsComponent::drawAuctionForm($params);

        return $auctionForm;
    }

    /**
     * Add bid to the auction
     * @param int $auctionId
     * @param float $nextBid
     * @return mixed
     */
    public function addBid($auctionId = 0, $nextBid = 0.00)
    {
        $status             = false;
        $message            = '';
        $updateData         = '';
        $result             = array();

        $memberId = CAuth::getLoggedRoleId();
        // Check the member is logged in.
        if (empty($memberId)) {
            $status = false;
            $message = A::t('auctions', 'To the place bid you must be logged in!');
        } elseif (empty($auctionId)) {
            // Check auctionId is not empty.
            $status = false;
            $message = A::t('auctions', 'The auction not found! Please try again later.');
        } else {
            // Check the auction exists in the database
            $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', false);
            if ($auction) {
                // Check the auction is active
                $checkActiveAuction = AuctionsComponent::checkActiveAuction($auction->date_from, $auction->date_to, $auction->status);
                $getCurrentWinnerId = BidsHistory::model()->getCurrentWinnerId($auction->id);
                if ($checkActiveAuction['active']) {
                    $bidsAmount = Members::getBidsAmount();
                    $nextBidForm = round($nextBid, 2);
                    $nextBidAuction = round(($auction->current_bid + $auction->size_bid), 2);
                    // Check if member has enough bids for bet
                    if ($bidsAmount < $auction->step_size) {
                        $status = false;
                        $message = A::t('auctions', 'You cannot place a bid! Not have enough bids to make a bet.');
                    // If all checks were successful, we place a bid and update auction.
                    } elseif ($nextBidAuction != $nextBidForm) {
                        $status = false;
                        $message = A::t('auctions', 'You cannot place a bid! Please try again later.');
                    // Check the member is not an auction winner.
                    } elseif ($memberId == $getCurrentWinnerId) {
                        $status = false;
                        $message = A::t('auctions', 'You cannot place a bid. You are the winner of the auction!');
                    } else {
                        // Update bids amount in the member account
                        $newBidsAmount = $bidsAmount - $auction->step_size;
                        $updateMember = Members::setBidsAmount($newBidsAmount);
                        if ($updateMember) {
                            // Add new record in the BidsHistory
                            $bidHistory = new BidsHistory();
                            $bidHistory->auction_type_id = $auction->auction_type_id;
                            $bidHistory->auction_id = $auction->id;
                            $bidHistory->member_id = $memberId;
                            $bidHistory->size_bid = $nextBidForm;
                            $bidHistory->created_at = date('Y-m-d H:i:s');

                            if ($bidHistory->save()) {
                                // Update auction info
                                $updateAuction = Auctions::model()->updateByPk($auction->id, array(
                                    'current_bid' => $nextBidForm,
                                ));
                                if ($updateAuction) {
                                    $bidHistory->refresh();
                                    $status = true;
                                    $message = A::t('auctions', 'The bid for the auction was successful added.');

                                    // Data for updating the auction form and the bid history table
                                    $bidsHistoryInfo = AuctionsComponent::getBidsHistoryInfo($auction->id);

                                    $dateTimeFormat = Bootstrap::init()->getSettings()->datetime_format;
                                    $newBidHtml = '<tr><td>' . (CLocale::date($dateTimeFormat, $bidHistory->created_at)) . '</td><td>' . ($bidHistory->first_name . ' ' . $bidHistory->last_name[0] . '.') . ($bidHistory->member_id == CAuth::getLoggedRoleId() ? '<span class=\"v-menu-item-info bg-success\">' . A::t('auctions', 'It\'s You') . '</span>' : '') . '</td><td>' . (CCurrency::format($bidHistory->size_bid)) . '</td></tr>';

                                    $updateData = '{
                                    "bidsAmount": "' . (!empty($newBidsAmount) ? CHtml::encode($newBidsAmount) : 0.00) . '",
                                    "bids": "' . (!empty($bidsHistoryInfo['count_bids']) ? CHtml::encode($bidsHistoryInfo['count_bids']) : 0) . '",
                                    "bidders": "' . (!empty($bidsHistoryInfo['count_bidders']) ? CHtml::encode($bidsHistoryInfo['count_bidders']) : 0) . '",
                                    "currentBid": "' . (CCurrency::format(CHtml::encode($nextBidForm))) . '",
                                    "nextStep": "' . (CCurrency::format(CHtml::encode($nextBidForm + $auction->size_bid))) . '",
                                    "nextBid": "' . (CHtml::encode($nextBidForm + $auction->size_bid)) . '",
                                    "winner": "' . (!empty($bidsHistoryInfo['winner']) ? $bidsHistoryInfo['winner'] : '') . '",
                                    "winnerId": "' . (!empty($bidsHistoryInfo['winner_id']) ? CHtml::encode($bidsHistoryInfo['winner_id']) : 0) . '",
                                    "newBid": "' . $newBidHtml . '"
                                }';
                                }
                            } else {
                                $status = false;
                                $message = A::t('auctions', 'You cannot place a bid! Please try again later.');
                            }
                        } else {
                            $status = false;
                            $message = A::t('auctions', 'You cannot place a bid! Please try again later.');
                        }
                    }
                } else {
                    $status = false;
                    $message = A::t('auctions', 'Auction not active! Please try again later.');
                }
            } else {
                $status = false;
                $message = A::t('auctions', 'The auction not found! Please try again later.');
            }
        }

        $result[] = '"auctionId": "' . (!empty($auctionId) ? $auctionId : '') . '"';
        $result[] = '"status": "' . ($status ? '1' : '0') . '"';
        $result[] = '"message": "' . $message . '"';
        $result[] = '"updateData": ' . (!empty($updateData) ? $updateData : '""');

        return $result;
    }


    /**
     * Auto Update Auctions
     * @param array $auctionIds
     * @return array
     */
    public function autoUpdateAuction($auctionIds = array())
    {
        $result     = array();
        $status     = 'error';
        $updateData = '';
        $message    = '';

        if (!empty($auctionIds) && is_array($auctionIds)) {
            foreach ($auctionIds as $auctionId => $nextBidForm) {
                $nextBidForm = round($nextBidForm, 2);
                // Check auctionId is not empty.
                if (!empty($auctionId)) {
                    // Check the auction exists in the database
                    $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', false);
                    if ($auction) {
                        // Check the auction is active
                        $activeAuction = AuctionsComponent::checkActiveAuction($auction->date_from, $auction->date_to, $auction->status);
                        if ($activeAuction['active']) {
                            // Check if the auction has changed
                            $nextBidAuction = round(($auction->current_bid + $auction->size_bid), 2);
                            if ($nextBidAuction != $nextBidForm) {
                                $status = 'update';
                                // Data for updating the auction form and the bid history table
                                $bidsHistoryInfo = AuctionsComponent::getBidsHistoryInfo($auction->id);

                                $newBidHtml = AuctionsComponent::getHtmlCurrentBidHistory($bidsHistoryInfo['bids_history'], $auction->current_bid);

                                $updateData = '{
                                    "bids": "' . (!empty($bidsHistoryInfo['count_bids']) ? CHtml::encode($bidsHistoryInfo['count_bids']) : 0) . '",
                                    "bidders": "' . (!empty($bidsHistoryInfo['count_bidders']) ? CHtml::encode($bidsHistoryInfo['count_bidders']) : 0) . '",
                                    "currentBid": "' . (CCurrency::format(CHtml::encode($auction->current_bid))) . '",
                                    "nextStep": "' . (CCurrency::format(CHtml::encode($nextBidAuction))) . '",
                                    "nextBid": "' . (CHtml::encode($nextBidAuction)) . '",
                                    "winner": "' . (!empty($bidsHistoryInfo['winner']) ? $bidsHistoryInfo['winner'] : '') . '",
                                    "winnerId": "' . (!empty($bidsHistoryInfo['winner_id']) ? CHtml::encode($bidsHistoryInfo['winner_id']) : 0) . '",
                                    "newBid": "' . $newBidHtml . '"
                                }';
                            } else {
                                $status = 'no_change';
                            }
                        } else {
                            if ($auction->status !== 3) {
                                $auction->status = 3;
                                $auction->status_changed = CLocale::date('Y-m-d H:i:s');
                                $auction->save();
                            }

                            if ($auction->status == 3) {
                                $status = 'closed';
                                $message = A::t('auctions', 'CLOSED!');
                            } else {
                                $status = 'error';
                                $message = A::t('auctions', 'An error occurred while updating the auction');
                            }
                        }
                    } else {
                        $status = 'error';
                        $message = A::t('auctions', 'An error occurred while updating the auction');
                    }

                    $result[] = '{
                        "auctionId": ' . (!empty($auctionId) ? $auctionId : '') . ',
                        "status": "' . $status . '",
                        "updateData": ' . (!empty($updateData) ? $updateData : '""') . ', 
                        "message": "' . (!empty($message) ? $message : '') . '"
                    }';
                }
            }
        }

        return $result;
    }

    /**
     * Register script and style files
     */
    public function registerFiles()
    {
        A::app()->getClientScript()->registerScriptFile('assets/modules/auctions/js/penny.js', 2);
    }

}

