<?php 

class Veritrans_Transaction {

	public $order_id;
	public $gross_amount;
	public $item_details;

	public function get_redirection_url()
	{
		$data_hash = array(
			'transaction_details' => array(
				'order_id' => $this->order_id,
				'gross_amount' => $this->gross_amount
				),
			'payment_type' => 'vtweb',
			'vtweb' => array(
				'enabled_payments' => array(
					'credit_card'
					)
				),
			'item_details' => $this->item_details
			);
		$base_url = Veritrans::$is_sandbox ? Veritrans::$sandbox_base_url : Veritrans::$production_base_url;
		$result = Veritrans_Utility::remote_call($base_url . '/charge', Veritrans::$server_key, $data_hash);
		return $result['redirect_url'];
	}
}