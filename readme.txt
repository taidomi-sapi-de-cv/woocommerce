Domitai is a company that runs a crypto-currency exchange.

== Description ==
Plugin that has the purpose of connecting to the domitai API and allows users of
pages that have woocommerce pay with the crypto currency of your preference (bitcoin,ethereum, etc).

== Installation ==

= Minimum Requirements =

* PHP 7.2 or higher.
* WooCommerce installed and activated.


= Configuration.

* First go to https://domitai.com/ and create an account.
* When you created it, go to My account and then to profile.
* It will redirect you to a page where there is a sidebar, select API.
* In the section "ADD NEW API" choose a name for the API you will use and press save,
When you do this a key will appear at the top, that's the API KEY and below that just press the eye
so you can see the KEY SECRET API.
* Then select Point of Sale and create one with the name of your preference. When you do so,a text like this "point-of-sale name" will appear next to it.
* Activate the sandbox mode and in the "Webhooks in case of successful payment" part, put your base domain then add '/wp-json/wl/v1/webhook'. It would be something like that: https://midominio.com/wp-json/wl/v1/webhook.
* When you have finished creating your API_KEYS and your domitai point of sale, please contact your admin
from wordpress and enter the woocommerce settings.
* Go to the payments tab and activate the bitcoin payment method - Plugin domitai. By doing that,
click on Manage.
* Entering there, you will find different fields, in Domitai KEY you will paste the KEY API of your profile of domitai, in Domitai Key Secret you will paste the KEY SECRET API of your domitai profile and the text that appeared next to them when they created theirs on domitai will be pasted.
* To test if the payments are configured correctly, enable the testnet option,
that way you can make payments with test cryptosystems that have no value.



= 1.2.1 =
1.2.1 is the most current version of the plugin.