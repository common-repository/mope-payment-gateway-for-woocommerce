=== Mopé Payment Gateway ===
Contributors: Vokality
Tags: mope gateway, Hakrinbank, webshop, mope,suriname
Author: Plauto Rafael De Moura Da Silva <rafael.demoura@hakrinbank.com>
Requires at least: 5.1
Tested up to: 6.6.2
Requires PHP: 7.2
Stable tag: 2.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

#Mopé Payment Gateway

== Description ==

# Mopé Payment Gateway for WooCommerce
Seamlessly accept payments from any Mopé wallet on your web shop, the supported currencies are SRD, USD and EUR.

## Requirements
- WordPress
- WooCommerce
- Mopé API Keys (Live Or Test)


## Getting started
- Install plugin
- Activate the plugin through the 'Plugins' menu in your WordPress Dashboard
- Go to WooCommerce > Settings > Payments and enable Mopé Payment Gateway
- Then, click on `Manage` to further customize and setup the Mope payment gateway



## Bugs & Feature requests
If you've found a bug or have a feature request, you can create an [issue](https://github.com/HKBLab/mope/issues)


== Installation ==
## Manual installation

1. Download the plugin 
2. Extra the content of mope-payment-gateway-for-woocommerce.zip 
3. Upload the extracted  mope-payment-gateway-for-woocommerce folder (folder containing all plugin files) to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in your WordPress Dashboard
3. Go to WooCommerce > Settings > Payments and enable Mopé Payment Gateway
4. Then, click on `Manage` to further customize and setup the Mope payment gateway


== Screenshots ==
1. In the add new plugin screen Search for 'Mope' (Install and Activate)
2. Woocommerce > Settings > Payments > Enable Mope Payment Gateway
3. Configure your Mope plugin with API keys and other information. Enjoy!

== Changelog ==

= 2.0.4 =
1. Security:

-Sanitizing inputs (sanitize_text_field).
-Secure API key handling.
-Nonce check could be added to the callback if further needed.
2. Logging:

-Add custom logs for error tracing with WooCommerce’s logger (can be added in the error block).

3. Error Handling:

-Expanded error handling for better user feedback and debugging capabilities.

4. Code Refactoring:

-Code has been reformatted for better readability and maintenance.

5.Update the Payment Processing Logic

= 2.0.3 =
* Plugin has been tested with the latest 3 major releases of WordPress.

= 2.0.2 =
* Security improved
* Assets update
* In the gateway settings there is a new field created for the API Base URL
* Currency support for SRD, USD and EURO

= 2.0.1 =
* Assets update

= 2.0.0 =
* Code Cleanup
* Automatically activate test mode no need to change Base URL in the source code

= 1.0.4 =
* use change WC_API callback structure
* use $order->get_checkout_order_received_url() instead of hardcoded URL

= 1.0.3 =
* This fixes a regression caused by 1.0.2 which constantly caused requests to Mopé to error when issuing a payment request.

= 1.0.2 =
* Prevents a bug from showing up when the response from Mopé is non-200 when creating a payment request.

= 1.0.1 =
* Improve readme.txt file
* Add screenshots and plugin icon

= 1.0.0 =
* initial release

== Upgrade Notice ==

= 2.0.2 =
* Security improved
* Assets update
* In the gateway settings there is a new field created for the API Base URL
* Currency support for SRD, USD and EURO

= 2.0.1 =
* Assets update

= 2.0.0 =
* Code Cleanup
* Automarically activate test mode no need to change Base URL in the source code

= 1.0.4 =
* use change WC_API callback structure
* use $order->get_checkout_order_received_url() instead of hardcoded URL

= 1.0.3 =
* This fixes a regression caused by 1.0.2 which constantly caused requests to Mopé to error when issuing a payment request.

= 1.0.2 =
* Prevents a bug from showing up when the response from Mopé is non-200 when creating a payment request.

= 1.0.1 =
* Improve readme.txt file
* Add screenshots and plugin icon

= 1.0.0 =
* initial release



== Frequently Asked Questions ==

1. Where can I get API keys to use Mope online ?
A = Please send a email to info@mope.sr requesting for the API keys

2. Do I need a Business account to get a Mope API key?
A = Yes. 

3. Do I need to pay to get a Mope Business account?
A = No.

4. How do I get a Mope Business account?
A= Email your request to info@mope.sr, the team will further help you.