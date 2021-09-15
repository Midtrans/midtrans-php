<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs:
// https://docs.midtrans.com/en/core-api/credit-card?id=_1-getting-the-card-token

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';
// Set Your server key
// can find in Merchant Portal -> Settings -> Access keys
Config::$clientKey = '<your client key>';

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

function printExampleWarningMessage() {
    if (strpos(Config::$clientKey, 'your ') != false ) {
        echo "<code>";
        echo "<h4>Please set your client key from sandbox</h4>";
        echo "In file: " . __FILE__;
        echo "<br>";
        echo "<br>";
        echo htmlspecialchars('Config::$clientKey = \'<your client key>\';');
        die();
    } 
}
?>
<html>

<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/featherlight/1.7.12/featherlight.min.css">
</head>

<body>
    <script id="midtrans-script" type="text/javascript" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js" data-environment="sandbox" data-client-key="<?php echo Config::$clientKey;?>"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/featherlight/1.7.12/featherlight.min.js"></script>

    <h1>Checkout</h1>
    <form action="checkout-process.php" method="POST" id="payment-form">
        <fieldset>
            <legend>Checkout</legend>
            <small><strong>Field that may be presented to customer:</strong></small>
            <p>
                <label>Card Number</label>
                <input class="card-number" name="card-number" value="4811 1111 1111 1114" size="23" type="text" autocomplete="off" />
            </p>
            <p>
                <label>Expiration (MM/YYYY)</label>
                <input class="card-expiry-month" name="card-expiry-month" value="12" placeholder="MM" size="2" type="text" />
                <span> / </span>
                <input class="card-expiry-year" name="card-expiry-year" value="2025" placeholder="YYYY" size="4" type="text" />
            </p>
            <p>
                <label>CVV</label>
                <input class="card-cvv" name="card-cvv" value="123" size="4" type="password" autocomplete="off" />
            </p>
            <p>
                <label>Save credit card</label>
                <input type="checkbox" id="save_cc" name="save_cc" value="true">
            </p>
            <small><strong>Fields that shouldn't be presented to the customer:</strong></small>
            <p>
                <label>3D Secure</label>
                <input type="checkbox" id="secure" name="secure" value="true" checked>
            </p>
            <input id="token_id" name="token_id" type="hidden" />
            <button class="submit-button" type="submit">Submit Payment</button>
        </fieldset>
    </form>

    <code>
        <pre>
        <b>Testing cards:</b>

        <b>For 3D Secure:</b>
        Visa        4811 1111 1111 1114
        MasterCard  5211 1111 1111 1117

        <b>For Non 3D Secure:</b>
        Visa success      4011 1111 1111 1112
        Visa challenge    4111 1111 1111 1111
        Visa deny by FDS  4211 1111 1111 1110

        MasterCard success      5481 1611 1111 1081
        MasterCard challenge    5110 1111 1111 1119
        MasterCard deny by FDS  5210 1111 1111 1118

        </pre>
    </code>

    <!-- Javascript for token generation -->
    <script type="text/javascript">
        $(function () {
            // open the console log to check the flow
            // 3ds new flow:
            // 1. get token_id
            // 2. send token_id to backend
            // 3. initial charge from backend to midtrans api
            // 4. open redirect_url

            var options = {
                performAuthentication: function(redirect_url){
                    openDialog(redirect_url);
                },
                onSuccess: function(response){
                    console.log('success');
                    console.log('response:',response);
                    closeDialog();
                },
                onFailure: function(response){
                    console.log('fail');
                    console.log('response:',response);
                    closeDialog();
                    alert(response.status_message);
                    $('button').removeAttr("disabled");
                },
                onPending: function(response){
                    console.log('pending');
                    console.log('response:',response);
                    closeDialog();
                }
            };

            function openDialog(url) {
                $.featherlight({
                iframe: url, 
                iframeMaxWidth: '80%', 
                iframeWidth: 700, 
                iframeHeight: 500,
                closeOnClick: false,
                closeOnEsc: false,
                closeIcon:''
                });
            }

            function closeDialog() {
                $.featherlight.close();
            }

            $(".submit-button").click(function (event) {
                var card = {
                    "card_number": $(".card-number").val(),
                    "card_exp_month": $(".card-expiry-month").val(),
                    "card_exp_year": $(".card-expiry-year").val(),
                    "card_cvv": $(".card-cvv").val()
                };
                
                event.preventDefault();
                $(this).attr("disabled", "disabled");

                console.log('1. get token_id');
                MidtransNew3ds.getCardToken(card, getCardTokenCallback);
                return false;
            });

            // callback functions
            var getCardTokenCallback = {
                onSuccess: function(response) {
                    // Success to get card token_id, implement as you wish here
                    console.log('Success to get card token_id, response:', response);
                    var token_id = response.token_id;
                    $("#token_id").val(token_id);

                    console.log('This is the card token_id:', token_id);
                    // Implement sending the token_id to backend to proceed to next step
                    console.log('2. send token_id to backend');
                    // send token_id, save_cc and secure params
                    // we send secure param for sample, in production, you should define transaction is secure/not in backend
                    // we recommend always use secure=true
                    // data: $("#token_id, #save_cc, #secure").serialize()
                    $.ajax({
                        type: 'POST',
                        url: 'checkout-process.php',
                        data: $("#token_id, #save_cc, #secure").serialize(),
                        success: function(response){
                            console.log('3. response charge from backend:', response);
                            if (response.redirect_url){
                                console.log('4. open redirect_url');
                                MidtransNew3ds.authenticate(response.redirect_url, options);
                            }
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                    
                },
                onFailure: function(response) {
                    // Fail to get card token_id, implement as you wish here
                    console.log('Fail to get card token_id, response:', response);
                    closeDialog();
                    $('button').removeAttr("disabled");
                }
            };
        });
    </script>
</body>

</html>