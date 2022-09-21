<div align="center">
  <img src="https://user-images.githubusercontent.com/1071490/190699242-774b353f-a108-4fc0-8ff5-325413c6058f.png">
</div>

Cosmos Pay - WooCommerce
===


## Table of Contents

*   [Requirements](#requirements "Requirements")
*   [Installation](#installation "Installation")
*   [Configuration](#configuration "Configuration")
*   [Use of the module](#use-of-the-module "Use of the module")
*   [Keplr](#keplr "Keplr")

## Requirements

* Last version of WordPress
* WooCommerce installed
* Keplr (client part)

## Installation

Download our script and install it in the plugin folder of your wordpress (by FTP or by the administrator interface)

1. In admin panel, click on activate from Cosmos-pay  
![image](https://user-images.githubusercontent.com/1071490/190692284-4cc43885-d267-45bc-86a9-5050e55c3798.png)

2. Once the plugin is activated, the "Accept disclaimer" link will appear, click on it to configure your BitCanna payment module  
![image](https://user-images.githubusercontent.com/1071490/190692414-3abf40a3-ec3f-4f79-9eb2-d2200a194b13.png)


## Configuration

In this part, we will configure the module to be connected to our blockchain, the configuration is very simple!
In the setting part of the payment module, here are the different points to edit:

![image](https://user-images.githubusercontent.com/1071490/190693320-33c2f09a-808d-49f9-b201-c549fdeef5e0.png)

1. Link to configure your reception address of all chains selected
2. Enable the payment method
3. Title of the payment module that appears in the list of payment choices
4. The description of the payment module
5. Selection of chains to activate for customers


## Use of the module
Once the configuration is done, you can start using your payment module!
To do your test, add a product for a few cents and go through the normal process to buy the product.
When selecting payment, you will see the BitCanna option, like this:

![image](https://user-images.githubusercontent.com/1071490/190694467-15f9ee9d-dba4-44bc-8f32-8cb3e35016b1.png)

Here the verification of the payment is made.
For this, the customer must use Keplr or directly from our webwallet to make the payment and validate the order.

![image](https://user-images.githubusercontent.com/1071490/190695431-c99ed4ef-7a6b-4d0c-9067-2b5a86c4024a.png)

1. The converted amount of the selected chain. The price is retrieved from the CoinGecko api
2. Selection of the chain for the payment, this can be edited in the administration panel
3. Payment method (Keplr or manual)
4. The cancel order button, the customer can cancel his order
5. The user has 1 hour to place his order

Once the payment has been sent, a few seconds later, our system will detect the payment using the memo.
3 checks are made:

1. Verification of the memo
2. Verification of the receiving address
3. Verification of the amount.

Example with Keplr:

![image](https://user-images.githubusercontent.com/1071490/190698105-cc690ebc-454d-41aa-bf86-11c539a5fc2c.png)

Once the payment has been verified and validated, a confirmation message with the link to the transaction will appear!

![image](https://user-images.githubusercontent.com/1071490/190698202-03cb0053-6950-4107-96b0-71f106245593.png)

The order will be validated in the backend, you can now check

![image](https://user-images.githubusercontent.com/1071490/190698646-70e6ee86-7494-4421-8a3d-8745f2ab815a.png)

 
