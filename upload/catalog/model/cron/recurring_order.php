<?php
class ModelCronRecurringOrder extends Model {
	public function getRecurringOrders($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "recurring_order`";

		$where_data = array();

		if (isset($data['filter_date_next_recurring_start'])) {
			$where_data[] = "date_next_recurring >= '" . $this->db->escape($data['filter_date_next_recurring_start']) . "'";
		}

		if (isset($data['filter_date_next_recurring_end'])) {
			$where_data[] = "date_next_recurring <= '" . $this->db->escape($data['filter_date_next_recurring_end']) . "'";
		}

		if (isset($data['filter_active'])) {
			$where_data[] = "active = '" . (int)$data['filter_active'] . "'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$sql .= " ORDER BY recurring_order_id ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getLastRecurringTransactionDate($recurring_order_id) {
		$query = $this->db->query("SELECT o.date_added FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id) WHERE op.recurring_order_id = '" . (int)$recurring_order_id . "' ORDER BY date_added DESC LIMIT 1");

		return ($query->row ? $query->row['date_added'] : false);
	}

	public function updateRecurringOrder($product, $success, $order_info) {
		if ($success) {
			if ($product['trial_duration']) {
				$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET trial_duration = (trial_duration - 1) WHERE recurring_order_id = '" . (int)$product['recurring_order_id'] . "'");
			}

			if ($product['trial_duration'] > 1) {
				$date_next_recurring = date('Y-m-d', strtotime('+' . $product['trial_cycle'] . ' ' . $product['trial_frequency'], strtotime($product['date_next_recurring'])));
			} else {
				$date_next_recurring = date('Y-m-d', strtotime('+' . $product['recurring_cycle'] . ' ' . $product['recurring_frequency'], strtotime($product['date_next_recurring'])));

				if ($product['recurring_duration'] > 0) {
					$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET recurring_duration = (recurring_duration - 1) WHERE recurring_order_id = '" . (int)$product['recurring_order_id'] . "'");

					if ($product['recurring_duration'] == 1) {
						$this->cancelRecurringOrder($product['recurring_order_id']);
					}
				}
			}

			// reduce the amount of coupon uses remaining, remove coupon if we've used them all up
			if ($product['coupon_remaining'] > 0) {
				$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET coupon_remaining = (coupon_remaining - 1) WHERE recurring_order_id = '" . (int)$product['recurring_order_id'] . "'");

				if ($product['coupon_remaining'] == 1) {
					$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET coupon_id = 0 WHERE recurring_order_id = '" . (int)$product['recurring_order_id'] . "'");
				}
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET date_next_recurring = '" . $this->db->escape($date_next_recurring) . "' WHERE recurring_order_id = '" . (int)$product['recurring_order_id'] . "'");
		} else {
			// get number of failed attempts since last successful attempt (query order table for orders linked to recurring_order_id)
			$account_info = $this->getAccount($product['account_id']);

			$failed_attempts = $this->getRecurringOrderFailedAttempts($product['recurring_order_id']);

			if (!$account_info || $failed_attempts >= (int)$this->config->get('config_recurring_order_max_failed_attempts')) {
				$this->cancelRecurringOrder($product['recurring_order_id']);
			}

			if ($account_info) {
				// Send Failed mail
				// Load the language for any mails that might be required to be sent out
				$this->load->model('localisation/language');
				$language_info = $this->model_localisation_language->getLanguage($order_info['language_id']);

				$language = new Language($language_info['directory']);
				$language->load($language_info['directory']);

				$mail_data = $language->load('mail/recurring_order_fail');

				$mail_data['text_footer'] = sprintf($language->get('text_footer'), date('Y'), $order_info['store_name']);

				$mail_data['href_home']      = $order_info['store_url'];
				$mail_data['href_dashboard'] = $this->url->link('account/dashboard', '', true);

				$mail_data['logo']             = $this->config->get('config_url') . 'image/' . $this->config->get('config_email_logo');
				$mail_data['site_name']        = $order_info['store_name'];
				$mail_data['firstname']        = $order_info['firstname'];
				$mail_data['lastname']         = $order_info['lastname'];
				$mail_data['message']          = $language->get('message_default');
				$mail_data['product_name']     = $product['name'];
				$mail_data['account_type']     = $account_info['type'];
				$mail_data['account_username'] = $account_info['username'];

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
				$mail->setSubject(html_entity_decode($language->get('subject_default'), ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($this->load->view('mail/recurring_order_fail', $mail_data));
				$mail->setText($this->load->view('mail/recurring_order_fail_text', array_map('strip_tags', $mail_data)));
				$mail->send();
			}
		}
	}

	public function getAccount($account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE account_id = '" . $this->db->escape($account_id) . "'");

		return $query->row;
	}

	public function getOrderDetails($recurring_order_id) {
		$order_query = $this->db->query("SELECT o.* FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_product` op ON (op.order_id = o.order_id) WHERE op.recurring_order_id = '" . (int)$recurring_order_id . "' AND o.order_status_id != '0'");

		return $order_query->row;
	}	

	public function cancelRecurringOrder($recurring_order_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET active = '0', date_canceled = NOW() WHERE recurring_order_id = '" . (int)$recurring_order_id . "'");

		// update any enabled marketing extensions
		$this->load->model('extension/extension');
		$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

		foreach ($marketing_extensions as $marketing) {
			if ($this->config->get($marketing['code'] . '_status')) {
				$this->{$marketing['code']} = new $marketing['code']($this->registry);

				$this->load->model('extension/marketing/' . $marketing['code']);
				$this->{'model_extension_marketing_' . $marketing['code']}->updateCustomer($this->customer->getId());
			}
		}
	}

	public function getRecurringOrderFailedAttempts($recurring_order_id) {
		$order_query = $this->db->query("SELECT o.order_id FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_product` op ON (op.order_id = o.order_id) WHERE op.recurring_order_id = '" . (int)$recurring_order_id . "' AND o.order_status_id != '0' ORDER BY o.date_added DESC");

		if ($order_query->row) {
			$order_id_last_payment = $order_query->row['order_id'];
		} else {
			$order_id_last_payment = 0;
		}

		$order_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_product` op ON (op.order_id = o.order_id) WHERE op.recurring_order_id = '" . (int)$recurring_order_id . "' AND o.order_id > '" . (int)$order_id_last_payment . "'");

		return $order_query->row['total'];
	}

	public function useStoreConfig($store_id) {
		$store_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store` WHERE store_id = '" . (int)$store_id . "'");

		if ($store_query->num_rows) {
			$this->config->set('config_store_id', (int)$store_id);

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "'");

			foreach ($query->rows as $setting) {
				if (!$setting['serialized']) {
					$this->config->set($setting['key'], $setting['value']);
				} else {
					$this->config->set($setting['key'], unserialize($setting['value']));
				}
			}

			return true;
		}

		return false;
	}
}