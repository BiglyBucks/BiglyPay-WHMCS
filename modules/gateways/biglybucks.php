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
        ],
        'paymentMode' => [
            'FriendlyName' => 'Payment Mode',
            'Type'         => 'radio',
            'Options'      => 'Inline,Remote',
            'Default'      => 'Inline',
            'Description'  => 'Select Inline to display payment details on WHMCS or Remote to redirect to a hosted BiglyPay payment page.',
        ],
    ];
}

function biglypay_link($params)
{
    $email         = $params['clientdetails']['email'];
    $invoiceId     = $params['invoiceid'];
    $amount        = $params['amount'];
    $currency      = $params['currency'];
    $callbackUrl   = $params['systemurl'] . '/modules/gateways/callback/biglypay.php';
    $apiKey        = $params['apiKey'];
	$returnurl	   = $params['systemurl']. '/viewinvoice.php?id='.$params["invoiceid"];
    $logo        = $params['clogo'];
    $bscBackendUrl = "https://pay-01.biglybucks.com";
    $ethBackendUrl = "https://pay-02.biglybucks.com";
    $whmcsUrl      = $params['systemurl'];
    $parse         = parse_url($whmcsUrl);
    $domain        = $parse['host'];
    $paymentMode   = isset($params['paymentMode']) ? $params['paymentMode'] : 'Inline';

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


    // --- Inline Payment Mode (existing interface) ---
    // Define token groups for Binance Smart Chain (BSC) and Ethereum Mainnet.
    $bsc_tokens = [
        "BNB" => [
            "image"    => "images/bnb-logo.png",
            "contract" => "0x0000000000000000000000000000000000000000"
        ],
        "USDT" => [
            "image"    => "images/usdt-logo.png",
            "contract" => "0x55d398326f99059fF775485246999027B3197955"
        ],
        "BIGLY" => [
            "image"    => "images/bigly-logo.png",
            "contract" => "0x90FA309Fd3C9EF44572Db05DCb500A0aC53eB340"
        ],
        "BABYDOGE" => [
            "image"    => "images/babydoge-logo.jpg",
            "contract" => "0xc748673057861a797275cd8a068abb95a902e8de"
        ],
        "DOGE" => [
            "image"    => "images/doge-logo.png",
            "contract" => "0xba2ae424d960c26247dd6c32edc70b295c744c43"
        ],
        "FLOKI" => [
            "image"    => "images/floki-logo.png",
            "contract" => "0xfb5b838b6cfeedc2873ab27866079ac55363d37e"
        ],
        "PEPE" => [
            "image"    => "images/pepe-logo.jpeg",
            "contract" => "0x25d887ce7a35172c62febfd67a1856f20faebb00"
        ],
        "PITBULL" => [
            "image"    => "images/pitbull-logo.png",
            "contract" => "0xA57ac35CE91Ee92CaEfAA8dc04140C8e232c2E50"
        ],
    ];

    $eth_tokens = [
        "ETH" => [
            "image"    => "images/eth-logo.png",
            "contract" => "0x0000000000000000000000000000000000000000"
        ],
        "USDT" => [
            "image"    => "images/usdt-logo.png",
            "contract" => "0xdAC17F958D2ee523a2206206994597C13D831ec7"
        ],
        "USDC" => [
            "image"    => "images/usdc-logo.png",
            "contract" => "0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48"
        ],
        "DAI" => [
            "image"    => "images/dai-logo.png",
            "contract" => "0x6B175474E89094C44Da98b954EedeAC495271d0F"
        ],
        "SHIB" => [
            "image"    => "images/shib-logo.png",
            "contract" => "0x95aD61b0a150d79219dCF64E1E6Cc01f0B64C4cE"
        ]
    ];

    // Build the hierarchical token dropdown.
    $html = "<style>
      .custom-dropdown { position: relative; display: inline-block; width: 100%; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; padding: 8px; background-color: #fff; }
      .custom-dropdown-options { position: absolute; background-color: #fff; border: 1px solid #ccc; width: 100%; z-index: 1000; max-height: 600px; min-width:360px; overflow-y: auto; display: none; }
      .custom-dropdown-option { padding: 8px; text-align: left;cursor: pointer; }
      .custom-dropdown-option:hover { background-color: #f1f1f1; }
      .custom-dropdown-option img { width: 20px; vertical-align: middle; margin-right: 5px; }
      .custom-dropdown-header { padding: 8px; font-weight: bold; background-color: #e9ecef; cursor: default; }
    </style>";

    $html .= "<div class='text-center' id='paymentBox' style='padding:15px; border:2px solid #f4c542; border-radius:10px; background:#fff; min-width:400px;'>
      <p style='font-size:14px;'><strong>Enter your Source Address:</strong></p>
      <input type='text' class='form-control' id='userWallet' placeholder='Enter your source wallet address here' style='margin-bottom:10px;'>
      
      <h5 style='color:#f4c542; font-weight:bold;'>Select a Token to Pay</h5>
      <div id='customTokenDropdown' class='custom-dropdown'>-- Select Token --</div>
      <div id='customTokenOptions' class='custom-dropdown-options'>";
      
    // Output headers and options for BSC tokens.
    $html .= "<div class='custom-dropdown-header'>Binance Smart Chain</div>";
    foreach ($bsc_tokens as $token => $data) {
        $image = $data["image"];
        $contract = $data["contract"];
        $html .= "<div class='custom-dropdown-option' data-token='{$token}' data-icon='{$image}' data-contract='{$contract}' data-network='BSC'>
          <img src='{$image}' /> {$token}
        </div>";
    }
    // Output headers and options for ETH tokens.
    $html .= "<div class='custom-dropdown-header'>Ethereum Mainnet</div>";
    foreach ($eth_tokens as $token => $data) {
        $image = $data["image"];
        $contract = $data["contract"];
        $html .= "<div class='custom-dropdown-option' data-token='{$token}' data-icon='{$image}' data-contract='{$contract}' data-network='ETH'>
          <img src='{$image}' /> {$token}
        </div>";
    }
      
    $html .= "</div>

      <div id='invoiceDetails' style='display:none; margin-top:15px;'>
        <center><img id='tokenImage' src='' alt='' style='max-width:150px; margin-bottom:10px;'></center>
        <h5 style='color:#f4c542; font-weight:bold;'>Send Payment with <span id='tokenName'></span>
          <small><p class='token-contract' id='tokenContract'></p></small>
        </h5>
        <p style='font-size:14px; color:#555; margin-top:25px;'>
          <strong>Amount to Send:</strong> <span id='tokenAmount'></span> <span id='tokenSymbol'></span>
        </p>
        <p id='feeDisplay' style='font-size:14px; color:#555;'></p>
        <p style='margin-top:35px; font-size:14px; font-weight:bold; color:#555;'>Send to this Destination:</p>
        <input type='text' class='form-control text-center' id='paymentAddress' readonly>
        <button onclick='copyAddress()' class='btn btn-warning' style='margin-top:5px;'>Copy Address</button>
        <p style='margin-top:25px; margin-bottom:0px;'>Scan to Pay:</p>
        <center><img id='qrCode' src='' alt='QR Code' style='margin-top:0px;'/></center>
      </div>
      
      <script>
        document.addEventListener('DOMContentLoaded', function(){
          var dropdown = document.getElementById('customTokenDropdown');
          var optionsDiv = document.getElementById('customTokenOptions');
          var selectedToken = '';
          var selectedNetwork = '';
          dropdown.addEventListener('click', function(e){
            e.stopPropagation();
            optionsDiv.style.display = (optionsDiv.style.display === 'none' || optionsDiv.style.display === '') ? 'block' : 'none';
          });
          var optionItems = optionsDiv.getElementsByClassName('custom-dropdown-option');
          for(var i = 0; i < optionItems.length; i++){
            optionItems[i].addEventListener('click', function(e){
              selectedToken = this.getAttribute('data-token');
              selectedNetwork = this.getAttribute('data-network');
              var iconUrl = this.getAttribute('data-icon');
              dropdown.innerHTML = '<img src=\"' + iconUrl + '\" style=\"width:20px; vertical-align:middle; margin-right:5px;\" /> ' + selectedToken;
              optionsDiv.style.display = 'none';
              var contractLink = formatContract(this.getAttribute('data-contract'), selectedNetwork);
              document.getElementById('tokenContract').innerHTML = contractLink;
              confirmAndFetch(selectedToken, selectedNetwork);
            });
          }
          document.addEventListener('click', function(e){
            if(!dropdown.contains(e.target) && !optionsDiv.contains(e.target)){
              optionsDiv.style.display = 'none';
            }
          });
          document.getElementById('userWallet').addEventListener('change', function(){
            if(selectedToken !== ''){
              confirmAndFetch(selectedToken, selectedNetwork);
            }
          });
        });
        
        function formatContract(contract, network){
          var baseUrl = (network === 'ETH') ? 'https://etherscan.io/token/' : 'https://bscscan.com/token/';
          if(contract.toLowerCase() === '0x0000000000000000000000000000000000000000'){
            return (network === 'ETH') ? '(ETH)' : '(BNB)';
          }
          if(contract.length < 10) return contract;
          var start = contract.substring(0, 6);
          var end = contract.substring(contract.length - 4);
          var short = start + '...' + end;
          return '(CA: <a href=\"' + baseUrl + contract + '\" target=\"_blank\">' + short + '</a>)';
        }
        
        function confirmAndFetch(token, network){
          var userWallet = document.getElementById('userWallet').value;
          if(userWallet === ''){
            alert('Please enter your source address first.');
            return;
          }
          if(!confirm('You entered source address: ' + userWallet + '. Is this correct?')){
            return;
          }
          fetchNewAddress(token, network);
        }
        
        function fetchNewAddress(token, network){
          var userWallet = document.getElementById('userWallet').value;
          if(userWallet === ''){
            alert('Please enter your source address first.');
            return;
          }
          var backendUrl;
          if(network === 'ETH'){
            backendUrl = '".$ethBackendUrl."';
          } else {
            backendUrl = '".$bscBackendUrl."';
          }
          var xhr = new XMLHttpRequest();
          xhr.open('POST', backendUrl + '/create_invoice/', true);
          xhr.setRequestHeader('Content-Type', 'application/json');
          xhr.setRequestHeader('Authorization', 'Bearer $apiKey');
          xhr.setRequestHeader('secretkey', '$apiKey');
          xhr.setRequestHeader('whmcsdomain', '$domain');
          xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
              var response = JSON.parse(xhr.responseText);
              document.getElementById('paymentAddress').value = response.destination_address;
              document.getElementById('tokenAmount').innerText = response.total_required_amount;
              document.getElementById('tokenSymbol').innerText = response.token_symbol;
              document.getElementById('qrCode').src = 'https://quickchart.io/qr?size=200x200&text=' + encodeURIComponent(response.destination_address);
              document.getElementById('tokenName').innerText = response.token_symbol;
              var tokenImg = document.getElementById('tokenImage');
              var dropdownImg = document.querySelector('#customTokenDropdown img');
              if(dropdownImg){
                tokenImg.src = dropdownImg.src;
              }
              var feeText = 'Included Fee: $0.50 worth of ' + response.token_symbol;
              document.getElementById('feeDisplay').innerText = feeText;
              document.getElementById('invoiceDetails').style.display = 'block';
            }
          };
          xhr.send(JSON.stringify({
              user_id: {$params['clientdetails']['userid']},
              whmcs_invoice_id: {$invoiceId},
              invoice_amount_usd: {$amount},
              whmcs_user_email: '{$email}',
              token: token,
              source_address: userWallet
          }));
        }
        
        function copyAddress(){
          var copyText = document.getElementById('paymentAddress');
          copyText.select();
          copyText.setSelectionRange(0, 99999);
          document.execCommand('copy');
          alert('Payment Address copied to clipboard!');
        }
      </script>
    </div>";

    return $html;
}
?>
