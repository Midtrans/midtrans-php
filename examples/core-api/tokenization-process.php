<?php
namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';
//Set Your server key
Config::$serverKey = "<your server key>";

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

$response = CoreApi::linkPaymentAccount($params);
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
