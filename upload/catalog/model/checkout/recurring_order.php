<?php
class ModelCheckoutRecurringOrder extends Model {
	public function addRecurringOrders($order_info, $metadata) {
		$order_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_info['order_id'] . "'");

		foreach ($order_product_query->rows as $product) {
			if ($product['recurring_id']) {
				// TODO: update this in the future when we add recurring upsells
				if (($recurring_order = $this->getActiveRecurringOrder($this->customer->getId(), $product['account_id']))) {
					return $this->editRecurringOrder($recurring_order['recurring_order_id'], $product, $order_info, $metadata);
				}

				$product['recurring_price'] = $this->currency->format($product['recurring_price'], $this->config->get('config_currency'), '', false);
				$product['trial_price']     = $this->currency->format($product['trial_price'], $this->config->get('config_currency'), '', false);

				if ($product['trial_status'] && $product['trial_duration']) {
					$date_next_recurring = date('Y-m-d', strtotime('+' . $product['trial_cycle'] . ' ' . $product['trial_frequency']));
				} else {
					$product['trial_duration'] = 0;

					$date_next_recurring = date('Y-m-d', strtotime('+' . $product['recurring_cycle'] . ' ' . $product['recurring_frequency']));
				}

				if (isset($this->session->data['recurring_coupon'])) {
					if ($this->session->data['recurring_coupon']['recurring_limit'] > 1) {
						$coupon_id        = $this->session->data['recurring_coupon']['coupon_id'];
						$coupon_remaining = $this->session->data['recurring_coupon']['recurring_limit'] - 1;
					} else {
						if (!$this->session->data['recurring_coupon']['recurring_limit']) {
							$coupon_id = $this->session->data['recurring_coupon']['coupon_id'];
						} else {
							$coupon_id = 0;
						}

						$coupon_remaining = 0;
					}
				} else {
					$coupon_id        = 0;
					$coupon_remaining = 0;
				}

				$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring_order` SET store_id = '" . (int)$this->config->get('config_store_id') . "', customer_id = '" . (int)$this->customer->getId() . "', account_id = '" . $this->db->escape($product['account_id']) . "', account_type = '" . $this->db->escape($product['account_type']) . "', account_username = '" . $this->db->escape($product['account_username']) . "', product_id = '" . (int)$product['product_id'] . "', product_name = '" . $this->db->escape($product['name']) . "', product_quantity = '" . $this->db->escape($product['quantity']) . "', recurring_id = '" . (int)$product['recurring_id'] . "', recurring_price = '" . (float)$product['recurring_price'] . "', recurring_cycle = '" . (int)$product['recurring_cycle'] . "', recurring_frequency = '" . $this->db->escape($product['recurring_frequency']) . "', recurring_duration = '" . (int)$product['recurring_duration'] . "', trial_status = '" . (int)$product['trial_status'] . "', trial_price = '" . (float)$product['trial_price'] . "', trial_cycle = '" . (int)$product['trial_cycle'] . "', trial_frequency = '" . $this->db->escape($product['trial_frequency']) . "', trial_duration = '" . (int)$product['trial_duration'] . "', payment_country = '" . $this->db->escape($order_info['payment_country']) . "', payment_country_id = '" . (int)$order_info['payment_country_id'] . "', payment_method = '" . $this->db->escape($order_info['payment_method']) . "', payment_code = '" . $this->db->escape($order_info['payment_code']) . "',  coupon_id = '" . (int)$coupon_id . "', coupon_remaining = '" . (int)$coupon_remaining . "', language_id = '" . (int)$order_info['language_id'] . "', currency_id = '" . (int)$order_info['currency_id'] . "', currency_code = '" . $this->db->escape($order_info['currency_code']) . "', currency_value = '" . (float)$order_info['currency_value'] . "', active = '1', date_next_recurring = '" . $this->db->escape($date_next_recurring) . "', metadata = '" . $this->db->escape(json_encode($metadata)) . "', date_added = NOW()");

				$recurring_order_id = $this->db->getLastId();

				$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET recurring_order_id = '" . (int)$recurring_order_id . "' WHERE order_product_id = '" . (int)$product['order_product_id'] . "'");
			}
		}
	}

	public function editRecurringOrder($recurring_order_id, $product, $order_info, $metadata) {
		if ($product['recurring_id']) {
			$product['recurring_price'] = $this->currency->format($product['recurring_price'], $this->config->get('config_currency'), '', false);
			$product['trial_price']     = $this->currency->format($product['trial_price'], $this->config->get('config_currency'), '', false);

			if ($product['trial_status'] && $product['trial_duration']) {
				$date_next_recurring = date('Y-m-d', strtotime('+' . $product['trial_cycle'] . ' ' . $product['trial_frequency']));
			} else {
				$product['trial_duration'] = 0;

				$date_next_recurring = date('Y-m-d', strtotime('+' . $product['recurring_cycle'] . ' ' . $product['recurring_frequency']));
			}

			if (isset($this->session->data['recurring_coupon'])) {
				if ($this->session->data['recurring_coupon']['recurring_limit'] > 1) {
					$coupon_id        = $this->session->data['recurring_coupon']['coupon_id'];
					$coupon_remaining = $this->session->data['recurring_coupon']['recurring_limit'] - 1;
				} else {
					if (!$this->session->data['recurring_coupon']['recurring_limit']) {
						$coupon_id = $this->session->data['recurring_coupon']['coupon_id'];
					} else {
						$coupon_id = 0;
					}

					$coupon_remaining = 0;
				}
			} else {
				$coupon_id        = 0;
				$coupon_remaining = 0;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET store_id = '" . (int)$this->config->get('config_store_id') . "', customer_id = '" . (int)$this->customer->getId() . "', account_id = '" . $this->db->escape($product['account_id']) . "', account_type = '" . $this->db->escape($product['account_type']) . "', account_username = '" . $this->db->escape($product['account_username']) . "', product_id = '" . (int)$product['product_id'] . "', product_name = '" . $this->db->escape($product['name']) . "', product_quantity = '" . $this->db->escape($product['quantity']) . "', recurring_id = '" . (int)$product['recurring_id'] . "', recurring_price = '" . (float)$product['recurring_price'] . "', recurring_cycle = '" . (int)$product['recurring_cycle'] . "', recurring_frequency = '" . $this->db->escape($product['recurring_frequency']) . "', recurring_duration = '" . (int)$product['recurring_duration'] . "', trial_status = '" . (int)$product['trial_status'] . "', trial_price = '" . (float)$product['trial_price'] . "', trial_cycle = '" . (int)$product['trial_cycle'] . "', trial_frequency = '" . $this->db->escape($product['trial_frequency']) . "', trial_duration = '" . (int)$product['trial_duration'] . "', payment_country = '" . $this->db->escape($order_info['payment_country']) . "', payment_country_id = '" . (int)$order_info['payment_country_id'] . "', payment_method = '" . $this->db->escape($order_info['payment_method']) . "', payment_code = '" . $this->db->escape($order_info['payment_code']) . "',  coupon_id = '" . (int)$coupon_id . "', coupon_remaining = '" . (int)$coupon_remaining . "', language_id = '" . (int)$order_info['language_id'] . "', currency_id = '" . (int)$order_info['currency_id'] . "', currency_code = '" . $this->db->escape($order_info['currency_code']) . "', currency_value = '" . (float)$order_info['currency_value'] . "', active = '1', date_next_recurring = '" . $this->db->escape($date_next_recurring) . "', metadata = '" . $this->db->escape(json_encode($metadata)) . "' WHERE recurring_order_id = '" . (int)$recurring_order_id . "'");

			$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET recurring_order_id = '" . (int)$recurring_order_id . "' WHERE order_product_id = '" . (int)$product['order_product_id'] . "'");
		}
	}

	public function updatePaymentMethodForAccount($customer_id, $account_id, $payment_method, $payment_code, $metadata) {
		$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET payment_method = '" . $this->db->escape($payment_method) . "', payment_code = '" . $this->db->escape($payment_code) . "', metadata = '" . $this->db->escape(json_encode($metadata)) . "' WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "' AND active = '1'");
	}

	public function getRecurringOrders($data) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "recurring_order`";

		$where_data = array();

		if (isset($data['filter_customer_id'])) {
			$where_data[] = "customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (isset($data['filter_account_id'])) {
			$where_data[] = "account_id = '" . $this->db->escape($data['filter_account_id']) . "'";
		}

		if (isset($data['filter_active'])) {
			$where_data[] = "active = '" . (int)$data['filter_active'] . "'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getActiveRecurringOrder($customer_id, $account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "' AND active = '1' ORDER BY recurring_order_id DESC LIMIT 1");

		return $query->row;
	}

	public function getLastRecurringOrder($customer_id, $account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "' ORDER BY active DESC, recurring_order_id DESC LIMIT 1");

		return $query->row;
	}
}