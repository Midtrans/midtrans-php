<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';
// Set Your server key
// can find in Merchant Portal -> Settings -> Access keys
Config::$serverKey = '<your server key>';

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

// define variables and set to empty values
$number = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number = ($_POST["number"]);
}

// required
$params = array(
    "payment_type" => "gopay",
    "gopay_partner" => array(
        "phone_number" => $number,
        "redirect_url" => "https://www.google.com"
    )
);

$response = '';
try {
    $response = CoreApi::linkPaymentAccount($params);
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}

function printExampleWarningMessage() {
    if (strpos(Config::$serverKey, 'your ') != false ) {
        echo "<code>";
        echo "<h4>Please set your server key from sandbox</h4>";
        echo "In file: " . __FILE__;
        echo "<br>";
        echo "<br>";
        echo htmlspecialchars('Config::$serverKey = \'<your server key>\';');
        die();
    } 
}

?>

<!DOCTYPE HTML>
<html>
<head>
</head>
<body>

<h2>Simple Gopay Tokenization</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Phone number: <input type="text" name="number">
    <br><br>
    <input type="submit" name="submit" value="Submit">
</form>


<?php
echo "<h2>Result get pay account:</h2>";
echo json_encode($response, JSON_UNESCAPED_SLASHES);
echo "<br>";
?>


</body>
</html>
