# Ifthenpay Joomla/Virtuemart payment gateway

Read this in ![Português](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/pt.png) [Português](readme.pt.md), and ![Inglês](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/en.png) [Inglês](readme.md)

[1. Introduction](#Introduction)

[2. Compatibility](#Compatibility)

[2. Installation](#Installation)

[3. Configuration](#Configuration)

[4. Customer usage experience](#Customer-usage-experience)


# Introduction
![Ifthenpay](https://ifthenpay.com/images/all_payments_logo_final.png)

**This is the Ifthenpay plugin for Joomla with Virtuemart E-Commerce component**

**Multibanco** is one Portuguese payment method that allows the customer to pay by bank reference.
This module will allow you to generate a payment Reference that the customer can then use to pay for his order on the ATM or Home Banking service. This plugin uses one of the several gateways/services available in Portugal, IfthenPay.

**MB WAY** is the first inter-bank solution that enables purchases and immediate transfers via smartphones and tablets.

This module will allow you to generate a request payment to the customer mobile phone, and he can authorize the payment for his order on the MB WAY App service. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Payshop** is one Portuguese payment method that allows the customer to pay by payshop reference.
This module will allow you to generate a payment Reference that the customer can then use to pay for his order on the Payshop agent or CTT. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Credit Card** 
This module will allow you to generate a payment by Visa or Master card, that the customer can then use to pay for his order. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Contract with Ifthenpay is required.**

See more at [Ifthenpay](https://ifthenpay.com). 


# Compatibility

Follow the table below to verify Ifthenpay gateway plugin compatibility with your online store.
|  | Joomla 3 + virtuemart 4 | Joomla 4 + Virtuemart 4 |
|---|---|---|
| Ifthenpay v1.0.0 to v1.0.5  | Compatible | Compatible  |

# Installation

Installation is straightforward:

* click the link for the latest release
![extensions_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/get_latest_release.png)
</br>

* download the zip file in the release Link
![extensions_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/download_installer.png)
</br>

* in Joomla backoffice top menu bar, go to Extensions/Manage/Install
![extensions_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/extensions_install.png)
</br>

* drag the previously downloaded zip to the box that says "Drag and drop file here..."
![drag_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/drag_install.png)
</br>

* in Joomla backoffice top menu bar, go to VirtueMart/Payment Methods
![view_payment_methods](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/view_payment_methods.png)
</br>

* click the " + New " button
![new_payment_method](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/new_payment_method.png)
</br>

* fill the payment method information, and click the "Save" button:
1. **Payment Name** - Set as "Ifthenpay"
2. **Sef Alias** - Set as "Ifthenpay"
3. **Published** - Select "Yes" if you wish to display this payment method on checkout
4. **Payment Description** - Optional
5. **Payment Method** - Select "Ifthenpay Payments"
6. **Shopper Group** - Optional (according to your shop needs)
7. **List Order** - Optional (according to your shop needs)
8. **Currency** - Euro is selected by default, since it is currently the only supported currency

![fill_information](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/fill_information.png)
</br>


# Configuration

* in Joomla backoffice top menu bar, go to VirtueMart/Payment Methods
![view_payment_methods](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/view_payment_methods.png)
</br>

* click the Payment Method you just added
![select_ifthenpay](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/select_ifthenpay.png)
</br>

* open the configuration tab (this tab should be opened by default)
![config_tab](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/config_tab.png)
</br>

* if you still do not have an Ifthenpay account, you may request one by filling the membership contract pdf file downloadable by the "Request an account" button, and send it along with requested documentation to the email ifthenpay@ifthenpay.com
![request_account](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/request_account.png)
</br>

* fill the payment method configuration, and click the "Save" button:
  
1. **Gateway Key** - Input the Gateway Key given on membership contract completion, for example: AAAA-999999 (four uppercase alphabet letters, an hyphen, and six digits)
2. **Anti-phishing Key** - Automatically generated, but you can create one, this must contain a total of 50 alphanumeric characters
3. **Replace Payment Logos** - If you do not wish to display your contracted payment method logos, you may replace these with a string of text
4. **Pending Orders** - Set by default to Pending, but you may change this if necessary
5. **Success Orders** - Set by default to Confirmed, but you may change this if necessary
6. **Failed Orders** - Set by default to Cancelled, but you may change this if necessary
7. **Countries** - Leave empty if you wish to allow this payment method to all countries, or select one or more countries to make this payment method available only the selected ones
8. **Minimum amount** - Leave empty if you wish to allow any amount, or input a numeric value to filter the availability of this method by minimum order total amount
9. **Maximum amount** - Leave empty if you wish to allow any amount, or input a numeric value to filter the availability of this method by maximum order total amount
10. **Currency** - Euro is selected by default, since it is currently the only supported currency

![config_first_part](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/config_first_part.png)
</br>

* after saving new fields will be available:

11. **Available Payment Methods** - (read only) will display the logos of the payment methods available on your payment gateway
12. **Activate** - press this button to be redirected to the activation page
13. **Url** - (read only) this is the callback url, you may use it for testing the update of order state
14. **Anti-phishing Key** - (read only) the anti-phishing key you have stored on database

![config_second_part](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/config_second_part.png)
</br>

* to activate your callback press Activate (this is necessary to update the state of the order from pending to confirmed when the customer payment is received)
![config_press_activate](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/press_activate.png)
</br>

* in the callback activation assistant page you will find that the Gateway Key and Anti-Phishing Key values are filled in for you correctly, so you only need to insert your Backoffice Key and press the Ativar button (the Backoffice Key is made of four sets of four digits separated by an hyphen and is given to you when signing contract with Ifthenpay, here is an example of the key: 1111-2222-3333-4444)
![config_activate](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/activate.png)
</br>

* and you are finished configuring your payment plugin, you can try it out from the customer side now.



# Customer usage experience
The following is experienced from the perspective of your customer.
Your shop customer may pay for an order in the following manner:

1. ...after placing an item in the cart and heading to checkout, select the Ifthenpay payment method (this may be shown as one or multiple payment method logos, or only a string of text, depending on your configuration)
2. agree to the Terms of Service
3. click the Confirm Purchase button
![customer_checkout](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/checkout.png)
</br>


(you will be redirected to Ifthenpay Gateway Page)

4. here you may verify the order value to pay
5. select a payment method from the ones available, these will depend on your contract:
  - **ATM (Multibanco)** - Entity, Reference and Value data is given, you may use these to pay either on a public ATM or at you online homebanking
  - **MB WAY** - (This requires that you have the MB WAY app installed on you smartphone) select the callsign of your country and your smartphone number, click the pay button, and you should receive a notification for payment in your MB WAY app
  - **Payshop** - Reference, and Value is given, you may use these to pay at a CTT shop or at any Payshop agent shops
  - **Ccard** - Fill in your credit card info and click the pay button
6. you may print the payment information if you so require, by clicking the "print" button
7. after obtaining your payment data for offline payments methods (ex: Multibanco, and Payshop) or finishing your payment using online payment (ex: MB WAY, and Ccard), you can click the "close" button to return to the shop's Thank you page
![customer_gateway](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/gateway.png)

#### when choosing a payment method you will be presented with the following:

* choosing Multibanco, you will be shown the Entity, Reference, and amount to use in an ATM or online banking app
![customer_gateway_multibanco](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/multibanco.png)
</br>

* choosing MB WAY, you will be prompted to input the phone number, and press pay in to send the notification to the MB WAY app on the smartphone (note: customer must have the app installed in the smartphone associated with the number)
![customer_gateway_mbway](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/mbway.png)
</br>

* choosing Payshop, you will be shown the Payshop reference and the amount to pay, these can be used on any Payshop store/agent
![customer_gateway_payshop](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/payshop.png)
</br>

* choosing Credit Card, you will be redirected to another page where you can fill in your credit card information and press the pay button to finish
![customer_gateway_ccard](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/ccard.png)
</br>
