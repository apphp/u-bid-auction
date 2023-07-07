<?php
return array(
    // Module components
    'components' => array(
        'AuctionsComponent' => array('enable' => true, 'class' => 'AuctionsComponent'),
    ),

    // Url manager (optional)
    'urlManager' => array(
        'rules' => array(
            'checkout/package/([0-9]+)' => 'checkout/package/id/{$0}',
            'checkout/packagePaymentForm/([0-9]+)' => 'checkout/packagePaymentForm/id/{$0}',

            'auctions/view/id/([0-9]+)'       => 'auctions/view/id/{$0}',
            'auctions/view/id/([0-9]+)/(.*?)' => 'auctions/view/id/{$0}',
            'auctions/view/([0-9]+)'          => 'auctions/view/id/{$0}',
            'auctions/view/([0-9]+)/(.*?)'    => 'auctions/view/id/{$0}',
            'auctions/([0-9]+)'               => 'auctions/view/id/{$0}',
            'auctions/([0-9]+)/(.*?)'         => 'auctions/view/id/{$0}',

            'auctions/categories/id/([0-9]+)'        => 'auctions/categories/id/{$0}',
            'auctions/categories/id/([0-9]+)/(.*?)' => 'auctions/categories/id/{$0}',
            'auctions/categories/([0-9]+)'          => 'auctions/categories/id/{$0}',
            'auctions/categories/([0-9]+)/(.*?)'    => 'auctions/categories/id/{$0}',
        ),
    ),

	// Default Backend url (optional, if defined - will be used as application default settings)
	'backendDefaultUrl' => 'auctions/manage',

    // Default settings (optional, if defined - will be used as application default settings)
	//'defaultErrorController' => 'Error',
    'defaultController' => 'home',
    'defaultAction' => 'index',
	
	// Payment complete page
	'paymentCompletePage' => 'checkout/complete',
);
