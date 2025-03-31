# BiglyPay Crypto Payment Gateway for WHMCS

BiglyPay is a cryptocurrency payment gateway module for WHMCS that enables your business to accept crypto payments seamlessly. This module monitors cryptocurrency transactions, updates invoice statuses (e.g., Partially Paid or Fully Paid), and logs transaction details in WHMCS. For Fees check https://biglypay.com/#Fees

## Features

- **Seamless Integration:** Easily integrate with WHMCS via the native gateways system.
- **Multi-Crypto Support:** Accept payments in various cryptocurrencies (e.g., ETH, BNB, BIGLY, USDT, DOGE, etc.) using BiglyPay’s infrastructure.
- **Automatic Invoice Updates:** Invoice statuses are updated automatically based on transaction monitoring.
- **Transaction Logging:** All transaction details are recorded in the WHMCS transactions table.
- **Centralized Settings:** API key, endpoint, and callback settings are managed directly through the BiglyPay control panel.

## Requirements

- **WHMCS:** Version 7.0 or higher.
- **PHP:** Version 7.2 or above.
- **MySQL:** For storing transactions and invoice data.

## Installation

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/BiglyBucks/BiglyPay-WHMCS.git
   ```

2. **Copy Files:**

   Copy the contents of the `modules/gateways/` folder from the repository into your WHMCS installation directory at `modules/gateways/`.

3. **Set Permissions:**

   Ensure that all module files have the correct file permissions so they are readable by your web server.

## Activate and Configure the Gateway Module

1. **Activate the Module:**

   - Log in to your WHMCS admin area.
   - Navigate to **Setup > Payments > Payment Gateways**.
   - Under the “Payment Gateways” tab, locate and activate **Crypto Payment (BiglyPay)**.

2. **Configure Module Settings:**

   Once activated, you will see the following configuration options:

   - **API Key:**  
     Enter the API Key for your backend server. This key is required to authenticate your transactions. (Obtain your API Key from the BiglyPay control panel.)

   - **IPN Key:**  
     Provide the IPN Key used for secure callback validation.

   - **Company Logo:**  
     Enter the URL or path of the logo that will appear on the remote checkout page.

   - **Payment Mode:**  
     Select the payment mode:  
     - **Inline:** Displays payment details directly within WHMCS.  
     - **Remote:** Redirects your customers to a hosted BiglyPay payment page.

   These settings allow you to customize how BiglyPay interacts with your WHMCS installation. For further configuration—such as managing your API keys and callbacks—please visit the BiglyPay control panel at [https://biglypay.com/settings.php](https://biglypay.com/settings.php).

## Usage

Once activated, the BiglyPay module will:

- Monitor cryptocurrency transactions associated with each invoice.
- Automatically update invoice statuses (e.g., "Partially Paid" or "Fully Paid") based on the payment amount received.
- Log all transaction details in the WHMCS transactions table for future reference.

## Contributing

Contributions are welcome! If you have suggestions or improvements, please open an issue or submit a pull request. Be sure to follow the project’s coding standards and include appropriate tests for any new functionality.

## License

This project is licensed under the MIT License. See the LICENSE file for further details.

## Support

For additional help or inquiries, please visit the BiglyPay control panel or contact support via the BiglyPay website.
```
