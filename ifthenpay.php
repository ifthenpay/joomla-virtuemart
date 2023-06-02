<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');


if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentIfthenpay extends vmPSPlugin
{

    public static $_this = false;
    private $sqlFields = array(
        'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
        'virtuemart_order_id' => 'int(1) UNSIGNED',
        'order_number' => 'char(64)',
        'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
        'payment_name' => 'varchar(5000)',
        'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\'',
        'payment_currency' => 'char(3) ',
        'cost_per_transaction' => 'decimal(10,2)',
        'cost_percent_total' => 'decimal(10,2)',
        'tax_id' => 'smallint(1)',
        'pay_method_used' => 'varchar(50)'
    );
    private $authToken = '';

    function __construct(&$subject, $config)
    {
        // load language
        $lang = self::loadJoomlaLang();

        // load helper
        if (!class_exists('Ifthenpayhelper')) {
            $path = VMPATH_ROOT . '/plugins/vmpayment/ifthenpay/helpers/ifthenpayhelper.php';
            if (file_exists($path))
                require $path;
        }
        if (!class_exists('CurlRequest')) {
            $path = VMPATH_ROOT . '/plugins/vmpayment/ifthenpay/helpers/curlRequest.php';
            if (file_exists($path))
                require $path;
        }

        parent::__construct($subject, $config);

        $this->_loggable = TRUE;
        $this->tableFields = array_keys($this->sqlFields);
        $this->_tablepkey = 'id';
        $this->_tableId = 'id';
        $varsToPush = $this->getVarsToPush();
        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
        $this->authToken = IFT_TOKEN_GATEWAY;
        $this->initLogger();
    }



    /**
     * gets array of table columns to store the payment
     *
     * @return array $sqlFields
     */
    function getTableSQLFields(): array
    {
        return $this->sqlFields;
    }



    /**
     * converts internal plugin order data into an array
     *
     * @param [type] $pluginOrderData
     * @return array
     */
    private function getInternalDataArr($pluginOrderData)
    {
        $internalDataArr = [];

        if (isset($pluginOrderData) && !empty($pluginOrderData)) {

            $arrKeys = array_keys($this->sqlFields);

            foreach ($arrKeys as $key) {
                if (isset($pluginOrderData->$key)) {

                    $internalDataArr[$key] = $pluginOrderData->$key;
                }
            }
        }

        return $internalDataArr;
    }




    /**
     * event triggered when confirming order
     *
     * @param [type] $cart
     * @param [type] $order
     * @return void
     */
    function plgVmConfirmedOrder($cart, $order)
    {
        if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }

        $this->log('info', 'on event plgVmConfirmedOrder, order number: ' . $order['details']['BT']->order_number);


        $dbValues = array();
        $dbValues['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
        $dbValues['order_number'] = $order['details']['BT']->order_number;
        $dbValues['virtuemart_paymentmethod_id'] = $cart->virtuemart_paymentmethod_id;
        $dbValues['payment_name'] = $method->payment_name;
        $dbValues['payment_order_total'] = $order['details']['BT']->order_total;
        $dbValues['payment_currency'] = $method->payment_currency;
        $dbValues['cost_per_transaction'] = 0;
        $dbValues['cost_percent_total'] = 0;
        $dbValues['tax_id'] = $method->tax_id;
        $dbValues['pay_method_used'] = 'gateway_generic';

        $this->storePSPluginInternalData($dbValues);


        $modelOrder = VmModel::getModel('orders');
        $order['order_status'] = ((isset($method->status_pending) and $method->status_pending != ""
            and $method->status_pending != "C") ? $method->status_pending : 'U');
        $order['customer_notified'] = 1;

        $modelOrder->updateStatusForOneOrder($order['details']['BT']->virtuemart_order_id, $order, true);


        // send data to gateway
        $urlGatewayToPay = $this->getGatewayPaymentUrl($order);

        // if does not get a valid url
        if ($urlGatewayToPay == '') {
            $this->log('error', 'on event plgVmConfirmedOrder, $urlGatewayToPay is empty, $order: ' . print_r($order, true));
            JFactory::getApplication()->enqueueMessage(vmText::_('VMPAYMENT_IFTHENPAY_CONFIRMATION_ERROR'), 'warning');

            return true;
        }


        $this->log('info', 'on event plgVmConfirmedOrder, dbValues: ' . print_r($dbValues, true));

        // redirect to gateway page
        $mainframe = JFactory::getApplication();
        $mainframe->redirect($urlGatewayToPay);

        return true;
    }

    /**
     * sends curl request with order data to gateway and get the url to the gateway page back
     *
     * @param [type] $order
     * @return void
     */
    public function getGatewayPaymentUrl($order)
    {
        $result = false;

        $orderDtl = isset($order['details']['BT']) ? $order['details']['BT'] : [];
        if (!empty($orderDtl)) {

            $plugin = JPluginHelper::getPlugin('vmpayment', 'ifthenpay');
            $paymentMethodParams = Ifthenpayhelper::getPaymentMethodParams($plugin->id);

            if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
                return NULL;
            }
            $this->getPaymentCurrency($method);
            $totalInPaymentCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total, $method->payment_currency);


            $params = [];
            $params['gatewayKey'] = $paymentMethodParams["gateway_key"];
            $params['antiphishingKey'] = $paymentMethodParams["anti_phishing_key"];
            $params['amount'] = (string) $totalInPaymentCurrency['value'];
            $params['orderId'] = $orderDtl->virtuemart_order_id;

            // description uses order id
            $descStr = JText::_('VMPAYMENT_IFTHENPAY_ORDER_DESC_GATEWAY');
            $params['orderDescription'] = ($descStr . $orderDtl->order_number);
            $params['email'] = urldecode($orderDtl->email);
            $params['firstName'] = utf8_decode(urldecode($orderDtl->first_name));
            $params['lastName'] = utf8_decode(urldecode($orderDtl->last_name));
            $params['returnUrl'] = urldecode(self::getReturnUrl($order));
            $params['cancelUrl'] = urldecode(self::getCancelUrl($order));
            $params['successUrl'] = urldecode(self::getNotificationUrl());
            $params['authToken'] = $this->authToken;
            $langPos = strpos($orderDtl->order_language, '-');
            $params['lang'] = substr($orderDtl->order_language, 0, $langPos);
            $params['accounts'] = "";
            $params['cms'] = "JOOMLA";


            $result = (new CurlRequest())->postGatewayConfirm($params);
        }


        return $result;
    }


    /**
     * event triggered when leaving the gateway page
     * should show a page with:
     * payment method used (still generic name for now, it does not differentiate between mb, mbway....)
     * order number
     * order total value
     * 
     * @param [type] $html
     * @return void
     */
    function plgVmOnPaymentResponseReceived(&$html)
    {


        // the payment itself should send the parameter needed.
        $virtuemart_paymentmethod_id = vRequest::getInt('pm', '');
        $virtuemart_order_id = vRequest::getString('oid', '');

        if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return NULL;
        } // Another method was selected, do nothing

        if (!$this->selectedThisElement($method->payment_element)) {
            return NULL;
        }

        // load required classes
        if (!class_exists('VirtueMartCart')) {
            require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
        }
        if (!class_exists('shopFunctionsF')) {
            require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
        }
        if (!class_exists('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }


        if (!($virtuemart_order_id)) {
            return NULL;
        }

        if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
            $this->log('error', 'on event plgVmOnPaymentResponseReceived, order does not exist in database, order id: ' . $virtuemart_order_id);
            return '';
        }

        $this->log('info', 'on event plgVmOnPaymentResponseReceived, redirected from gateway page to thank you page');


        vmLanguage::loadJLang('com_virtuemart');
        $orderModel = VmModel::getModel('orders');
        $order = $orderModel->getOrder($virtuemart_order_id);


        if (!class_exists('CurrencyDisplay')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
        }
        $currency = CurrencyDisplay::getInstance();
        $amountCurrency = $currency->priceDisplay($order['details']['BT']->order_total);


        $payment_name = $this->renderPluginName($method);
        $html = $this->_getPaymentResponseHtml($paymentTable, $payment_name, $amountCurrency);
        $link = JRoute::_("index.php?option=com_virtuemart&view=orders&layout=details&order_number=" . $order['details']['BT']->order_number . "&order_pass=" . $order['details']['BT']->order_pass, false);

        $html .= '<br />
		<a class="vm-button-correct" href="' . $link . '">' . vmText::_('VMPAYMENT_IFTHENPAY_VIEW_ORDER') . '</a>';

        $cart = VirtueMartCart::getCart();
        $cart->emptyCart();

        return TRUE;
    }


    /**
     * generates the html to present the order sumary after leaving the gateway page
     *
     * @param [type] $paymentTable
     * @param [type] $payment_name
     * @param [type] $amountCurrency
     * @return void
     */
    function _getPaymentResponseHtml($paymentTable, $payment_name, $amountCurrency)
    {
        $html = '<table>' . "\n";
        $html .= $this->getHtmlRow(JText::_('VMPAYMENT_IFTHENPAY_METHOD_NAME'), $payment_name);
        if (!empty($paymentTable)) {
            $html .= $this->getHtmlRow(JText::_('VMPAYMENT_IFTHENPAY_ORDER_NUMBER'), $paymentTable->order_number);
        }
        $html .= $this->getHtmlRow(JText::_('VMPAYMENT_IFTHENPAY_ORDER_TOTAL'), $amountCurrency);
        $html .= '</table>' . "\n";

        return $html;
    }


    /**
     * This function is triggered when we view the order details in administrator.
     *
     */
    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $virtuemart_payment_id)
    {
        if (!$this->selectedThisByMethodId($virtuemart_payment_id)) {
            return NULL; // Another method was selected, do nothing
        }
        if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
            return NULL;
        }

        // vmLanguage::loadJLang('com_virtuemart'); // might not be necessary

        $html = '<table class="adminlist table">' . "\n";
        $html .= $this->getHtmlHeaderBE();
        $html .= $this->getHtmlRowBE('VMPAYMENT_IFTHENPAY_PAYMENT_MODULE', ucfirst($paymentTable->payment_name));

        $payMethodUsed = $paymentTable->pay_method_used != '' ? $paymentTable->pay_method_used : JText::_('VMPAYMENT_IFTHENPAY_AWAITING_PAYMENT');
        $html .= $this->getHtmlRowBE('VMPAYMENT_IFTHENPAY_PAYMENT_METHOD_USED', strtoupper($payMethodUsed));

        $totalInPaymentCurrency = vmPSPlugin::getAmountInCurrency($paymentTable->payment_order_total, $paymentTable->payment_currency);
        $html .= $this->getHtmlRowBE('VMPAYMENT_IFTHENPAY_PAYMENT_TOTAL_CURRENCY', $totalInPaymentCurrency['display']);
        $html .= '</table>' . "\n";
        return $html;
    }



    /**
     * checks if payment method can be displayed on checkout
     *
     * @param [type] $cart
     * @param [type] $method
     * @param [type] $cart_prices
     * @return void
     */
    protected function checkConditions($cart, $method, $cart_prices)
    {
        // get customer address
        $address = $cart->getST();

        // validate amount
        $amount = $cart_prices['salesPrice'];
        if (
            ($method->min_amount != '' && $amount < $method->min_amount) ||
            ($method->max_amount != '' && $amount > $method->max_amount)
        ) {
            return false;
        }

        // validate currency
        $plugin = JPluginHelper::getPlugin('vmpayment', 'ifthenpay');
        $paymentMethod = Ifthenpayhelper::getPaymentMethodParams($plugin->id);

        $shopCurrency = CurrencyDisplay::getInstance()->getCurrencyForDisplay();
        if (isset($paymentMethod['payment_currency']) && isset($shopCurrency) && $shopCurrency != '') {

            if ($paymentMethod['payment_currency'] != $shopCurrency) {
                return false;
            }
        }

        // get array of available countries
        $countries = array();
        if (!empty($method->countries)) {
            if (!is_array($method->countries)) {
                $countries[0] = $method->countries;
            } else {
                $countries = $method->countries;
            }
        }

        // probably did not gave his BT:ST address
        if (!is_array($address)) {
            $address = array();
            $address['virtuemart_country_id'] = 0;
        }
        if (!isset($address['virtuemart_country_id'])) {
            $address['virtuemart_country_id'] = 0;
        }

        // validate country
        if (!(count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries))) {
            return false;
        }

        return true;
    }


    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id)
    {
        return $this->onStoreInstallPluginTable($jplugin_id);
    }

    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart)
    {
        return $this->OnSelectCheck($cart);
    }

    /**
     * plgVmDisplayListFEPayment
     * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
     *
     * @param VirtueMartCart $cart Cart object
     * @param integer $selected ID of the method selected
     * @param [type] $htmlIn
     * @return bool
     */
    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn): bool
    {
        if ($this->getPluginMethods($cart->vendorId) === 0) {
            if (empty($this->_name)) {
                $app = JFactory::getApplication();
                $app->enqueueMessage(vmText::_('COM_VIRTUEMART_CART_NO_' . strtoupper($this->_psType)));
                return false;
            } else {
                return false;
            }
        }

        $method_name = $this->_psType . '_name';
        $idN = 'virtuemart_' . $this->_psType . 'method_id';

        foreach ($this->methods as $this->_currentMethod) {

            if ($this->checkConditions($cart, $this->_currentMethod, $cart->cartPrices)) {

                $html = '';

                $this->_currentMethod->payment_currency = $this->getPaymentCurrency($this->_currentMethod);
                $this->_currentMethod->$method_name = $this->renderPluginName($this->_currentMethod);

                $html .= $this->getPluginHtm($this->_currentMethod, $selected);
                $htmlIn[$this->_psType][$this->_currentMethod->$idN] = $html;
            }
        }

        return true;
    }

    /**
     * Generates the html for the selectable radio box on checkout
     *
     * @param [type] $plugin
     * @param [type] $selectedPlugin
     * @return void
     */
    protected function getPluginHtm($plugin, $selectedPlugin)
    {

        $pluginmethod_id = $this->_idName;
        if ($selectedPlugin == $plugin->{$pluginmethod_id}) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        $dynUpdate = '';
        if (VmConfig::get('oncheckout_ajax', false)) {
            $dynUpdate = ' data-dynamic-update="1" ';
        }

        $html = '';
        if ((isset($plugin->payment_methods_text) && $plugin->payment_methods_text != '')) {
            $html = '<input type="radio" ' . $dynUpdate . ' name="' . $pluginmethod_id . '" id="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '"   value="' . $plugin->$pluginmethod_id . '" ' . $checked . ">\n" . '
            <label for="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '">
                <span class="vmpayment">
                    <span class="vmpayment_name">' . $plugin->payment_methods_text . ' 
                    </span>
                </span>
            </label>
            ';
        } else {

            $pmLogosArr = Ifthenpayhelper::getPaymentMethodsArr($plugin->gateway_key);

            $imgHtml = '';
            foreach ($pmLogosArr as $pmCode) {

                if (isset(IFT_PAYMENT_METHODS[$pmCode])) {
                    $pmName = IFT_PAYMENT_METHODS[$pmCode];

                    $imgHtml .= '
                    <label for="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '">
                        <img style="padding-left: 10px; cursor: pointer;" src="' . IFT_URL_IMAGES_FOLDER . $pmName . '.png" alt="' . $pmName . '"/>
                    </label>
                    ';
                }
            }

            $html = '<input type="radio" ' . $dynUpdate . ' name="' . $pluginmethod_id . '" id="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '"   value="' . $plugin->$pluginmethod_id . '" ' . $checked . ">\n" . $imgHtml;
        }
        return $html;
    }



    /**
     * process User Payment Cancel
     *
     * @return  boolean|null
     * @since   1.0.0
     */
    function plgVmOnUserPaymentCancel()
    {

        if (!class_exists('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }

        $virtuemart_order_id = vRequest::getString('oid', '');
        $virtuemart_paymentmethod_id = vRequest::getInt('pm', '');
        if (
            empty($virtuemart_order_id) or
            empty($virtuemart_paymentmethod_id) or
            !$this->selectedThisByMethodId($virtuemart_paymentmethod_id)
        ) {
            return NULL;
        }

        if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
            return NULL;
        }


        $this->log('info', 'on event plgVmOnUserPaymentCancel, paymentTable(same as order information): ' . print_r($paymentTable, true));


        $this->handlePaymentUserCancel($virtuemart_order_id);

        // send message informing cancelation, this is required when canceling from credit card
        JFactory::getApplication()->enqueueMessage(vmText::_('VMPAYMENT_IFTHENPAY_PAYMENT_CANCELLED'));


        return TRUE;
    }


    public function plgVmOnSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
    {

        return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }


    function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId)
    {

        if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }
        $this->getPaymentCurrency($method);

        $paymentCurrencyId = $method->payment_currency;
    }

    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array())
    {
        return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }

    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name)
    {
        $this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
    }

    function plgVmOnShowOrderPrintPayment($order_number, $method_id)
    {
        return $this->onShowOrderPrint($order_number, $method_id);
    }

    function plgVmDeclarePluginParamsPayment($name, $id, &$data)
    {
        return $this->declarePluginParams('payment', $name, $id, $data);
    }

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table)
    {
        return $this->setOnTablePluginParams($name, $id, $table);
    }

    function plgVmDeclarePluginParamsPaymentVM3(&$data)
    {
        return $this->declarePluginParams('payment', $data);
    }


    /**
     * when gateway comunicates that payment has been made
     *
     * @return void
     */
    function plgVmOnPaymentNotification()
    {


        $cb_data = vRequest::getGet();

        $uidData = json_decode((new CurlRequest())->getTransactionDataByUid($cb_data['uid']));

        if ($uidData) {
            $apk = isset($uidData->antiphishingKey) && $uidData->antiphishingKey != '' ? $uidData->antiphishingKey : '';
            $amount = isset($uidData->amount) && $uidData->amount != '' ? $uidData->amount : '';
            $orderId = isset($uidData->orderId) && $uidData->orderId != '' ? $uidData->orderId : '';

        } else if ($cb_data) {
            $apk = (isset($cb_data['apk']) && $cb_data['apk'] != '') ? $cb_data['apk'] : '';
            $amount = (isset($cb_data['amt']) && $cb_data['amt'] != '') ? $cb_data['amt'] : '';
            $orderId = (isset($cb_data['oid']) && $cb_data['oid'] != '') ? $cb_data['oid'] : '';
        }




        if (!class_exists('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }


        $this->log('info', 'on event plgVmOnPaymentNotification, cb_data: ' . print_r($cb_data, true));


        $plugin = JPluginHelper::getPlugin('vmpayment', 'ifthenpay');
        $paymentMethodParams = Ifthenpayhelper::getPaymentMethodParams($plugin->id);

        if ($apk != $paymentMethodParams['anti_phishing_key']) {

            $this->log('error', 'on event plgVmOnPaymentNotification, order number not set in get request');
            http_response_code(400);
            die('oops'); // this message is purposely ambiguous, to prevent insight into the workings of the function
        }
        // check: has order number
        if (!$orderId) {

            $this->log('error', 'on event plgVmOnPaymentNotification, order number not set in get request');
            http_response_code(400);
            die('oops'); // this message is purposely ambiguous, to prevent insight into the workings of the function
        }



        // get&check: order data
        if (!($payment = $this->getDataByOrderId($orderId))) {

            $this->log('error', 'on event plgVmOnPaymentNotification, order data does not exist in database');
            http_response_code(400);
            die('oops'); // this message is purposely ambiguous, to prevent insight into the workings of the function
        }

        $method = $this->getVmPluginMethod($payment->virtuemart_paymentmethod_id);
        // check if payment method is assigned to order
        if (!$this->selectedThisElement($method->payment_element)) {

            $this->log('error', 'on event plgVmOnPaymentNotification, this payment method is not assigned to this order');
            http_response_code(400);
            die('oops'); // this message is purposely ambiguous, to prevent insight into the workings of the function
        }

        // todo: not sure if this is needed, since "get&check: order data" already seems to test this
        if (!$payment) {
            $this->log('error', 'on event plgVmOnPaymentNotification, payment not found');
            http_response_code(400);
            die('oops'); // this message is purposely ambiguous, to prevent insight into the workings of the function
        }

        $modelOrder = VmModel::getModel('orders');
        $vmOrder = $modelOrder->getOrder($orderId);
        $orderStatus = $vmOrder['details']['BT']->order_status;


        // note: it is weird that I am testing for status_success since I would not expect to change an already successful order
        // still they did just that on the example plugin....
        if (
            $orderStatus != $method->status_success &&
            $orderStatus != $method->status_pending
        ) { // can't get status or payment failed
            $this->log('error', 'on event plgVmOnPaymentNotification, order has been cancelled');
            http_response_code(400);
            die('oops'); // this message is purposely ambiguous, to prevent insight into the workings of the function
        }


        $fOrderTotal = number_format($payment->payment_order_total, 2);


        // $amountForCcard = (isset($cb_data['amount']) && $cb_data['amount'] !== '') ? $cb_data['amount'] : '';
        // $amountForOther = (isset($cb_data['amt']) && $cb_data['amt'] !== '') ? $cb_data['amt'] : '';

        // $amount = (isset($cb_data['pmt']) && ($cb_data['pmt'] === 'ccard' || $cb_data['pmt'] === 'CCARD')) ? $amountForCcard : $amountForOther;


        if ($amount === '' || ($amount != $fOrderTotal)) {
            $this->log('error', 'on event plgVmOnPaymentNotification, value is different (expected: ' . $fOrderTotal . ', received: ' . ($cb_data['amt'] ?? 'no value received'));
            http_response_code(400);
            die('oops'); // this message is purposely ambiguous, to prevent insight into the workings of the function
        }


        if (isset($cb_data["task"]) && $cb_data["task"] == "pluginnotification") {
            $order['order_status'] = $method->status_success;
            $order['comments'] = vmText::sprintf('VMPAYMENT_IFTHENPAY_PAYMENT_STATUS_CONFIRMED', $order_number);
        }




        $order['customer_notified'] = 1;

        $pluginOrderData = $this->getDataByOrderId($orderId);
        $pluginInternalDataArr = $this->getInternalDataArr($pluginOrderData);
        $pluginInternalDataArr['pay_method_used'] = $cb_data['pmt'];


        $this->storePSPluginInternalData($pluginInternalDataArr, 'virtuemart_order_id', true);



        $modelOrder = VmModel::getModel('orders');


        $this->log('info', 'on event plgVmOnPaymentNotification, order status changed to confirmed (' . $order['order_status'] . ')');

        $modelOrder->updateStatusForOneOrder($orderId, $order, TRUE);

        $this->emptyCart($payment->user_session, $cb_data['oid']);

        if ($cb_data['pmt'] === 'ccard' || $cb_data['pmt'] === 'CCARD') {
            // redirect to tankyou page

            $urlReturnFromGateway = $this->getOnlineReturnUrl($cb_data['pmt'], $cb_data['pm'], $orderId);
            $mainframe = JFactory::getApplication();
            $mainframe->redirect($urlReturnFromGateway);
        }


        http_response_code(200);
        die('ok');
    }





    // utility functions bellow

    static function getCancelUrl($order)
    {
        return JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel' .
            '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id .
            '&oid=' . $order['details']['BT']->virtuemart_order_id .
            '&apk=[ANTI_PHISHING_KEY]' .
            '&amt=[AMOUNT]' .
            '&pmt=[PAYMENT_METHOD]';
    }

    static function getOnlineReturnUrl($paymentMethod, $paymentMethodId, $orderNumber)
    {
        return JURI::root() . "index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=" . $paymentMethodId . '&oid=' . $orderNumber .
            '&pmt=' . $paymentMethod;
    }

    static function getReturnUrl($order)
    {
        return JURI::root() . "index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=" . $order['details']['BT']->virtuemart_paymentmethod_id . '&Itemid=' . vRequest::getInt('Itemid') .
            '&oid=' . $order['details']['BT']->virtuemart_order_id .
            '&apk=[ANTI_PHISHING_KEY]' .
            '&amt=[AMOUNT]' .
            '&pmt=[PAYMENT_METHOD]';
    }


    static function getNotificationUrl()
    {
        return Ifthenpayhelper::getCallbackUrl();
    }


    static function loadJoomlaLang()
    {
        $lang = JFactory::getLanguage();
        $extension = 'plg_vmpayment_ifthenpay';
        $base_dir = JPATH_SITE;
        $language_tag = $lang->getTag();
        $lang->load($extension, $base_dir, $language_tag, TRUE);

        return $lang;
    }


    /**
     * set our own logger, this will log events to "administrator/logs/com_ifthenpay.log.php"
     *
     * @return void
     */
    private function initLogger()
    {
        JLog::addLogger(
            array(
                'text_file' => 'com_ifthenpay.log.php',
                'text_entry_format' => '{PRIORITY}  {DATETIME}  {CLIENTIP} =>  {MESSAGE}'
            ),
            JLog::ALL,
            array($this->_name . '_log')
        );
    }

    /**
     * logs stuff, this is an abstraction of the original joomla logger function in order to not waste time filling parameters
     *
     * @param [type] $priority
     * @param [type] $msg
     * @return void
     */
    public function log($priority, $msg)
    {
        switch ($priority) {
            case 'critical':
                $priority = JLog::CRITICAL;
                break;
            case 'error':
                $priority = JLog::ERROR;
                break;
            case 'warning':
                $priority = JLog::WARNING;
                break;
            default:
                $priority = JLog::INFO;
                break;
        }

        $msg = str_replace(array("\r", "\n"), '', $msg);

        JLog::add($msg, $priority, $this->_name . '_log');
    }
}