<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function biglybucks_MetaData()
{
    return [
        'DisplayName' => 'BiglyPay Payment Gateway',
        'APIVersion' => '1.1',
        'DisableLocalCreditCardInput' => true,
        'LogoUrl' => 'https://biglybucks.com/logo-250.png',
        'Author' => 'BiglyBucks',
    ];
}

function biglybucks_config()
{
    return [
        'FriendlyName' => [
            'Type'  => 'System',
            'Value' => 'Crypto Payment ($bigly)',
        ],
        'Description' => [
            'Type'  => 'System',
            'Value' => 'Accept payments using BiglyPay (BIGLY) with automatic verification. Customers can pay securely via blockchain transactions.',
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
        'backendUrl' => [
            'FriendlyName' => 'Backend URL',
            'Type'         => 'text',
            'Size'         => '100',
            'Default'      => 'https://pay-01.biglybucks.com',
            'Description'  => 'Enter the URL of the BiglyPay backend server, default: https://pay-01.biglybucks.com',
        ],
    ];
}

function biglybucks_link($params)
{
    $invoiceId   = $params['invoiceid'];
    $amount      = $params['amount'];
    $currency    = $params['currency'];
    $callbackUrl = $params['systemurl'] . '/modules/gateways/callback/biglybucks.php';
    $apiKey      = $params['apiKey'];
    $backendUrl  = rtrim($params['backendUrl'], '/');
    $whmcsUrl    = $params['systemurl'];
    $parse       = parse_url($whmcsUrl);
    $domain      = $parse['host'];
    
    // Define tokens with image and contract address.
    $tokens = [
        "BNB" => [
            "image"    => "images/bnb-logo.png",
            "contract" => "0x0000000000000000000000000000000000000000"
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
    
    // Build the HTML output.
    $html = "
    <style>
      /* Custom Dropdown Styles */
      .custom-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        padding: 8px;
        background-color: #fff;
      }
      .custom-dropdown-options {
        position: absolute;
        background-color: #fff;
        border: 1px solid #ccc;
        width: 100%;
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        display: none;
      }
      .custom-dropdown-option {
        padding: 8px;
        text-align: left;
      }
      .custom-dropdown-option:hover {
        background-color: #f1f1f1;
      }
      .custom-dropdown-option img {
        width: 20px;
        vertical-align: middle;
        margin-right: 5px;
      }
      .token-contract {
        font-size: 12px;
        color: #555;
        margin-top: 5px;
      }
      .token-contract a {
        color: #007bff;
        text-decoration: none;
      }
      .token-contract a:hover {
        text-decoration: underline;
      }
    </style>

    <div class='text-center' id='paymentBox' style='padding:15px; border:2px solid #f4c542; border-radius:10px; background:#fff; min-width:400px;'>
      <p style='font-size:14px;'><strong>Enter your Source Address:</strong></p>
      <input type='text' class='form-control' id='userWallet' placeholder='Enter your source wallet address here' style='margin-bottom:10px;'>
      
      <h5 style='color:#f4c542; font-weight:bold;'>Select a Token to Pay</h5>
      <!-- Custom Dropdown -->
      <div id='customTokenDropdown' class='custom-dropdown'>-- Select Token --</div>
      <div id='customTokenOptions' class='custom-dropdown-options'>";
    foreach ($tokens as $token => $data) {
        $image    = $data["image"];
        $contract = $data["contract"];
        $html .= "<div class='custom-dropdown-option' data-token='{$token}' data-icon='{$image}' data-contract='{$contract}'>
                    <img src='{$image}' /> {$token}
                  </div>";
    }
    $html .= "
      </div>

      <div id='invoiceDetails' style='display:none; margin-top:15px;'>
        <center><img id='tokenImage' src='' alt='' style='max-width:150px; margin-bottom:10px;'></center>
        <h5 style='color:#f4c542; font-weight:bold;'>Send Payment with <span id='tokenName'></span><small><p class='token-contract' id='tokenContract'></p></small></h5>
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
        // Custom dropdown handling using vanilla JavaScript.
        document.addEventListener('DOMContentLoaded', function(){
          var dropdown = document.getElementById('customTokenDropdown');
          var optionsDiv = document.getElementById('customTokenOptions');
          var selectedToken = \"\";
          var selectedContract = \"\";
          
          // Ensure options are hidden initially.
          optionsDiv.style.display = 'none';
          
          dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            // Toggle options visibility.
            if(optionsDiv.style.display === 'none' || optionsDiv.style.display === ''){
               optionsDiv.style.display = 'block';
            } else {
               optionsDiv.style.display = 'none';
            }
          });
          
          // Add click event to each option.
          var optionItems = optionsDiv.getElementsByClassName('custom-dropdown-option');
          for(var i = 0; i < optionItems.length; i++){
            optionItems[i].addEventListener('click', function(e) {
              selectedToken = this.getAttribute('data-token');
              selectedContract = this.getAttribute('data-contract');
              var iconUrl = this.getAttribute('data-icon');
              dropdown.innerHTML = '<img src=\"' + iconUrl + '\" style=\"width:20px; vertical-align:middle; margin-right:5px;\" /> ' + selectedToken;
              optionsDiv.style.display = 'none';
              // Show the contract link below the token name.
              var contractLink = formatContract(selectedContract);
              document.getElementById('tokenContract').innerHTML = contractLink;
              // Ask for confirmation then trigger invoice fetch.
              confirmAndFetch(selectedToken);
            });
          }
          
          // Hide dropdown if clicking outside.
          document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !optionsDiv.contains(e.target)) {
              optionsDiv.style.display = 'none';
            }
          });
          
          // When source address changes, if a token is selected, re-confirm and re-fetch invoice details.
          document.getElementById('userWallet').addEventListener('change', function() {
            if(selectedToken !== \"\") {
              confirmAndFetch(selectedToken);
            }
          });
        });
        
        // Format the contract address as \"start...end\" and return a clickable link.
        function formatContract(contract) {
          if(contract.toLowerCase() === '0x0000000000000000000000000000000000000000') {
            return '(BNB)';
          }
          if(contract.length < 10) return contract;
          var start = contract.substring(0, 6);
          var end = contract.substring(contract.length - 4);
          var short = start + '...' + end;
          var url = 'https://bscscan.com/token/' + contract;
          return '(CA: <a href=\"' + url + '\" target=\"_blank\">' + short + '</a>)';
        }
        
        // Ask for confirmation of the source address.
        function confirmAndFetch(token) {
          var userWallet = document.getElementById('userWallet').value;
          if(userWallet === \"\") {
            alert('Please enter your source address first.');
            return;
          }
          var confirmation = window.confirm('You entered source address: ' + userWallet + '. Is this correct?');
          if(confirmation) {
            fetchNewAddress(token);
          }
        }
        
        function fetchNewAddress(token) {
          var userWallet = document.getElementById('userWallet').value;
          if(userWallet === \"\") {
            alert('Please enter your source address first.');
            return;
          }
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '$backendUrl/create_invoice/', true);
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
              // Update main token image if needed.
              var tokenImg = document.getElementById('tokenImage');
              var dropdownImg = document.querySelector('#customTokenDropdown img');
              if(dropdownImg) {
                tokenImg.src = dropdownImg.src;
              }
              // Display fee information based on token selection.
              var feeText = '';
              if(response.token_symbol === 'BNB') {
                feeText = 'Included Fee: 0.0008 BNB';
              } else if(response.token_symbol === 'BIGLY') {
                feeText = 'Included Fee: No Fees';
              } else {
                feeText = 'Included Fee: $0.70 worth of ' + response.token_symbol;
              }
              document.getElementById('feeDisplay').innerText = feeText;
              document.getElementById('invoiceDetails').style.display = 'block';
            }
          };
          xhr.send(JSON.stringify({
              user_id: {$params['clientdetails']['userid']},
              whmcs_invoice_id: {$invoiceId},
              invoice_amount_usd: {$amount},
              token: token,
              source_address: userWallet
          }));
        }
        
        function copyAddress() {
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
