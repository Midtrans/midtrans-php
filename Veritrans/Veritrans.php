<?php 

class Veritrans {
	
	public static $serverKey;
	public static $apiVersion = 2;
	public static $isProduction = false;
	
	const SANDBOX_BASE_URL = 'https://api.sandbox.veritrans.co.id/v2';
	const PRODUCTION_BASE_URL = 'https://api.veritrans.co.id/v2';

	public static function getBaseUrl()
	{
		return Veritrans::$isProduction ? Veritrans::PRODUCTION_BASE_URL : Veritrans::SANDBOX_BASE_URL; 
	}
}