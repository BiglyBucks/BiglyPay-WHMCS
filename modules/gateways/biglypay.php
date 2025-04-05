<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function biglypay_MetaData()
{
    return [
        'DisplayName' => 'BiglyPay Payment Gateway',
        'APIVersion' => '1.1',
        'DisableLocalCreditCardInput' => true,
        'LogoUrl' => 'https://biglybucks.com/logo-250.png',
        'Author' => 'BiglyBucks',
    ];
}

function biglypay_config()
{
    return [
        'FriendlyName' => [
            'Type'  => 'System',
            'Value' => 'Crypto Payment (BiglyPay)',
        ],
        'Description' => [
            'Type'  => 'System',
            'Value' => 'Accept payments using BiglyPay with automatic verification. Customers can pay securely via blockchain transactions.',
        ],
        'apiKey' => [
            'FriendlyName' => 'API Key',
            'Type'         => 'text',
            'Size'         => '50',
            'Description'  => 'Enter the API Key for the backend server.',
        ],
        'ipnKey' => [
            'FriendlyName' => 'IPN Key',
            'Type'         => 'text',
            'Size'         => '50',
            'Description'  => 'Enter the IPN Key for secure callback validation.',
        ],
        'clogo' => [
            'FriendlyName' => 'Company Logo',
            'Type'         => 'text',
            'Size'         => '550',
            'Description'  => 'Enter the Logo to show on Remote Checkout.',
        ]
    ];
}

function biglypay_link($params)
{
    $email         = $params['clientdetails']['email'];
    $invoiceId     = $params['invoiceid'];
    $amount        = $params['amount'];
    $currency      = $params['currency'];
    $apiKey        = $params['apiKey'];
	$returnurl	   = $params['systemurl']. '/viewinvoice.php?id='.$params["invoiceid"];
    $logo        = $params['clogo'];
    $whmcsUrl      = $params['systemurl'];
    $parse         = parse_url($whmcsUrl);
    $domain        = $parse['host'];
	$paymentMode   = 'remote';


	if (!filter_var($logo, FILTER_VALIDATE_URL)) {
            unset($logo);
     }


	// --- Remote Payment Mode using POST ---
	if (strtolower($paymentMode) == 'remote') {
		$remoteUrl = "https://biglypay.com/remote_payment.php";
		// Prepare the parameters to post
		$postFields = [
			'userid'     => $params['clientdetails']['userid'],
			'invoiceid'   => $invoiceId,
			'amount'      => $amount,
			'currency'    => $currency,
			'returnurl'   => $returnurl,
			'apiKey'      => $apiKey,
			'logo'		  => $logo,
			'whmcsdomain' => $domain,
			'email'       => $email,
		];

		$html = "<div class='text-center' style='padding:20px;'>
					<form action='{$remoteUrl}' method='POST'>";
		// Create hidden inputs for each post field
		foreach ($postFields as $name => $value) {
			$html .= "<input type='hidden' name='{$name}' value='" . htmlspecialchars($value, ENT_QUOTES) . "' />";
		}
		$html .= "<p>Please click the button below to pay using BiglyPay.</p>
				  <button type='submit' class='btn btn-primary'>Pay with BiglyPay</button>
				  </form>
				 </div>";
		return $html;
	}

}
?>
