<?php
require_once('config.php');



class CurlRequest
{

  protected $urlGatewayConfirm;



  public function __construct()
  {

    $this->urlGatewayConfirm = IFT_URL_GATEWAY_CONFIRM_ORDER;
    $this->urlTransactionUidData = IFT_URL_GATEWAY_UID_DATA;
    $this->urlUpdateXml = IFT_URL_GATEWAY_UPDATE_XML;
  }

  /*
     * Pedido via post
     * 
     * Argumentos:
     *      $url:               endereço a qual vai ser efectuado o pedido
     *      $param:             dados a serem enviados
     *      $option_headers:    informações opcionais 
     * 
     */
  public static function do_post_curl($url, $params, $option_headers = "")
  {
    $curl = curl_init();
    $jsonParams = empty($params) ? json_encode($params, JSON_FORCE_OBJECT) : json_encode($params);

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_POSTFIELDS => $jsonParams,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "$option_headers"
      ),
    ));

    $response = curl_exec($curl);

    return $response;
  }


  public static function do_get_curl($url, $option_headers = "")
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "$option_headers"
      ),
    ));

    $response = curl_exec($curl);

    return $response;
  }


  /**
   * send order data to gateway, and get the gateway redirection url back
   */
  public function postGatewayConfirm($params)
  {
    $url = $this->urlGatewayConfirm;
    return json_decode($this->do_post_curl($url, $params));
  }

  public function getTransactionDataByUid($uid)
  {
    $url = $this->urlTransactionUidData . $uid;
    return json_decode($this->do_get_curl($url));
  }

  public function getUpdateXml()
  {
    $url = $this->urlUpdateXml;
    return $this->do_get_curl($url);
  }

  public function getAvailablePaymentMethods(string $gatewayKey)
  {
    return json_decode($this->do_get_curl(IFT_URL_GATEWAY_PAYMENT_METHODS . $gatewayKey));
  }


}
