<?php 

class Veritrans_ApiRequestor {

  private $sandbox_base_url = 'https://api.sandbox.veritrans.co.id/v2';
  private $production_base_url = 'https://api.veritrans.co.id/v2';

  public static function get($url, $server_key, $data_hash)
  {
    return Veritrans_Utility::remoteCall($url, $server_key, $data_hash, false);
  }

  public static function post($url, $server_key, $data_hash)
  {
    return Veritrans_Utility::remoteCall($url, $server_key, $data_hash, true);
  }

  public static function remote_call($url, $server_key, $data_hash, $post = true)
  {
    $ch = curl_init();
    
    if ($data_hash) {
      $body = json_encode($data_hash);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: Basic ' . base64_encode($server_key . ':')
      ));

    if ($post)
      curl_setopt($ch, CURLOPT_POST, 1);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    curl_close($ch);

    if ($result === FALSE)
    {
      throw new \Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
    } else
    {
      $result_array = json_decode($result, true);
      if (!in_array($result_array['status_code'], array(200, 201, 202)))
      {
        throw new \Exception('Veritrans Error (' . $result_array['status_code'] . '): ' . $result_array['status_message'], $result_array['status_code']);
      } else
      {
        return $result_array;
      }
    }  

  }

}