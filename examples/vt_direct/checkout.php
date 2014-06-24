<html>
<head>
	<title>Checkout</title>
	
	<!-- 
	Include JQuery & JQuery Fancy Box.
	In this sample, we use JQuery & JQuery Fancy to simplify the process when showing 3D Secure dialog. Feel free to use your own implementation or your prefered JS framework. 
	-->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	
</head>

<body>
	<h1>Checkout</h1>
	
	<form action="http://localhost/veritrans-php-2-examples/checkout_stripe.php" method="POST" id="payment-form">
	  <span class="payment-errors"></span>

	  <div class="form-row">
	    <label>
	      <span>Card Number</span>
	      <input type="text" size="20" data-stripe="number"/>
	    </label>
	  </div>

	  <div class="form-row">
	    <label>
	      <span>CVC</span>
	      <input type="text" size="4" data-stripe="cvc"/>
	    </label>
	  </div>

	  <div class="form-row">
	    <label>
	      <span>Expiration (MM/YYYY)</span>
	      <input type="text" size="2" data-stripe="exp-month"/>
	    </label>
	    <span> / </span>
	    <input type="text" size="4" data-stripe="exp-year"/>
	  </div>

	  <button type="submit">Submit Payment</button>
	</form>
	<script>
		Stripe.setPublishableKey('pk_test_Ka57d09dUDGvAQUzU3FfGXWJ');
		var stripeResponseHandler = function(status, response) {
		  var $form = $('#payment-form');

		  if (response.error) {
		    // Show the errors on the form
		    $form.find('.payment-errors').text(response.error.message);
		    $form.find('button').prop('disabled', false);
		  } else {
		    // token contains id, last4, and card type
		    var token = response.id;
		    // Insert the token into the form so it gets submitted to the server
		    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
		    // and submit
		    $form.get(0).submit();
		  }
		};
		jQuery(function($) {
		  $('#payment-form').submit(function(event) {
		    var $form = $(this);

		    // Disable the submit button to prevent repeated clicks
		    $form.find('button').prop('disabled', true);

		    Stripe.card.createToken($form, stripeResponseHandler);

		    // Prevent the form from submitting with the default action
		    return false;
		  });
		});
	</script>
</body>
</html>
