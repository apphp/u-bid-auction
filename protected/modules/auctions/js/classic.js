var $ = jQuery;

/*
|--------------------------------------------------------------------------
| ClassicAuction Object
|--------------------------------------------------------------------------
*/
function ClassicAuction() {

    //define this to enable debugging(true|false)
    this.is_debug = false;

    this.el = '';
    this.auctionId = '';
    this.nextBid = '';
    this.button = '';

    /*
	*	Init
	*/
    this.init = function (idEl) {

        this.el = $('#' + idEl);
        this.button = this.el.find('.place-bid');
        this.auctionId = this.el.find('.current-bid').data('auctionId');
        this.nextBid = this.el.find('.current-bid').data('nextBid');

        if (this.button.length == 0) {
            return false
        }

        this.bindEvents();
    };

    /*
	*	Binds all the events
	*/
    this.bindEvents = function () {

        var self = this;

       // Init ClassicAuction for click `.classic-auction .place-bid`
       this.button.on("click", function () {
           self.addBid();
       });
    };


    /**
     * Add bid to the auction
     */
    this.addBid = function () {

        this.debug('- ClassicAuction fired addBid()');
        this.debug('    var auctionId = ' + this.auctionId);
        this.debug('    var nextBid = ' + arrClassicAuctions[this.auctionId].nextBid);
        this.debug('------');

        var self = this;
        var nextBid = arrClassicAuctions[self.auctionId].nextBid;

        // Lock button click
        var disabledButton = self.button.prop("disabled") ? self.button.prop("disabled") : false;
        if (disabledButton) return false;

        // Add picture download
        ajaxLoading(self.button, 'append');

        // Lock button click
        self.button.prop("disabled", true);


        // Call Ajax Request
        var ajax = $.ajax({
            url: 'auctions/ajaxAddBid',
            global: false,
            type: 'POST',
            data: ({
                auctionId: self.auctionId,
                nextBid: nextBid
            }),
            dataType: 'html',
            async: true,
            error: function (html) {
                console.error("AJAX: cannot connect to the server or server response error!");
            },
            success: function (html) {
                try {
                    var obj = $.parseJSON(html);
                    if (obj.status == "1") {
                        // Delete picture download
                        resetAjaxLoading(self.button);
                        self.debug('success: ' + obj.message);
                        // Auction update after bid
                        self.updateHtml(obj.auctionId, obj.updateData);
                        // Print Success Message
                        apAlert(obj.message, 'success');
                        // Unlock button click
                        self.button.prop("disabled", false);
                    } else {
                        // Delete picture download
                        resetAjaxLoading(self.button);
                        // Print Error Message
                        apAlert(obj.message, obj.message_type);
                        // Unlock button click
                        self.button.prop("disabled", false);
                        self.debug('error: ' + obj.message);
                    }
                } catch (err) {
                    console.error(err);
                }
            }
        });

        return ajax;
    };

    /**
     * Update value in the auction form
     */
    this.updateHtml = function (auctionId, auctionFormData) {

        var self = this;

        var bids         = auctionFormData.bids;
        var bidders      = auctionFormData.bidders;
        var currentBid   = auctionFormData.currentBid;
        var nextStep     = auctionFormData.nextStep;
        var nextBid      = auctionFormData.nextBid;
        var winner       = auctionFormData.winner;
        var winnerId     = auctionFormData.winnerId;
        var newBid       = auctionFormData.newBid;
        var classAuction = '.auction-' + auctionId;

        self.debug('- ClassicAuction fired updateHtml()');
        self.debug('    var bids = ' + bids);
        self.debug('    var bidders = ' + bidders);
        self.debug('    var currentBid = ' + currentBid);
        self.debug('    var nextStep = ' + nextStep);
        self.debug('    var nextBid = ' + nextBid);
        self.debug('    var winner = ' + winner);
        self.debug('    var winnerId = ' + winnerId);
        self.debug('    var newBid = ' + newBid);
        self.debug('    var classAuction = ' + classAuction);
        self.debug('------');

        // Update nextИid in arrClassicAuctions
        if ((nextBid !== null || nextBid !== undefined)) {
            arrClassicAuctions[auctionId].nextBid = nextBid;
        }

        var auctionForm = $('#auction-form .classic-auction'+classAuction);
        // Updating data after bids on the auction page
        if (auctionForm.length > 0) {
            //Update Data In the auction form
            if ((bids !== null || bids !== undefined)) {
                $('.classic-auction'+classAuction+' #bids').text(bids);
            }
            if ((bidders !== null || bidders !== undefined)) {
                $('.classic-auction'+classAuction+' #bidders').text(bidders);
            }
            if ((currentBid !== null || currentBid !== undefined)) {
                this.animatedPriceChange($('.classic-auction'+classAuction+' .current-bid'), currentBid, "50px");
            }
            if ((nextStep !== null || nextStep !== undefined)) {
                $('.classic-auction'+classAuction+' #next_step').text(nextStep);
            }
            if ((winner !== null || winner !== undefined)) {
                $('.classic-auction'+classAuction+' #winner').text(winner);
            }
            if ((winnerId !== null || winnerId !== undefined)) {
                $('.classic-auction'+classAuction+' #winner_id').text(winnerId);
            }
            if ((newBid !== null || newBid !== undefined)) {
                $('#bids_history_table tbody').prepend(newBid);
                $('#bid_history_info').hide();
            }
        } else {
            //Update Data In the homepage and similar auction and categories page
            if ((currentBid !== null || currentBid !== undefined)) {
                this.animatedPriceChange($('.classic-auction'+classAuction+' .current-bid'), currentBid, "20px");
            }
        }
    };


    // Animated price change
    this.animatedPriceChange = function (el, text, fontSize) {

        var oldFontSize = el.css("font-size");

        el.text(text)
        .animate({color: "#ff0000", fontSize: fontSize}, 500)
        .animate({color: "#4ba500", fontSize: oldFontSize}, 500);

    };

    this.debug = function (text) {
        if (this.is_debug == true) {
            console.log(text);
        }
    };

} // ClassicAuction Object


/*
|--------------------------------------------------------------------------
| ClassicAuction Object
|--------------------------------------------------------------------------
*/
function AutoUpdateClassicAuction() {

    // Update interval
    this.interval = 3000;
    this.errorUpdate = false;
    this.timerUpdateAuction = '';

    /**
     * Update Auction Form
     */
    this.updateAuction = function () {

        // Exit if an error occurred
        if (this.errorUpdate) {
            return false;
        }

        var self = this;
        var auctionList = {};
        var max_count_auctions = 0;
        var i = 0;


        $.each(arrClassicAuctions, function( index, value ) {
            if (arrClassicAuctions[index] !== null && arrClassicAuctions[index] !== undefined && arrClassicAuctions[index] !== '') {
                auctionList[value.auctionId] = value.nextBid;
            }
        });

        var auctionsJson = JSON.stringify(auctionList);

        // Exit if no auctions were found on the page
        if (auctionsJson == null || auctionsJson === '') {
            return false;
        }

        // Call Ajax Request
        var ajax = $.ajax({
            url: 'auctions/ajaxAutoUpdateAuction',
            global: false,
            type: 'POST',
            data: ({
                auctions: auctionsJson
            }),
            dataType: 'html',
            async: true,
            error: function (html) {
                console.error("AJAX: cannot connect to the server or server response error!");
            },
            success: function (html) {
                try {
                    var obj = $.parseJSON(html);
                    //Run in a loop the data received from the server
                    for (i = 0, max_count_auctions = obj.length; i < max_count_auctions; i++) {
                        if (obj[i].status == "update") {
                            // Auction update
                            if (obj[i].updateData !== null && obj[i].updateData !== undefined) {
                                var auction = new ClassicAuction();
                                arrClassicAuctions[obj[i].auctionId].updateHtml(obj[i].auctionId, obj[i].updateData);
                            }
                        } else if (obj[i].status == "closed") {
                            // Close auction
                            var classAuction = '.auction-' + obj[i].auctionId;
                            var auctionForm  = $('#auction-form .classic-auction'+classAuction);
                            var bidButton    = $('.classic-auction'+classAuction+' .place-bid');

                            delete arrClassicAuctions[obj[i].auctionId];

                            // Close auction in the auction page
                            if (auctionForm.length > 0) {
                                var errorMessage = '<p class="closed-auction mb5">' + obj[i].message + '</p>';

                                auctionForm.find("#timer-label").remove();
                                auctionForm.find(".timer").remove();
                                auctionForm.find("#current-bid-label").remove();
                                auctionForm.find(".current-bid").remove();
                                auctionForm.find(".bid-now-link").remove();
                                auctionForm.find("#buy-now").closest("div").remove();
                                auctionForm.find("#watchlist").closest("div").removeClass().addClass("col-xs-6 col-xs-offset-3 col-sm-12 col-md-6 col-md-offset-3 pb20");

                                auctionForm.find(".bb1").after(errorMessage);
                            } else if (bidButton.length > 0) {
                                // Close auction in the homepage
                                var errorMessage = '<p class="closed-auction mb5">' + obj[i].message + '</p>';
                                $('.classic-auction'+classAuction+' .current-bid').remove();
                                $('.classic-auction'+classAuction+' .bid-now-link').append(errorMessage);
                                bidButton.remove();
                            } else {
                                // Close auction in the similar auction and categories page
                                var errorMessage = obj[i].message;
                                $('.classic-auction'+classAuction+' .current-bid').text(errorMessage).addClass("error");
                            }

                        } else if (obj[i].status == "error") {
                            // Stop updateAuction function
                            stopAutoUpdate();
                            // Display a pop-up window to reload the page.
                            if (confirm(obj[i].message)) {
                                window.location.reload();
                            }
                        }
                    }
                } catch (err) {
                    console.error(err);
                }
            }
        });

        return ajax;
    };

    /**
     * Run Auto Update
     */
    this.run = function () {
        this.timerUpdateAuction = setInterval(this.updateAuction, this.interval);
    };

    /**
     * Stop Auto Update
     */
    function stopAutoUpdate () {
        this.errorUpdate = true;
        clearInterval(this.timerUpdateAuction);
    }

} // AutoUpdateClassicAuction Object

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/
var arrClassicAuctions = [];
$(document).ready(function () {
    // Init ClassicAuction for each `.classic-auction`
    $(".classic-auction").each(function (i) {
        if ($(this).attr('id') != undefined) {
            var auction = new ClassicAuction(this);
            auction.init($(this).attr('id'));
            if (arrClassicAuctions[auction.auctionId] === null || arrClassicAuctions[auction.auctionId] === undefined || arrClassicAuctions[auction.auctionId] === '') {
                arrClassicAuctions[auction.auctionId] = auction;
            }
        }
    });

    // Run AutoUpdateClassicAuction
    var autoUpdate = new AutoUpdateClassicAuction();
    autoUpdate.run();
});