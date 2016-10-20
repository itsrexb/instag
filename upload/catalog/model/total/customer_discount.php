<?php
class ModelTotalCustomerDiscount extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('customer_discount_status')) {
			$discount_percent = $this->customer->getDiscount();

			if ($discount_percent) {
				$discount_total = 0;

				foreach ($this->cart->getProducts() as $product) {
					$discount = $product['total'] / 100 * $discount_percent;

					if ($discount > $product['discount']) {
						$this->cart->updateDiscount($product['cart_id'], $discount);

						$discount -= $product['discount'];

						if ($product['tax_class_id']) {
							$tax_rates = $this->tax->getRates($discount, $product['tax_class_id']);

							foreach ($tax_rates as $tax_rate) {
								if ($tax_rate['type'] == 'P') {
									$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
								}
							}
						}

						$discount_total += $discount;
					}
				}

				if ($discount_total > $total) {
					$discount_total = $total;
				}

				if ($discount_total > 0) {
					$this->load->language('total/customer_discount');

					$total_data[] = array(
						'code'       => 'customer_discount',
						'title'      => $this->language->get('text_customer_discount'),
						'value'      => -$discount_total,
						'sort_order' => $this->config->get('customer_discount_sort_order')
					);

					$total -= $discount_total;
				}
			}
		}
	}
}