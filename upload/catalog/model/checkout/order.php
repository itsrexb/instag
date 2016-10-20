<?php
class ModelCheckoutOrder extends Model {
	public function addOrder($data) {
		$set_data = array();

		$set_data[] = "invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "'";
		$set_data[] = "store_id = '" . (int)$data['store_id'] . "'";
		$set_data[] = "store_name = '" . $this->db->escape($data['store_name']) . "'";
		$set_data[] = "store_url = '" . $this->db->escape($data['store_url']) . "'";
		$set_data[] = "customer_id = '" . (int)$data['customer_id'] . "'";
		$set_data[] = "customer_group_id = '" . (int)$data['customer_group_id'] . "'";
		$set_data[] = "firstname = '" . $this->db->escape($data['firstname']) . "'";
		$set_data[] = "lastname = '" . $this->db->escape($data['lastname']) . "'";
		$set_data[] = "email = '" . $this->db->escape($data['email']) . "'";
		$set_data[] = "telephone = '" . $this->db->escape($data['telephone']) . "'";
		$set_data[] = "custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "'";

		if (isset($data['payment_country'])) {
			$set_data[] = "payment_country = '" . $this->db->escape($data['payment_country']) . "'";
		}

		if (isset($data['payment_country_id'])) {
			$set_data[] = "payment_country_id = '" . (int)$data['payment_country_id'] . "'";
		}

		$set_data[] = "payment_method = '" . $this->db->escape($data['payment_method']) . "'";
		$set_data[] = "payment_code = '" . $this->db->escape($data['payment_code']) . "'";
		$set_data[] = "comment = '" . $this->db->escape($data['comment']) . "'";
		$set_data[] = "total = '" . (float)$data['total'] . "'";
		$set_data[] = "language_id = '" . (int)$data['language_id'] . "'";
		$set_data[] = "currency_id = '" . (int)$data['currency_id'] . "'";
		$set_data[] = "currency_code = '" . $this->db->escape($data['currency_code']) . "'";
		$set_data[] = "currency_value = '" . (float)$data['currency_value'] . "'";
		$set_data[] = "ip = '" . $this->db->escape($data['ip']) . "'";
		$set_data[] = "forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "'";
		$set_data[] = "user_agent = '" . $this->db->escape($data['user_agent']) . "'";
		$set_data[] = "accept_language = '" . $this->db->escape($data['accept_language']) . "'";

		if (isset($data['user_id'])) {
			$set_data[] = "user_id = '" . (int)$data['user_id'] . "'";
		}

		if (isset($data['recurring_order'])) {
			$set_data[] = "recurring_order = '" .(int)$data['recurring_order'] . "'";
		}

		$set_data[] = "date_added = NOW()";
		$set_data[] = "date_modified = NOW()";

		if ($set_data) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET " . implode(", ", $set_data));

			$order_id = $this->db->getLastId();

			// Products
			if (isset($data['products'])) {
				foreach ($data['products'] as $product) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "order_product` SET order_id = '" . (int)$order_id . "', recurring_order_id = '" . (int)(isset($product['recurring_order_id']) ? $product['recurring_order_id'] : 0) . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', account_id = '" . $this->db->escape($product['account_id']) . "', account_type = '" . $this->db->escape($product['account_type']) . "', account_username = '" . $this->db->escape($product['account_username']) . "', time_extension = '" . (int)$product['time_extension'] . "', time_extension_frequency = '" . $this->db->escape($product['time_extension_frequency']) . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', discount = '" . -(float)$product['discount'] . "', coupon_id = '" . (int)$product['coupon_id'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

					$order_product_id = $this->db->getLastId();

					if ($product['recurring']) {
						$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET recurring_id = '" . (int)$product['recurring']['recurring_id'] . "', recurring_price = '" . (float)$product['recurring']['price'] . "', recurring_cycle = '" . (int)$product['recurring']['cycle'] . "', recurring_frequency = '" . $this->db->escape($product['recurring']['frequency']) . "', recurring_duration = '" . (int)$product['recurring']['duration'] . "', trial_status = '" . (int)$product['recurring']['trial_status'] . "', trial_price = '" . (float)$product['recurring']['trial_price'] . "', trial_cycle = '" . (int)$product['recurring']['trial_cycle'] . "', trial_frequency = '" . $this->db->escape($product['recurring']['trial_frequency']) . "', trial_duration = '" . (int)$product['recurring']['trial_duration'] . "' WHERE order_product_id = '" . (int)$order_product_id . "'");
					}
				}
			}

			// Gift Voucher
			$this->load->model('total/voucher');

			// Vouchers
			if (isset($data['vouchers'])) {
				foreach ($data['vouchers'] as $voucher) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "order_voucher` SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");

					$order_voucher_id = $this->db->getLastId();

					$voucher_id = $this->model_total_voucher->addVoucher($order_id, $voucher);

					$this->db->query("UPDATE `" . DB_PREFIX . "order_voucher` SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher_id . "'");
				}
			}

			// Totals
			if (isset($data['totals'])) {
				foreach ($data['totals'] as $total) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
				}
			}

			return $order_id;
		}

		return 0;
	}

	public function editOrder($order_id, $data) {
		// Void the order first
		$this->addOrderHistory($order_id, 0);

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(json_encode($data['custom_field'])) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");

		// Products
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "order_product` SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', account_id = '" . $this->db->escape($product['account_id']) . "', account_type = '" . $this->db->escape($product['account_type']) . "', account_username = '" . $this->db->escape($product['account_username']) . "', time_extension = '" . (int)$product['time_extension'] . "', time_extension_frequency = '" . $this->db->escape($product['time_extension_frequency']) . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', discount = '" . -(float)$product['discount'] . "', coupon_id = '" . (int)$product['coupon_id'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

				if ($product['recurring']) {
					$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET recurring_id = '" . (int)$product['recurring']['recurring_id'] . "', recurring_name = '" . $this->db->escape($product['recurring']['name']) . "', recurring_price = '" . (float)$product['recurring']['price'] . "', recurring_cycle = '" . (int)$product['recurring']['cycle'] . "', recurring_frequency = '" . $this->db->escape($product['recurring']['frequency']) . "', recurring_duration = '" . (int)$product['recurring']['duration'] . "', trial = '" . (int)$product['recurring']['trial'] . "', trial_price = '" . (float)$product['recurring']['trial_price'] . "', trial_cycle = '" . (int)$product['recurring']['trial_cycle'] . "', trial_frequency = '" . $this->db->escape($product['recurring']['trial_frequency']) . "', trial_duration = '" . (int)$product['recurring']['trial_duration'] . "' WHERE order_product_id = '" . (int)$order_product_id . "'");
				}
			}
		}

		// Gift Voucher
		$this->load->model('total/voucher');

		$this->model_total_voucher->disableVoucher($order_id);

		// Vouchers
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		if (isset($data['vouchers'])) {
			foreach ($data['vouchers'] as $voucher) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "order_voucher` SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");

				$order_voucher_id = $this->db->getLastId();

				$voucher_id = $this->model_total_voucher->addVoucher($order_id, $voucher);

				$this->db->query("UPDATE `" . DB_PREFIX . "order_voucher` SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher_id . "'");
			}
		}

		// Totals
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "'");

		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}
	}

	public function deleteOrder($order_id) {
		// Void the order first
		$this->addOrderHistory($order_id, 0);

		$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_history` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");

		// Gift Voucher
		$this->load->model('total/voucher');

		$this->model_total_voucher->disableVoucher($order_id);
	}

	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code      = $language_info['code'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code      = '';
				$language_directory = '';
			}

			return array(
				'order_id'           => $order_query->row['order_id'],
				'invoice_no'         => $order_query->row['invoice_no'],
				'invoice_prefix'     => $order_query->row['invoice_prefix'],
				'store_id'           => $order_query->row['store_id'],
				'store_name'         => $order_query->row['store_name'],
				'store_url'          => $order_query->row['store_url'],
				'customer_id'        => $order_query->row['customer_id'],
				'firstname'          => $order_query->row['firstname'],
				'lastname'           => $order_query->row['lastname'],
				'email'              => $order_query->row['email'],
				'telephone'          => $order_query->row['telephone'],
				'custom_field'       => json_decode($order_query->row['custom_field'], true),
				'payment_country_id' => $order_query->row['payment_country_id'],
				'payment_country'    => $order_query->row['payment_country'],
				'payment_iso_code_2' => $payment_iso_code_2,
				'payment_iso_code_3' => $payment_iso_code_3,
				'payment_method'     => $order_query->row['payment_method'],
				'payment_code'       => $order_query->row['payment_code'],
				'comment'            => $order_query->row['comment'],
				'total'              => $order_query->row['total'],
				'order_status_id'    => $order_query->row['order_status_id'],
				'order_status'       => $order_query->row['order_status'],
				'language_id'        => $order_query->row['language_id'],
				'language_code'      => $language_code,
				'language_directory' => $language_directory,
				'currency_id'        => $order_query->row['currency_id'],
				'currency_code'      => $order_query->row['currency_code'],
				'currency_value'     => $order_query->row['currency_value'],
				'ip'                 => $order_query->row['ip'],
				'forwarded_ip'       => $order_query->row['forwarded_ip'],
				'user_agent'         => $order_query->row['user_agent'],
				'accept_language'    => $order_query->row['accept_language'],
				'date_modified'      => $order_query->row['date_modified'],
				'date_added'         => $order_query->row['date_added']
			);
		} else {
			return false;
		}
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false, $override = false) {
		$order_info = $this->getOrder($order_id);
		
		if ($order_info) {
			// NOT processing/complete -> processing/complete (Fraud Detection)
			if (!in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
				if ($override) {
					$safe = true;
				} else {
					$this->load->model('customer/customer');

					$customer_info = $this->model_customer_customer->getCustomer($order_info['customer_id']);

					$safe = ($customer_info ? $customer_info['safe'] : false);
				}

				// only do the fraud check if the customer is not on the safe list
				if (!$safe) {
					// Anti-Fraud
					$this->load->model('extension/extension');

					$extensions = $this->model_extension_extension->getExtensions('fraud');

					foreach ($extensions as $extension) {
						if ($this->config->get($extension['code'] . '_status')) {
							$this->load->model('fraud/' . $extension['code']);

							$fraud_status_id = $this->{'model_fraud_' . $extension['code']}->check($order_info);

							if ($fraud_status_id) {
								$order_status_id = $fraud_status_id;
							}
						}
					}
				}

				// Redeem coupon, vouchers and reward points
				$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

				foreach ($order_total_query->rows as $order_total) {
					$this->load->model('total/' . $order_total['code']);

					if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
					  // Confirm coupon, vouchers and reward points
					  $fraud_status_id = $this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);

					  // If the balance on the coupon, vouchers and reward points is not enough to cover the transaction or has already been used then the fraud order status is returned.
					  if ($fraud_status_id) {
						  $order_status_id = $fraud_status_id;
					  }
				  }
				}
			}

			// Update the DB with the new statuses
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

			$this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");

			// NOT processing/complete -> processing/complete (check again to make sure fraud wasn't detected)
			if (!in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
				// If a product is purchased that extends the time of an account, extend the time
				$this->load->model('checkout/account');
				$this->load->model('checkout/recurring_order');

				$order_product_query = $this->db->query("SELECT op.* FROM `" . DB_PREFIX . "order_product` op WHERE order_id = '" . (int)$order_id . "'");

				foreach ($order_product_query->rows as $order_product) {
					if ($order_product['account_id'] && $order_product['time_extension'] && $order_product['time_extension_frequency']) {
						if (isset($this->request->get['route']) && $this->request->get['route'] == 'cron/recurring_orders') {
							$this->model_checkout_account->updateExpiration($order_product['account_id'], $order_product['time_extension'], $order_product['time_extension_frequency']);
						} else {
							// if this account has had a recurring order, edit the expiration date (proration handles the credit), otherwise update the expiration date
							// TODO: this will need to be updated after we do upsells to only get recurring orders that extend the account time
							$recurring_orders = $this->model_checkout_recurring_order->getRecurringOrders(array(
								'filter_customer_id' => $order_info['customer_id'],
								'filter_account_id'  => $order_product['account_id']
							));

							if ($recurring_orders) {
								$this->model_checkout_account->editExpiration($order_product['account_id'], date('Y-m-d H:i:s', strtotime('+' . $order_product['time_extension'] . ' ' . $order_product['time_extension_frequency'])));
							} else {
								$this->model_checkout_account->updateExpiration($order_product['account_id'], $order_product['time_extension'], $order_product['time_extension_frequency']);
							}
						}
					}
				}
			}

			// processing/complete -> NOT processing/complete
			if (in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && !in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
				// Remove coupon, vouchers and reward points history
				$this->load->model('customer/order');

				$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

				foreach ($order_total_query->rows as $order_total) {
					$this->load->model('total/' . $order_total['code']);

					if (method_exists($this->{'model_total_' . $order_total['code']}, 'unconfirm')) {
						$this->{'model_total_' . $order_total['code']}->unconfirm($order_id);
					}
				}
			}

			$this->cache->delete('product');

			// missing -> pending/processing/complete
			if (!$order_info['order_status_id'] && $order_status_id) {
				$download_status = false;

				if (!isset($order_product_query)) {
					$order_product_query = $this->db->query("SELECT op.* FROM `" . DB_PREFIX . "order_product` op WHERE order_id = '" . (int)$order_id . "'");
				}

				foreach ($order_product_query->rows as $order_product) {
					// Check if there are any linked downloads
					$product_download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$order_product['product_id'] . "'");

					if ($product_download_query->row['total']) {
						$download_status = true;
						break;
					}
				}

				// Load the language for any mails that might be required to be sent out
				$language = new Language($order_info['language_directory']);
				$language->load($order_info['language_directory']);
				$language->load('mail/order');

				$order_status_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

				if ($order_status_query->num_rows) {
					$order_status = $order_status_query->row['name'];
				} else {
					$order_status = '';
				}

				$subject = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

				$mail_data = array();

				$mail_data['title'] = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_id);

				$mail_data['text_greeting']       = $language->get('text_new_greeting');
				$mail_data['text_download']       = $language->get('text_new_download');
				$mail_data['text_order_detail']   = $language->get('text_new_order_detail');
				$mail_data['text_instruction']    = $language->get('text_new_instruction');
				$mail_data['text_order_id']       = $language->get('text_new_order_id');
				$mail_data['text_date_added']     = $language->get('text_new_date_added');
				$mail_data['text_payment_method'] = $language->get('text_new_payment_method');
				$mail_data['text_customer']       = $language->get('text_new_customer');
				$mail_data['text_email']          = $language->get('text_new_email');
				$mail_data['text_telephone']      = $language->get('text_new_telephone');
				$mail_data['text_affiliate']      = $language->get('text_new_affiliate');
				$mail_data['text_ext_aff_id']     = $language->get('text_new_ext_aff_id');
				$mail_data['text_order_status']   = $language->get('text_new_order_status');
				$mail_data['text_product']        = $language->get('text_new_product');
				$mail_data['text_products']       = $language->get('text_new_products');
				$mail_data['text_order_total']    = $language->get('text_new_order_total');
				$mail_data['text_price']          = $language->get('text_new_price');
				$mail_data['text_total']          = $language->get('text_new_total');
				$mail_data['text_account']        = $language->get('text_new_account');
				$mail_data['text_footer']         = sprintf($language->get('text_new_footer'), date('Y'), $order_info['store_name']);

				$mail_data['logo']        = $this->config->get('config_url') . 'image/' . $this->config->get('config_email_logo');
				$mail_data['store_name']  = $order_info['store_name'];
				$mail_data['store_url']   = $order_info['store_url'];
				$mail_data['customer_id'] = $order_info['customer_id'];

				if ($download_status) {
					$mail_data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
				} else {
					$mail_data['download'] = '';
				}

				$mail_data['order_id']       = $order_id;
				$mail_data['date_added']     = date($language->get('date_format_short'), strtotime($order_info['date_added']));
				$mail_data['payment_method'] = $order_info['payment_method'];
				$mail_data['customer']       = $order_info['firstname'] . ' ' . $order_info['lastname'];
				$mail_data['email']          = $order_info['email'];
				$mail_data['telephone']      = $order_info['telephone'];
				$mail_data['affiliate']      = '';
				$mail_data['ext_aff_id']     = '';
				$mail_data['order_status']   = $order_status;

				if ($comment && $notify) {
					$mail_data['comment'] = nl2br($comment);
				} else {
					$mail_data['comment'] = '';
				}

				$this->load->model('tool/upload');

				// Products
				$mail_data['products'] = array();

				foreach ($order_product_query->rows as $product) {
					$mail_data['products'][] = array(
						'name'             => $product['name'],
						'account_type'     => $product['account_type'],
						'account_username' => $product['account_username'],
						'price'            => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'            => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				// Vouchers
				$mail_data['vouchers'] = array();

				$order_voucher_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

				foreach ($order_voucher_query->rows as $voucher) {
					$mail_data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}

				// Order Totals
				$mail_data['totals'] = array();

				$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

				foreach ($order_total_query->rows as $total) {
					$mail_data['totals'][] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}

				$mail = new Mail();
				$mail->protocol      = $this->config->get('config_mail_protocol');
				$mail->parameter     = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($order_info['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($this->load->view('mail/order', $mail_data));
				$mail->setText($this->load->view('mail/order_text', $mail_data));
				$mail->send();

				// Admin Alert Mail
				if ($this->config->get('config_order_mail') && (!isset($this->request->get['route']) || $this->request->get['route'] != 'cron/recurring_orders')) {
					$subject = sprintf($language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);

					// HTML Mail
					$mail_data['text_greeting'] = $language->get('text_new_received');

					if ($comment) {
						if ($order_info['comment']) {
							$mail_data['comment'] = nl2br($comment) . '<br/><br/>' . $order_info['comment'];
						} else {
							$mail_data['comment'] = nl2br($comment);
						}
					} else {
						if ($order_info['comment']) {
							$mail_data['comment'] = $order_info['comment'];
						} else {
							$mail_data['comment'] = '';
						}
					}

					$mail_data['text_download'] = '';
					$mail_data['text_footer']   = '';
					$mail_data['text_link']     = '';

					$mail_data['link']     = '';
					$mail_data['download'] = '';

					$this->load->model('customer/customer');

					$customer_info = $this->model_customer_customer->getCustomer($order_info['customer_id']);

					if ($customer_info) {
						if ($customer_info['affiliate_id']) {
							$this->load->model('affiliate/affiliate');

							$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($customer_info['affiliate_id']);

							if ($affiliate_info) {
								if ($affiliate_info['path']) {
									$mail_data['affiliate'] = $affiliate_info['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' . $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
								} else {
									$mail_data['affiliate'] = $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
								}
							}
						}

						if ($customer_info['ext_aff_id']) {
							$mail_data['ext_aff_id'] = $customer_info['ext_aff_id'];
						}
					}

					$mail = new Mail();
					$mail->protocol      = $this->config->get('config_mail_protocol');
					$mail->parameter     = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

					$mail->setTo($this->config->get('config_email'));
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
					$mail->setHtml($this->load->view('mail/order', $mail_data));
					$mail->setText($this->load->view('mail/order_text', $mail_data));
					$mail->send();

					// Send to additional alert emails
					$emails = explode(',', $this->config->get('config_mail_alert'));

					foreach ($emails as $email) {
						if ($email && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
							$mail->setTo($email);
							$mail->send();
						}
					}
				}
			}

			// If order status is not 0 then send update text email
			if ($order_info['order_status_id'] && $order_status_id && $notify) {
				$language = new Language($order_info['language_directory']);
				$language->load($order_info['language_directory']);
				$language->load('mail/order');

				$subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

				$message  = $language->get('text_update_order') . ' ' . $order_id . "\n";
				$message .= $language->get('text_update_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

				$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

				if ($order_status_query->num_rows) {
					$message .= $language->get('text_update_order_status') . "\n\n";
					$message .= $order_status_query->row['name'] . "\n\n";
				}

				if ($order_info['customer_id']) {
					$message .= $language->get('text_update_link') . "\n";
					$message .= $order_info['store_url'] . 'index.php?route=customer/order/info&order_id=' . $order_id . "\n\n";
				}

				if ($comment) {
					$message .= $language->get('text_update_comment') . "\n\n";
					$message .= strip_tags($comment) . "\n\n";
				}

				$message .= sprintf($language->get('text_update_footer'), date('Y'), $order_info['store_name']);

				$mail = new Mail();
				$mail->protocol      = $this->config->get('config_mail_protocol');
				$mail->parameter     = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($order_info['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText($message);
				$mail->send();
			}
		}
	}
	
	public function resendOrderEmail($order_id) {
		$order_info = $this->getOrder($order_id);
		$download_status = false;
		$order_product_query = $this->db->query("SELECT op.* FROM `" . DB_PREFIX . "order_product` op WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_product_query->rows as $order_product) {
			// Check if there are any linked downloads
			$product_download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$order_product['product_id'] . "'");

			if ($product_download_query->row['total']) {
				$download_status = true;
				break;
			}
		}

		// Load the language for any mails that might be required to be sent out
		$language = new Language($order_info['language_directory']);
		$language->load($order_info['language_directory']);
		$language->load('mail/order');

		$order_status_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_status_id = '" . (int)$order_info['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

		if ($order_status_query->num_rows) {
			$order_status = $order_status_query->row['name'];
		} else {
			$order_status = '';
		}

		$subject = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

		$mail_data = array();

		$mail_data['title'] = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_id);

		$mail_data['text_greeting']       = $language->get('text_new_greeting');
		$mail_data['text_download']       = $language->get('text_new_download');
		$mail_data['text_order_detail']   = $language->get('text_new_order_detail');
		$mail_data['text_instruction']    = $language->get('text_new_instruction');
		$mail_data['text_order_id']       = $language->get('text_new_order_id');
		$mail_data['text_date_added']     = $language->get('text_new_date_added');
		$mail_data['text_payment_method'] = $language->get('text_new_payment_method');
		$mail_data['text_customer']       = $language->get('text_new_customer');
		$mail_data['text_email']          = $language->get('text_new_email');
		$mail_data['text_telephone']      = $language->get('text_new_telephone');
		$mail_data['text_affiliate']      = $language->get('text_new_affiliate');
		$mail_data['text_ext_aff_id']     = $language->get('text_new_ext_aff_id');
		$mail_data['text_order_status']   = $language->get('text_new_order_status');
		$mail_data['text_product']        = $language->get('text_new_product');
		$mail_data['text_products']       = $language->get('text_new_products');
		$mail_data['text_order_total']    = $language->get('text_new_order_total');
		$mail_data['text_price']          = $language->get('text_new_price');
		$mail_data['text_total']          = $language->get('text_new_total');
		$mail_data['text_account']        = $language->get('text_new_account');
		$mail_data['text_footer']         = sprintf($language->get('text_new_footer'), date('Y'), $order_info['store_name']);

		$mail_data['logo']        = $this->config->get('config_url') . 'image/' . $this->config->get('config_email_logo');
		$mail_data['store_name']  = $order_info['store_name'];
		$mail_data['store_url']   = $order_info['store_url'];
		$mail_data['customer_id'] = $order_info['customer_id'];

		if (isset($download_status)) {
			$mail_data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
		} else {
			$mail_data['download'] = '';
		}

		$mail_data['order_id']       = $order_id;
		$mail_data['date_added']     = date($language->get('date_format_short'), strtotime($order_info['date_added']));
		$mail_data['payment_method'] = $order_info['payment_method'];
		$mail_data['customer']       = $order_info['firstname'] . ' ' . $order_info['lastname'];
		$mail_data['email']          = $order_info['email'];
		$mail_data['telephone']      = $order_info['telephone'];
		$mail_data['affiliate']      = '';
		$mail_data['ext_aff_id']     = '';
		$mail_data['order_status']   = $order_status;

		$mail_data['comment'] = '';

		$this->load->model('tool/upload');

		// Products
		$mail_data['products'] = array();

		foreach ($order_product_query->rows as $product) {
			$mail_data['products'][] = array(
				'name'             => $product['name'],
				'account_type'     => $product['account_type'],
				'account_username' => $product['account_username'],
				'price'            => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'            => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
			);
		}

		// Vouchers
		$mail_data['vouchers'] = array();

		$order_voucher_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_voucher_query->rows as $voucher) {
			$mail_data['vouchers'][] = array(
				'description' => $voucher['description'],
				'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}

		// Order Totals
		$mail_data['totals'] = array();

		$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

		foreach ($order_total_query->rows as $total) {
			$mail_data['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}

		$mail = new Mail();
		$mail->protocol      = $this->config->get('config_mail_protocol');
		$mail->parameter     = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($order_info['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($this->load->view('mail/order', $mail_data));
		$mail->setText($this->load->view('mail/order_text', $mail_data));
		$mail->send();
	}	

	public function getLastOrderForRecurringOrder($recurring_order_id) {
		$query = $this->db->query("SELECT op.order_id, (op.price + op.discount) AS total, o.date_added FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id) WHERE op.recurring_order_id = '" . (int)$recurring_order_id . "' GROUP BY op.order_product_id ORDER BY op.order_product_id DESC LIMIT 1");

		return $query->row;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT code, title, value FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getInvoiceData($order_id) {
		$data = array();

		$order_info = $this->getOrder($order_id);
		
		if ($order_info) {
			$download_status = false;

			$order_product_query = $this->db->query("SELECT op.*, (SELECT type FROM  `" . DB_PREFIX . "account` WHERE account_id = op.account_id) as type FROM `" . DB_PREFIX . "order_product` op WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_product_query->rows as $order_product) {
				// Check if there are any linked downloads
				$product_download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$order_product['product_id'] . "'");

				if ($product_download_query->row['total']) {
					$download_status = true;
					break;
				}
			}

			// Load the language for any mails that might be required to be sent out
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_directory']);
			$language->load('mail/order');

			$order_status_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_status_id = '" . (int)$order_info['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

			if ($order_status_query->num_rows) {
				$order_status = $order_status_query->row['name'];
			} else {
				$order_status = '';
			}

			$subject = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

			// HTML Mail
			$data['title'] = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_id);

			$data['text_greeting']        = $language->get('text_new_greeting');
			$data['text_download']        = $language->get('text_new_download');
			$data['text_order_detail']    = $language->get('text_new_order_detail');
			$data['text_instruction']     = $language->get('text_new_instruction');
			$data['text_order_id']        = $language->get('text_new_order_id');
			$data['text_date_added']      = $language->get('text_new_date_added');
			$data['text_payment_method']  = $language->get('text_new_payment_method');
			$data['text_email']           = $language->get('text_new_email');
			$data['text_telephone']       = $language->get('text_new_telephone');
			$data['text_ip']              = $language->get('text_new_ip');
			$data['text_order_status']    = $language->get('text_new_order_status');
			$data['text_product']         = $language->get('text_new_product');
			$data['text_price']           = $language->get('text_new_price');
			$data['text_total']           = $language->get('text_new_total');
			$data['text_account']         = $language->get('text_new_account');
			$data['text_footer']          = sprintf($language->get('text_new_footer'), date('Y'), $order_info['store_name']);

			$data['logo']        = $this->config->get('config_url') . 'image/' . $this->config->get('config_email_logo');
			$data['store_name']  = $order_info['store_name'];
			$data['store_url']   = $order_info['store_url'];
			$data['customer_id'] = $order_info['customer_id'];

			if ($download_status) {
				$data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
			} else {
				$data['download'] = '';
			}

			$data['order_id']       = $order_id;
			$data['date_added']     = date($language->get('date_format_short'), strtotime($order_info['date_added']));
			$data['payment_method'] = $order_info['payment_method'];
			$data['email']          = $order_info['email'];
			$data['telephone']      = $order_info['telephone'];
			$data['ip']             = $order_info['ip'];
			$data['order_status']   = $order_status;

			if ($comment && $notify) {
				$data['comment'] = nl2br($comment);
			} else {
				$data['comment'] = '';
			}

			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			foreach ($order_product_query->rows as $product) {
				$data['products'][] = array(
					'name'             => $product['name'],
					'account_type'     => $product['account_type'],
					'account_username' => $product['account_username'],
					'price'            => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'            => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			// Vouchers
			$data['vouchers'] = array();

			$order_voucher_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_voucher_query->rows as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			// Order Totals
			$data['totals'] = array();

			$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

			foreach ($order_total_query->rows as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}
		}

		return $data;
	}
}