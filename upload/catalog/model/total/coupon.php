<?php
class ModelTotalCoupon extends Model {
	/* update by habib*/
	// new fucntion for find out  coupon deatls according to coupon id ; 
	public function getCouponById($coupon_id) {
		$status = true;

		$coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE coupon_id = '" . $this->db->escape($coupon_id) . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");

		if ($coupon_query->num_rows) {
			return array(
				'coupon_id'       => $coupon_query->row['coupon_id'],
				'code'            => $coupon_query->row['code'],
				'name'            => $coupon_query->row['name'],
				'type'            => $coupon_query->row['type'],
				'discount'        => $coupon_query->row['discount'],
				'recurring'       => $coupon_query->row['recurring'],
				'recurring_limit' => $coupon_query->row['recurring_limit'],
				'total'           => $coupon_query->row['total'],
				'date_start'      => $coupon_query->row['date_start'],
				'date_end'        => $coupon_query->row['date_end'],
				'uses_total'      => $coupon_query->row['uses_total'],
				'uses_customer'   => $coupon_query->row['uses_customer'],
				'status'          => $coupon_query->row['status'],
				'date_added'      => $coupon_query->row['date_added']
			);
		} 
	}
	/* update end  by habib*/
	public function getCoupon($code) {
		$status = true;

		$coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($code) . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");

		if ($coupon_query->num_rows) {
			if ($coupon_query->row['total'] > $this->cart->getSubTotal()) {
				$status = false;
			}

			$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

			if ($coupon_query->row['uses_total'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_total'])) {
				$status = false;
			}

			if ($this->customer->getId()) {
				$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "' AND ch.customer_id = '" . (int)$this->customer->getId() . "'");

				if ($coupon_query->row['uses_customer'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_customer'])) {
					$status = false;
				}
			}

			// Products
			$coupon_product_data = array();

			$coupon_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_product` WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

			foreach ($coupon_product_query->rows as $product) {
				$coupon_product_data[] = $product['product_id'];
			}

			// Categories
			$coupon_category_data = array();

			$coupon_category_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_category` cc LEFT JOIN `" . DB_PREFIX . "category_path` cp ON (cc.category_id = cp.path_id) WHERE cc.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

			foreach ($coupon_category_query->rows as $category) {
				$coupon_category_data[] = $category['category_id'];
			}

			$product_data = array();

			if ($coupon_product_data || $coupon_category_data) {
				foreach ($this->cart->getProducts() as $product) {
					if (in_array($product['product_id'], $coupon_product_data)) {
						$product_data[] = $product['product_id'];

						continue;
					}

					foreach ($coupon_category_data as $category_id) {
						$coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product['product_id'] . "' AND category_id = '" . (int)$category_id . "'");

						if ($coupon_category_query->row['total']) {
							$product_data[] = $product['product_id'];

							continue;
						}
					}
				}

				if (!$product_data) {
					$status = false;
				}
			}
		} else {
			$status = false;
		}

		if ($status) {
			return array(
				'coupon_id'       => $coupon_query->row['coupon_id'],
				'code'            => $coupon_query->row['code'],
				'name'            => $coupon_query->row['name'],
				'type'            => $coupon_query->row['type'],
				'discount'        => $coupon_query->row['discount'],
				'recurring'       => $coupon_query->row['recurring'],
				'recurring_limit' => $coupon_query->row['recurring_limit'],
				'total'           => $coupon_query->row['total'],
				'product'         => $product_data,
				'date_start'      => $coupon_query->row['date_start'],
				'date_end'        => $coupon_query->row['date_end'],
				'uses_total'      => $coupon_query->row['uses_total'],
				'uses_customer'   => $coupon_query->row['uses_customer'],
				'status'          => $coupon_query->row['status'],
				'date_added'      => $coupon_query->row['date_added']
			);
		}
	}

	public function getRecurringCoupon($coupon_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE coupon_id = '" . (int)$coupon_id . "' AND status = '1'");

		return $query->row;
	}

	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('coupon_status')) {
			$discount_data = array();

			if (isset($this->session->data['coupon'])) {
				$this->load->language('total/coupon');

				$coupon_info = $this->getCoupon($this->session->data['coupon']);

				if ($coupon_info) {
					$discount_data[$coupon_info['code']] = 0;

					foreach ($this->cart->getProducts() as $product) {
						$discount = 0;

						if (!$coupon_info['product'] || in_array($product['product_id'], $coupon_info['product'])) {
							if ($coupon_info['type'] == 'F') {
								$discount = min($coupon_info['discount'], $product['price']) * $product['quantity'];
							} else if ($coupon_info['type'] == 'P') {
								$discount = $product['total'] / 100 * $coupon_info['discount'];
							}

							if ($discount > $product['discount']) {
								$this->cart->updateDiscount($product['cart_id'], $discount, $coupon_info['coupon_id']);

								$discount -= $product['discount'];

								if ($product['tax_class_id']) {
									$tax_rates = $this->tax->getRates($discount, $product['tax_class_id']);

									foreach ($tax_rates as $tax_rate) {
										if ($tax_rate['type'] == 'P') {
											$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
										}
									}
								}

								$discount_data[$coupon_info['code']] += $discount;
							}
						}
					}

					if ($discount_data[$coupon_info['code']] > 0) {
						if ($coupon_info['recurring']) {
							$this->session->data['recurring_coupon'] = $coupon_info;
						} else if (isset($this->session->data['recurring_coupon'])) {
							unset($this->session->data['recurring_coupon']);
						}
					} else {
						unset($this->session->data['coupon']);

						if (isset($this->session->data['recurring_coupon'])) {
							unset($this->session->data['recurring_coupon']);
						}
					}
				} else {
					unset($this->session->data['coupon']);

					if (isset($this->session->data['recurring_coupon'])) {
						unset($this->session->data['recurring_coupon']);
					}
				}
			} else {
				if (isset($this->session->data['recurring_coupon'])) {
					unset($this->session->data['recurring_coupon']);
				}
			}

			// Recurring Coupons
			if (isset($this->session->data['recurring_coupons'])) {
				$this->load->language('total/coupon');

				foreach ($this->session->data['recurring_coupons'] as $key => $recurring_coupon) {
					$coupon_info = $this->getRecurringCoupon($recurring_coupon['coupon_id']);

					if ($coupon_info) {
						$product = $this->cart->getProduct($recurring_coupon['cart_id']);

						if ($product) {
							if (!isset($discount_data[$coupon_info['code']])) {
								$discount_data[$coupon_info['code']] = 0;
							}

							$discount = 0;

							if ($coupon_info['type'] == 'F') {
								$discount = min($coupon_info['discount'], $product['price']) * $product['quantity'];
							} else if ($coupon_info['type'] == 'P') {
								$discount = $product['total'] / 100 * $coupon_info['discount'];
							}

							if ($discount > $product['discount']) {
								$this->cart->updateDiscount($product['cart_id'], $discount, $recurring_coupon['cart_id']);

								$discount -= $product['discount'];

								if ($product['tax_class_id']) {
									$tax_rates = $this->tax->getRates($discount, $product['tax_class_id']);

									foreach ($tax_rates as $tax_rate) {
										if ($tax_rate['type'] == 'P') {
											$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
										}
									}
								}

								$discount_data[$coupon_info['code']] += $discount;
							}
						}
					}
				}
			}

			foreach ($discount_data as $code => $discount) {
				if ($discount > $total) {
					$discount = $total;
				}

				if ($discount > 0) {
					$total_data[] = array(
						'code'       => 'coupon',
						'title'      => sprintf($this->language->get('text_coupon'), $code),
						'value'      => -$discount,
						'sort_order' => $this->config->get('coupon_sort_order')
					);

					$total -= $discount;
				}
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end   = strrpos($order_total['title'], ')');

		if ($start && $end) {
			$code = substr($order_total['title'], $start, $end - $start);
		}

		if ($code) {
			$coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($code) . "'");

			if ($coupon_query->row) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "coupon_history` SET coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', customer_id = '" . (int)$order_info['customer_id'] . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");

				// tie non-affiliate customer to affiliate if this coupon is tied to an affiliate
				if ($coupon_query->row['affiliate_id']) {
					$this->load->model('customer/customer');

					$customer_info = $this->model_customer_customer->getCustomer($this->customer->getId());

					if (!$customer_info['affiliate_id']) {
						$this->model_customer_customer->editAffiliate($coupon_query->row['affiliate_id']);
					}
				}
			} else {
				return $this->config->get('config_fraud_status_id');
			}
		}
	}

	public function unconfirm($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "coupon_history` WHERE order_id = '" . (int)$order_id . "'");
	}
}