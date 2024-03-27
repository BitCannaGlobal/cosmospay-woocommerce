# Cosmos Pay

Contributors: BitCanna
Donate link: https://commerce.bitcanna.io
Tags: payments, cryptocurrency, blockchain
Requires at least: 3.0.1
Tested up to: 6.3
Stable tag: 1.0.22
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily accept cryptocurrency payments on your WordPress site. Enable multiple currencies from the interconnected Cosmos ecosystem.


==  Description == 

The Cosmos Pay plugin allows you to accept cryptocurrency payments on your WordPress site. You can enable multiple currencies from the interconnected Cosmos ecosystem with just a few clicks. The plugin works with the Keplr Wallet Browser Extension, and any other wallet that supports the integrated cryptocurrencies.

An up-to-date list of current supported cryptocurrencies can be found on our [documentation website](https://docs.bitcanna.io/v/bitcanna-commerce/the-basics/supported-currencies).


## Installation

For instructions to install/configure the plugin and performing payments, please head over to our [documentation website](https://docs.bitcanna.io/v/bitcanna-commerce/get-started/integrating-with-woocommerce).

## FAQ

For our FAQ we'd like to refer you to our [documentation website](https://docs.bitcanna.io/v/bitcanna-commerce/faq/frequently-asked-questions).

## Screenshots

1. On this page you can add your cryptocurrency addresses to receive payments.
2. In the woocommerce settings of your store you can edit the description of the payment method and select the currencies you would like to accept.
3. Customers can select their desired cryptocurrency and their preferred way to pay. 
4. Simply approve the Keplr Browser Extension pop-up and perform the payment.
5. After just a couple seconds, the payment will be performed and you can view your transaction.

== Changelog ==
= 1.0.22 =
* Add fee calculation of fee by chain

= 1.0.21 =
* Update webpack version from v4 to v5
* Update cosmJs from 0.23.0 to 0.31.3
* Fix bitcanna logo path

= 1.0.19 =
* Fix array_search() method in admin panel

= 1.0.18 =
* Check the payment_method_title to avoid conflicts with other payment methods
* Check the selected_payment_method to remove js call

= 1.0.17 =
* Fix keplr bug
* Change status order

= 1.0.16 =
* Fix call api-store only on checkout page
* Fix mobile view on checkout
* Implementation of BCNAracle on bitcanna

= 1.0.15 =
* Fix bug on default chain selected
* Add message error if no chain is selected in adminCP

= 1.0.14 =
* Add failover on RPC/LCD
* Fix bech32 admin confirmation

= 1.0.13 =
* Add keplr/ledger compatibility
* Fix check balance keplr

= 1.0.12 =
* Fix admin settings

= 1.0.10 =
* Edit link marketplace

= 1.0.9 =
* Fix css front
* Fix address backend

= 1.0.7 =
* Fix multiple css bug
* Add bech32 check address in admin cp
* Added an error page if the plugin configuration is not done

= 1.0.4 =
* Added rights for non-login users to place orders without logging in

= 1.0.3 =
* Edit all README
* Add screenshot of demo

= 1.0.2 =
* Fix some typo
* Edit wordpress banner

= 1.0.1 =
* Fix plugin name
* Edit link admin panel

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.1 =
This version fixes a bug. Upgrade immediately.

