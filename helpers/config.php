<?php

// plugin

define('IFT_VERSION', '1.0.2');
define('IFT_PLUGIN_TYPE', 'vmpayment');
define('IFT_PLUGIN_NAME', 'ifthenpay');
define('IFT_CMS', 'JOOMLA');

define(
	'IFT_PAYMENT_METHODS',
	array(
		"MULTIBANCO" => "multibanco",
		"MBWAY" => "mbway",
		"PAYSHOP" => "payshop",
		"CCARD" => "ccard"
	)
);


// tokens

define("IFT_TOKEN_GATEWAY", "IFT-KeyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9");



// urls

define("IFT_URL_GATEWAY_CONFIRM_ORDER", "https://ifthenpay.com/api/gateway/plugins/transaction");
define("IFT_URL_GATEWAY_CALLBACK_ACTIVATE", "https://ifthenpay.com/api/gateway/plugins/callback/activate");
define("IFT_URL_GATEWAY_PAYMENT_METHODS", "https://ifthenpay.com/api/gateway/plugins/payments/verify?key=");
define("IFT_URL_GATEWAY_UID_DATA", "https://ifthenpay.com/api/gateway/plugins/transaction/get?id=");
define("IFT_URL_GATEWAY_UPDATE_XML", "https://ifthenpay.com/modulesUpgrade/joomla/upgrade.xml");





// paths, assets

define("IFT_URL_IMAGES_FOLDER", JURI::root() . 'plugins/vmpayment/ifthenpay/assets/images/');