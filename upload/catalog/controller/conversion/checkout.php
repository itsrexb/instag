<?php
class ControllerConversionCheckout extends Controller {
	public function index($order_info = array(), $order_products = array()) {
		$conversion_code = html_entity_decode($this->config->get('conversion_checkout_code'), ENT_QUOTES, 'UTF-8');

		if ($order_info) {
			// loop through all attributes in $order_info and see if they can be replaced into the conversion code
			foreach ($order_info as $key => $value) {
				if (!is_array($value)) {
					if ($key == 'total') {
						$conversion_code = str_replace('[[' . $key . ']]', $this->currency->format($value, $this->config->get('config_currency'), '', false), $conversion_code);
					} else {
						$conversion_code = str_replace('[[' . $key . ']]', $value, $conversion_code);
					}
				}
			}

			// replace order_product blocks with real data
			if ($order_products) {
				// block start and end strings
				$start_str = '[[START order_products]]';
				$end_str   = '[[END order_products]]';

				do {
					// loop through conversion code and find each block of order_products to replace
					$start_block = strpos($conversion_code, $start_str);
					$end_block   = strpos($conversion_code, $end_str);

					if ($start_block !== false && $end_block !== false) {
						$block_code = trim(substr($conversion_code, ($start_block + strlen($start_str)), ($end_block - ($start_block + strlen($start_str)))));

						$order_product_data = array();

						foreach ($order_products as $order_product) {
							$order_product_code = $block_code;

							// loop through all attributes in each order_product record and see if they can be replaced into the order product code
							foreach ($order_product as $key => $value) {
								if (is_float($key)) {
									$order_product_code = str_replace('[[product_' . $key . ']]', $this->currency->format($value, $this->config->get('config_currency'), '', false), $order_product_code);
								} else {
									$order_product_code = str_replace('[[product_' . $key . ']]', $value, $order_product_code);
								}
							}

							$order_product_data[] = $order_product_code;
						}

						$conversion_code = str_replace(substr($conversion_code, $start_block, (($end_block + strlen($end_str)) - $start_block)), implode("\n", $order_product_data), $conversion_code);
					}
				} while ($start_block !== false && $end_block !== false);
			}
		}

		return $conversion_code;
	}
}