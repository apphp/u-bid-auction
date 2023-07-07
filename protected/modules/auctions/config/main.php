<?php

return array(
    // Module classes
    // Full class name with namespace => Class name
    'classes' => array(
        'Modules\Auctions\Components\AuctionsComponent',
        'Modules\Auctions\Controllers\AuctionImages',
        'Modules\Auctions\Controllers\Auctions',
        'Modules\Auctions\Controllers\AuctionTypes',
        'Modules\Auctions\Controllers\BidsHistory',
        'Modules\Auctions\Controllers\Categories',
        'Modules\Auctions\Controllers\Checkout',
        'Modules\Auctions\Controllers\Home',
        'Modules\Auctions\Controllers\Members',
        'Modules\Auctions\Controllers\Orders',
        'Modules\Auctions\Controllers\Packages',
        'Modules\Auctions\Controllers\Reviews',
        'Modules\Auctions\Controllers\Shipments',
        'Modules\Auctions\Controllers\Statistics',
        'Modules\Auctions\Controllers\Taxes',
    ),

    // Management links
    'managementLinks' => array(
        A::t('auctions', 'Categories') => 'categories/manage',
        A::t('auctions', 'Auction Types') => 'auctionTypes/manage',
        A::t('auctions', 'Auctions') => 'auctions/manage',
        A::t('auctions', 'Members') => 'members/manage',
        A::t('auctions', 'Packages') => 'packages/manage',
        A::t('auctions', 'Taxes') => 'taxes/manage',
        A::t('auctions', 'Orders') => 'orders/manage',
        A::t('auctions', 'Statistics') => 'statistics/manage',
    ),
);
