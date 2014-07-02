<html>

<head>
  <title>Checkout</title>
  <link rel="stylesheet" href="jquery.fancybox.css">
</head>

<body>
  <script type="text/javascript" src="https://api.sandbox.veritrans.co.id/v2/assets/js/veritrans.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script type="text/javascript" src="jquery.fancybox.pack.js"></script>

  <h1>Checkout</h1>
  <form action="checkout-process.php" method="POST" id="payment-form">
    <fieldset>
      <legend>Checkout</legend>
      <p>
        <label>Card Number</label>
        <input class="card-number" value="4111111111111111" size="20" type="text" autocomplete="off" />
      </p>
      <p>
        <label>Expiration (MM/YYYY)</label>
        <input class="card-expiry-month" value="12" placeholder="MM" size="2" type="text" />
        <span> / </span>
        <input class="card-expiry-year" value="2020" placeholder="YYYY" size="4" type="text" />
      </p>
      <p>
        <label>CVV</label>
        <input class="card-cvv" value="123" size="4" type="password" autocomplete="off" />
      </p>

      <p>
        <label>Save credit card</label>
        <input type="checkbox" name="save_cc" value="true">
      </p>

      <input id="token_id" name="token_id" type="hidden" />
      <button class="submit-button" type="submit">Submit Payment</button>
    </fieldset>
  </form>

  <!-- Javascript for token generation -->
  <script type="text/javascript">
    $(function () {
      // Sandbox URL
      Veritrans.url = "https://api.sandbox.veritrans.co.id/v2/token";
      // TODO: Change with your client key.
      Veritrans.client_key = "<your client key>";
      var card = function () {
        return {
          "card_number": $(".card-number").val(),
          "card_exp_month": $(".card-expiry-month").val(),
          "card_exp_year": $(".card-expiry-year").val(),
          "card_cvv": $(".card-cvv").val(),
          "secure": false,
          "gross_amount": 200000
        }
      };

      function callback(response) {
        console.log(response);
        if (response.redirect_url) {
          console.log("3D SECURE");
          // 3D Secure transaction, please open this popup
          openDialog(response.redirect_url);

        }
        else if (response.status_code == "200") {
          console.log("NOT 3-D SECURE");
          // Success 3-D Secure or success normal
          closeDialog();
          // Submit form
          $("#token_id").val(response.token_id);
          $("#payment-form").submit();
        }
        else {
          // Failed request token
          console.log(response.status_code);
          alert(response.status_message);
        }
      }

      function openDialog(url) {
        $.fancybox.open({
          href: url,
          type: "iframe",
          autoSize: false,
          width: 700,
          height: 500,
          closeBtn: false,
          modal: true
        });
      }

      function closeDialog() {
        $.fancybox.close();
      }

      $(".submit-button").click(function (event) {
        console.log("SUBMIT");
        event.preventDefault();
        $(this).attr("disabled", "disabled");
        Veritrans.token(card, callback);
        return false;
      });
    });
  </script>
</body>

</html>