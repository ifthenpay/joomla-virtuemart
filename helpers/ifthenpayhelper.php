<?php
defined('_JEXEC') or die('Restricted access');

require_once('config.php');


class Ifthenpayhelper
{

	private static $pluginName = IFT_PLUGIN_NAME;
	private static $pluginType = IFT_PLUGIN_TYPE;





	/**
	 * Get image folder path
	 *
	 * @return void
	 */
	public static function getImgFolderPath()
	{
		return JURI::root() . 'plugins/' . self::$pluginType . '/' . self::$pluginName . '/assets/images/';
	}


	// field utilities

	/**
	 * Get payment method ID, this is the id stored as a vmPaymentMethod, and is necessary for generating the 
	 * url of callback in the config page
	 *
	 * @param string $payment_method_id
	 * @return void
	 */
	public static function getPaymentMethodId(string $payment_method_id)
	{
		$query = "SELECT virtuemart_paymentmethod_id FROM `#__virtuemart_paymentmethods` WHERE  payment_jplugin_id = '" . $payment_method_id . "'";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$paramsStr = $db->loadResult();

		return $paramsStr;
	}


	/**
	 * Get payment method parameters in a custom maner, this is necessary because of how the showlogos
	 * are stored
	 *
	 * @param string $payment_method_id
	 * @return void
	 */
	public static function getPaymentMethodParams(string $payment_method_id)
	{

		$query = "SELECT payment_params FROM `#__virtuemart_paymentmethods` WHERE  payment_jplugin_id = '" . $payment_method_id . "'";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$paramsStr = $db->loadResult();

		$trimmedParamsStr = preg_replace('/[ \t]+/', '', preg_replace('/[\r\n]+/', '', $paramsStr));
		$paramsArr = explode("|", $trimmedParamsStr);

		$newParamArr = [];

		foreach ($paramsArr as $param) {
			if (empty($param)) {
				continue;
			}
			$paramArr = explode('=', $param);

			if (isset($paramArr[0]) && isset($paramArr[1])) { // is valid param


				if (strpos($paramArr[1], "[") !== false) {  // means it is a simple array

					$paramArr[1] = str_replace(array('[', ']'), '', $paramArr[1]);
					$simpleArr = explode(",", $paramArr[1]);

					$tempArr = [];
					foreach ($simpleArr as $saItem) {
						array_push($tempArr, trim($saItem, '"'));
					}

					$newParamArr[$paramArr[0]] = $tempArr;
				} else if (strpos($paramArr[1], "{") !== false) {  // means it is a json object

					$newParamArr[$paramArr[0]] = json_decode($paramArr[1], true);
				} else { // means it is normal value

					$newParamArr[$paramArr[0]] = trim($paramArr[1], '"');
				}
			}
		}

		return $newParamArr;
	}

	/**
	 * generates an url string for the callback
	 *
	 * @return void
	 */
	static function getCallbackUrl()
	{
		$pluginId = self::getPluginId();
		$pm = self::getPaymentMethodId($pluginId);
		return JURI::root()  .  "index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&pm=" . $pm .
			'&oid=[ORDER_ID]' .
			'&apk=[ANTI_PHISHING_KEY]' .
			'&amt=[AMOUNT]' .
			'&pmt=[PAYMENT_METHOD]';
	}

	
	/**
	 * return the anti-phishing key
	 *
	 * @return void
	 */
	static function getGatewayKey()
	{
		$pluginId = self::getPluginId();
		$paymentMethodParams = Ifthenpayhelper::getPaymentMethodParams($pluginId);

		return $paymentMethodParams['gateway_key'];
	}

	/**
	 * return the anti-phishing key
	 *
	 * @return void
	 */
	static function getAntiPhishingKey()
	{
		$pluginId = self::getPluginId();
		$paymentMethodParams = Ifthenpayhelper::getPaymentMethodParams($pluginId);

		return $paymentMethodParams['anti_phishing_key'];
	}



	/**
	 * checks if the gateway key and the anti-phishing key are set in the config. This is used to 
	 * show or hide the callback url in the admin config
	 *
	 * @return boolean
	 */
	static function isGateKeyAntiPhisKeySet(): bool
	{
		$pluginId = self::getPluginId();
		$paymentMethodParams = Ifthenpayhelper::getPaymentMethodParams($pluginId);

		$result = false;

		if (isset($paymentMethodParams['gateway_key']) && 
			  $paymentMethodParams['gateway_key'] != '' && 
			  isset($paymentMethodParams['anti_phishing_key']) && 
			  $paymentMethodParams['anti_phishing_key'] != ''				
				) {
			$result = true;
		}

		return $result;
	}

	
	/**
	 * generates the html for the set of images to display on checkout
	 *
	 * @param string $gatewayKey
	 * @return string
	 */
	static function getPaymentMethodsHtml(string $gatewayKey): string
	{
		$imgFolder = self::getImgFolderPath();
		
		$paymentMethodsArr = self::getPaymentMethodsArr($gatewayKey);
		$html = '';

		if (!empty($paymentMethodsArr)) {

			$pmHtml = '';

			for ($i = 0; $i < count($paymentMethodsArr); $i++) {

				if (isset(IFT_PAYMENT_METHODS[$paymentMethodsArr[$i]])) {
					$pmName = IFT_PAYMENT_METHODS[$paymentMethodsArr[$i]];


					$pmHtml .= '
						<div>
							<img class="img" src="' . $imgFolder . $pmName . '.png" title="' . ucfirst($pmName) . '"/>
						</div>
						';
				}
			}

			$html = '
				<div>
					' . $pmHtml . '				
				</div>
			';
		}

		return $html;
	}

	/**
	 * get array of available methods for given gatewaykey using a webservice
	 *
	 * @param string $gatewayKey
	 * @return array
	 */
	static function getPaymentMethodsArr(string $gatewayKey): array{

		if (!class_exists('curlRequest')) {
			$path = VMPATH_ROOT . '/plugins/vmpayment/ifthenpay/helpers/curlRequest.php';
			if (file_exists($path)) require_once($path);
		}

		$strPaymentMethods = (new CurlRequest())->getAvailablePaymentMethods($gatewayKey);

		$paymentMethodsArr = explode('|', $strPaymentMethods);

		return $paymentMethodsArr;
	}



	/**
	 * get this plugin id
	 *
	 * @return string
	 */
	static function getPluginId()
	{
		$plugin = JPluginHelper::getPlugin(self::$pluginType, self::$pluginName);
		return $plugin->id;
	}

	/**
	 * verifies if there are updates for this plugin
	 *
	 * @return void
	 */
	static function hasUpdate()
	{
		if (!class_exists('curlRequest')) {
			$path = VMPATH_ROOT . '/plugins/vmpayment/ifthenpay/helpers/curlRequest.php';
			if (file_exists($path)) require_once($path);
		}

		$resultXml = (new CurlRequest())->getUpdateXml();
		$latestVersion = self::getVersionFromXml($resultXml);

		return self::isVersionHigher($latestVersion);
	}

	/**
	 * verifies if there are updates for this plugin, but this is not used because it uses internal joomla data
	 *
	 * @return void
	 */
	static function hasUpdate_alternative()
	{
		if (!class_exists('curlRequest')) {
			$path = VMPATH_ROOT . '/plugins/vmpayment/ifthenpay/helpers/curlRequest.php';
			if (file_exists($path)) require_once($path);
		}

		$pluginId = self::getPluginId();

		$query = "SELECT `version` FROM `joom_updates` WHERE  extension_id = " . $pluginId;
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$paramsStr = $db->loadResult();

		if (is_array($paramsStr) || ($paramsStr != '' && $paramsStr != null)) {
			return true;
		}

		return false;
	}


	/**
	 * extract plugin version number from upgrade xml file string
	 *
	 * @param [type] $xml
	 * @return void
	 */
	static function getVersionFromXml($xml)
	{
		$startStr = '<version>';
		$endStr = '</version>';

		$startP = strpos($xml, $startStr) + strlen($startStr);
		$endP = strpos($xml, $endStr);
		$len = $endP - $startP;

		$xmlVersion = substr($xml, $startP, $len);

		return $xmlVersion;
	}

	/**
	 * test if given version is higher than the current one 
	 *
	 * @param [type] $latestVersion
	 * @return boolean
	 */
	static function isVersionHigher($latestVersion)
	{
		$newVArr = explode('.', $latestVersion);
		$curVArr = explode('.', IFT_VERSION);

		if (count($curVArr) == count($newVArr) && count($newVArr) == 3) {
			for ($i = 0; $i < count($newVArr); $i++) {
				$cv = $curVArr[$i];
				$nv = $newVArr[$i];

				if ($cv < $nv) {
					return true;
				}
			}
		}

		return false;
	}
}
