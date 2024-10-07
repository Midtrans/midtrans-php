<?php
// notification.php

namespace SnapBi;

require_once dirname(__FILE__) . '/../../../Midtrans.php';
// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(["error" => "Only POST requests are allowed"]);
    exit;
}

// Read all headers
$headers = getallheaders();

// Example: Get a specific header (like 'X-Signature')
$xSignature = isset($headers['X-Signature']) ? $headers['X-Signature'] : "kosong";
$xTimeStamp = isset($headers['X-Timestamp']) ? $headers['X-Timestamp'] : "kosong";
$requestUri = $_SERVER['REQUEST_URI'];

// Extract everything after '/notification.php'
$afterNotification = strstr($requestUri, '/notification.php');
$pathAfterNotification = substr($afterNotification, strlen('/notification.php'));

// Read the input/payload from the request body
$input = file_get_contents("php://input");
$payload = json_decode($input);

if (!$input) {
    // Respond with an error if no payload is received
    header("Content-Type: application/json");
    echo json_encode(["error" => "No input received"]);
    exit;
}
header("Content-Type: application/json");
$notificationUrlPath = $pathAfterNotification;

$publicKeyString = "-----BEGIN PUBLIC KEY-----\nABCDefghlhuoJgoXiK21s2NIW0+uJb08sHmd/+/Cm7UH7M/oU3VE9oLhU89oOzXZgtsiw7lR8duWJ0w738NfzvkdA5pX8OYnIL+5Hfa/CxvlT4yAX/abcdEFgh\n-----END PUBLIC KEY-----\n";
SnapBiConfig::$snapBiPublicKey = $publicKeyString;


try {
    echo json_encode([
        "message" => "Webhook verified successfully",
        "isVerified" => SnapBi::notification()
            ->withBody($payload)
            ->withSignature($xSignature)
            ->withTimeStamp($xTimeStamp)
            ->withNotificationUrlPath($notificationUrlPath)
            ->isWebhookNotificationVerified(),
    ]);
} catch (\Exception $e) {
    echo json_encode([
        "message" => "Webhook verification error",
        "error" => $e->getMessage()
    ]);
}


