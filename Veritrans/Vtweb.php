<?php 

class Veritrans_Vtweb {

	/**
		* @return the redirection url when the request is successful
		*/
	public static function getRedirectionUrl($params)
	{
		$params = array_merge_recursive($params, array(
			'payment_type' => 'vtweb',
			'vtweb' => array(
				'enabled_payments' => array(
					'credit_card'
					)
				)
			));
		$result = Veritrans_ApiRequestor::post(Veritrans::getBaseUrl() . '/charge', Veritrans::$serverKey, $params);
		return $result['redirect_url'];
	}

}