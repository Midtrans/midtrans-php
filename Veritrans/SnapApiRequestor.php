<?php
/**
 * Send request to Snap API
 * Better don't use this class directly, use Veritrans_Snap
 */

class Veritrans_SnapApiRequestor {

  /**
   * Send GET request
   * @param string  $url
   * @param string  $server_key
   * @param mixed[] $data_hash
   */
  public static function get($url, $server_key, $data_hash)
  {
    return self::remoteCall($url, $server_key, $data_hash, false);
  }

  /**
   * Send POST request
   * @param string  $url
   * @param string  $server_key
   * @param mixed[] $data_hash
   */
  public static function post($url, $server_key, $data_hash)
  {
    return self::remoteCall($url, $server_key, $data_hash, true);
  }

  /**
   * Actually send request to API server
   * @param string  $url
   * @param string  $server_key
   * @param mixed[] $data_hash
   * @param bool    $post
   */
  public static function remoteCall($url, $server_key, $data_hash, $post = true)
  {
    $ch = curl_init();

    $curl_options = array(
      CURLOPT_URL => $url,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($server_key . ':')
      ),
      CURLOPT_RETURNTRANSFER => 1,
      // CURLOPT_CAINFO => dirname(__FILE__) . "/../data/cacert.pem"
    );

    // merging with Veritrans_Config::$curlOptions
    if (count(Veritrans_Config::$curlOptions)) {
      // We need to combine headers manually, because it's array and it will no be merged
      if (Veritrans_Config::$curlOptions[CURLOPT_HTTPHEADER]) {
        $mergedHeders = array_merge($curl_options[CURLOPT_HTTPHEADER], Veritrans_Config::$curlOptions[CURLOPT_HTTPHEADER]);
        $headerOptions = array( CURLOPT_HTTPHEADER => $mergedHeders );
      } else {
        $mergedHeders = array();
      }

      $curl_options = array_replace_recursive($curl_options, Veritrans_Config::$curlOptions, $headerOptions);
    }

    if ($post) {
      $curl_options[CURLOPT_POST] = 1;

      if ($data_hash) {
        $body = json_encode($data_hash);
        $curl_options[CURLOPT_POSTFIELDS] = $body;
      } else {
        $curl_options[CURLOPT_POSTFIELDS] = '';
      }
    }

    curl_setopt_array($ch, $curl_options);

    // For testing purpose
    if (class_exists('VT_Tests') && VT_Tests::$stubHttp) {
      $result = self::processStubed($curl_options, $url, $server_key, $data_hash, $post);
      $info = VT_Tests::$stubHttpStatus;
    } else {
      $result = curl_exec($ch);
      $info = curl_getinfo($ch);
      // curl_close($ch);
    }


    if ($result === FALSE) {
      throw new Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
    }
    else {
      try {
        $result_array = json_decode($result);
      } catch (Exception $e) {
        $message = "API Request Error unable to json_decode API response: ".$result . ' | Request url: '.$url;
        throw new Exception($message);
      }
      if ($info['http_code'] != 201) {
        $message = 'Midtrans Error (' . $info['http_code'] . '): '
            . $result . ' | Request url: '.$url;
        throw new Exception($message, $info['http_code']);
      }
      else {
        return $result_array;
      }
    }
  }

  private static function processStubed($curl, $url, $server_key, $data_hash, $post) {
    VT_Tests::$lastHttpRequest = array(
      "url" => $url,
      "server_key" => $server_key,
      "data_hash" => $data_hash,
      "post" => $post,
      "curl" => $curl
    );

    return VT_Tests::$stubHttpResponse;
  }
}
