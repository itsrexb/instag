<?php
class ModelExtensionMarketingMailchimp extends Model {
	public function customerLogin($customer_id) {
		if ($this->validate()) {
			if (!$this->getCustomerMailchimpId($customer_id)) {
				$this->updateCustomer($customer_id);
			}
		}
	}

	public function localSync($customer_id, &$customer_data) {
		if ($this->validate()) {
			$mailchimp_id = $this->getCustomerMailchimpId($customer_id);

			if ($mailchimp_id) {
				$member_info = $this->mailchimp->lists->get_member($this->config->get('mailchimp_customer_list_id'), $mailchimp_id);

				if (isset($member_info->id)) {
					if ($member_info->status == 'subscribed' && !$customer_data['newsletter']) {
						$customer_data['newsletter'] = 1;

						$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET newsletter = '1' WHERE customer_id = '" . (int)$customer_id . "'");
					} else if ($member_info->status == 'unsubscribed' && $customer_data['newsletter']) {
						$customer_data['newsletter'] = 0;

						$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET newsletter = '0' WHERE customer_id = '" . (int)$customer_id . "'");
					}
				}
			} else {
				$this->updateCustomer($customer_id, $customer_data);
			}
		}
	}

	public function updateCustomer($customer_id, $customer_data = array()) {
		if ($this->validate()) {
			if (!$customer_data) {
				$this->load->model('customer/customer');
				$customer_data = $this->model_customer_customer->getCustomer($customer_id);
			}

			if ($customer_data) {
				$newsletter = (isset($customer_data['newsletter']) ? $customer_data['newsletter'] : $this->customer->getNewsletter());

				$mailchimp_data = array(
					'status'        => ($newsletter ? 'subscribed' : 'unsubscribed'),
					'email_address' => $customer_data['email'],
					'language'      => ($customer_data['language_code'] ? $customer_data['language_code'] : $this->config->get('config_language')),
					'merge_fields'  => array(
						'FNAME' => $customer_data['firstname'],
						'LNAME' => $customer_data['lastname']
					)
				);

				$ecommerce_data = array(
					'id'            => (string)$customer_id,
					'email_address' => $customer_data['email'],
					'opt_in_status' => (bool)$newsletter,
					'first_name'    => $customer_data['firstname'],
					'last_name'     => $customer_data['lastname']
				);

				if ($this->config->get('mailchimp_account_status_tag')) {
					$mailchimp_data['merge_fields'][$this->config->get('mailchimp_account_status_tag')] = $this->getCustomerAccountStatus($customer_id);
				}

				if ($customer_data['country_id'] && $this->config->get('mailchimp_country_tag')) {
					$this->load->model('localisation/country');
					$country_info = $this->model_localisation_country->getCountry($customer_data['country_id']);

					if ($country_info) {
						$mailchimp_data['merge_fields'][$this->config->get('mailchimp_country_tag')] = $country_info['name'];

						$ecommerce_data['address'] = array(
							'country' => $country_info['name']
						);
					}
				}

				if ($this->config->get('mailchimp_currency_tag')) {
					$mailchimp_data['merge_fields'][$this->config->get('mailchimp_currency_tag')] = $customer_data['currency_code'];
				}

				if ($this->config->get('mailchimp_plan_tag')) {
					$customer_plan_data = $this->getCustomerPlan($customer_id);

					if ($customer_plan_data) {
						$mailchimp_data['merge_fields'][$this->config->get('mailchimp_plan_tag')] = $customer_plan_data['name'];
					} else {
						$mailchimp_data['merge_fields'][$this->config->get('mailchimp_plan_tag')] = '';
					}
				}

				$mailchimp_id = (isset($customer_data['mailchimp_id']) ? $customer_data['mailchimp_id'] : $this->getCustomerMailchimpId($customer_id));

				$result = $this->mailchimp->lists->update_member($this->config->get('mailchimp_customer_list_id'), $mailchimp_data, $mailchimp_id);

				if (isset($result->id) && $result->id != $mailchimp_id) {
					$this->updateCustomerMailchimpId($customer_id, $result->id);
				}

				// update mailchimp customer
				if ($this->config->get('mailchimp_store_id')) {
					// get orders_count and total_spent
					$customer_order_data = $this->getCustomerTotalOrders($customer_id);

					$ecommerce_data['orders_count'] = (int)$customer_order_data['orders_count'];
					$ecommerce_data['total_spent']  = (float)$this->currency->format($customer_order_data['total_spent'], $this->config->get('config_currency'), '', false);

					$this->mailchimp->ecommerce->update_customer($this->config->get('mailchimp_store_id'), $customer_id, $ecommerce_data);
				}
			}
		}
	}

	public function addOrder($order_id, $order_info = array(), $order_products = array()) {
		if ($this->validate() && $this->config->get('mailchimp_store_id')) {
			if (!$order_info) {
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($order_id);
			}

			$ecommerce_data = array(
				'id'       => (string)$order_id,
				'customer' => array(
					'id'            => (string)$order_info['customer_id'],
					'email_address' => $order_info['email'],
					'opt_in_status' => (bool)$this->customer->getNewsletter(),
					'first_name'    => $order_info['firstname'],
					'last_name'     => $order_info['lastname'],
				),
				'currency_code'        => $this->config->get('config_currency'),
				'order_total'          => $this->currency->format($order_info['total'], $this->config->get('config_currency'), '', false),
				'processed_at_foreign' => $order_info['date_added'],
				'updated_at_foreign'   => $order_info['date_modified'],
				'lines'                => array()
			);

			if (!empty($this->request->cookie['mc_cid'])) {
				$ecommerce_data['campaign_id'] = $this->request->cookie['mc_cid'];

				unset($this->request->cookie['mc_cid']);
			}

			if (!$order_products) {
				$this->load->model('checkout/order');
				$order_products = $this->model_checkout_order->getOrderProducts($order_id);
			}

			// add order products to ecommerce line data
			foreach ($order_products as $order_product) {
				$ecommerce_data['lines'][] = array(
					'id'                 => (string)$order_product['order_product_id'],
					'product_id'         => (string)$order_product['product_id'],
					'product_variant_id' => (string)$order_product['product_id'],
					'quantity'           => (int)$order_product['quantity'],
					'price'              => (float)$this->currency->format($order_product['total'], $this->config->get('config_currency'), '', false)
				);
			}

			// get orders_count and total_spent
			$customer_order_data = $this->getCustomerTotalOrders($order_info['customer_id']);

			$ecommerce_data['customer']['orders_count'] = (int)$customer_order_data['orders_count'];
			$ecommerce_data['customer']['total_spent']  = (float)$this->currency->format($customer_order_data['total_spent'], $this->config->get('config_currency'), '', false);

			$this->mailchimp->ecommerce->add_order($this->config->get('mailchimp_store_id'), $ecommerce_data);
		}
	}

	public function validate() {
		if ($this->config->get('mailchimp_api_key') && $this->config->get('mailchimp_customer_list_id')) {
			return true;
		}

		return false;
	}

	private function updateCustomerMailchimpId($customer_id, $mailchimp_id) {
		$query = $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET mailchimp_id = '" . $this->db->escape($mailchimp_id) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	private function getCustomerMailchimpId($customer_id) {
		$query = $this->db->query("SELECT mailchimp_id FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");

		return ($query->row ? $query->row['mailchimp_id'] : '');
	}

	private function getCustomerTotalOrders($customer_id) {
		$query = $this->db->query("SELECT COUNT(order_id) AS orders_count, SUM(total) AS total_spent FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$customer_id . "' AND order_status_id > '0'");

		return $query->row;
	}

	private function getCustomerPlan($customer_id) {
		$setting_query = $this->db->query("SELECT l.language_id FROM `" . DB_PREFIX . "setting` s LEFT JOIN `" . DB_PREFIX . "language` l ON (l.code = s.value) WHERE s.`key` = 'config_language'");

		$language_id = ($setting_query->row ? $setting_query->row['language_id'] : $this->config->get('config_language_id'));

		$query = $this->db->query("SELECT ro.product_id, pd.name FROM `" . DB_PREFIX . "recurring_order` ro LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = ro.product_id AND pd.language_id = '" . (int)$language_id . "') WHERE ro.customer_id = '" . (int)$customer_id . "' AND ro.active = '1' ORDER BY ro.recurring_price DESC LIMIT 1");

		return $query->row;
	}

	private function getCustomerAccountStatus($customer_id) {
		$query = $this->db->query("SELECT (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') AS total_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '1') AS deleted_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') AS active_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires = '0000-00-00 00:00:00') AS kickoff_accounts, (SELECT COUNT(DISTINCT ro.account_id) FROM `" . DB_PREFIX . "recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') AS active_recurring_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.customer_id IN (SELECT customer_id FROM `" . DB_PREFIX . "recurring_order` WHERE account_id = ca.account_id AND active = '0') AND ca.date_expires != '0000-00-00 00:00:00' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') AS inactive_recurring_accounts, (SELECT SUM(o.total) FROM `" . DB_PREFIX . "order` o WHERE o.customer_id = c.customer_id AND o.order_status_id IN ('" . implode("','", $this->config->get('config_complete_status')) . "')) AS total_revenue FROM `" . DB_PREFIX . "customer` c WHERE c.customer_id = '" . (int)$customer_id . "'");

		if ($query->row) {
			if ($query->row['total_accounts']) {
				if ($query->row['active_accounts']) {
					// active_accounts > 0
					if ($query->row['active_recurring_accounts']) {
						// active_recurring_accounts > 0
						return 'Active';
					} else {
						// active_recurring_accounts = 0
						if ($query->row['inactive_recurring_accounts']) {
							// inactive_recurring_accounts > 0
							return 'Inactive';
						} else {
							// inactive_recurring_accounts = 0
							return 'Free Trial';
						}
					}
				} else {
					// active_accounts = 0
					if ($query->row['kickoff_accounts']) {
						// kickoff_accounts > 0
						return 'Kickoff';
					} else {
						// kickoff_accounts = 0
						if ($query->row['total_revenue']) {
							// total_revenue > 0
							return 'Expired (Paid)';
						} else {
							// total_revenue = 0
							return 'Expired';
						}
					}
				}
			} else {
				// total_accounts = 0
				if ($query->row['deleted_accounts']) {
					// deleted_accounts > 0
					if ($query->row['total_revenue']) {
						// total_revenue > 0
						return 'Deleted (Paid)';
					} else {
						// total_revenue = 0
						return 'Deleted';
					}
				} else {
					// deleted_accounts = 0
					return 'New';
				}
			}
		} else {
			return 'New';
		}
	}
}