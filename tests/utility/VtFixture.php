<?php
class VtFixture {
	protected static function readFixture($filename) {
		$charge_json = file_get_contents(__DIR__ . '/fixture/' . $filename);
		$charge_template_params = json_decode($charge_json, true);
		$charge_template_params['transaction_details']['order_id'] = rand();
		return $charge_template_params;
	}
}

class VtChargeFixture extends VtFixture {
	public static function build($payment_type, $payment_data = NULL) {
		$charge_params = self::readFixture('vt_charge.json');

		if (!is_null($payment_type)) {
			$charge_params['payment_type'] = $payment_type;
		}

		if (!is_null($payment_data)) {
			$charge_params[$payment_type] = $payment_data;
		}
		return $charge_params;
	}
}
