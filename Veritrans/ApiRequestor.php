<?php
/**
 * Send reuquest to Veritrans API
 * Better don't use this class directly, use Veritrans_VtWeb, Veritrans_VtDirect, Veritrans_Transaction
 */

class Veritrans_ApiRequestor {

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
      CURLOPT_CAINFO => dirname(__FILE__) . "/../data/cacert.pem"
    );

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
    } else {
      $result = curl_exec($ch);
      // curl_close($ch);
    }


    if ($result === FALSE) {
      throw new Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
    }
    else {
      $result_array = json_decode($result);
      if (!in_array($result_array->status_code, array(200, 201, 202, 407))) {
        $message = 'Veritrans Error (' . $result_array->status_code . '): '
            . $result_array->status_message;
        throw new Exception($message, $result_array->status_code);
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
