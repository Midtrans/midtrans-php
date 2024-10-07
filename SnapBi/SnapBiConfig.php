<?php

namespace SnapBi;

/**
 * Midtrans Configuration
 */
class SnapBiConfig
{
    public static $isProduction = false;
    public static $snapBiClientId;
    public static $snapBiPrivateKey;
    public static $snapBiClientSecret;
    public static $snapBiPartnerId;
    public static $snapBiChannelId;
    public static $enableLogging = false;
    public static $snapBiPublicKey;

    const SNAP_BI_SANDBOX_BASE_URL = 'https://merchants.sbx.midtrans.com';
    const SNAP_BI_PRODUCTION_BASE_URL = 'https://merchants.midtrans.com';

    /**
     * Get baseUrl
     *
     * @return string Midtrans API URL, depends on $isProduction
     */
    public static function getSnapBiTransactionBaseUrl()
    {
        return SnapBiConfig::$isProduction ?
            SnapBiConfig::SNAP_BI_PRODUCTION_BASE_URL : SnapBiConfig::SNAP_BI_SANDBOX_BASE_URL;
    }
}
