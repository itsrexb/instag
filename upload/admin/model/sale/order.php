<?php
class ModelSaleOrder extends Model {
	public function editOrderTotal($order_id, $total) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total . "' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
		
		if ($order_query->num_rows) {
			$reward = 0;

			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_product_query->rows as $product) {
				$reward += $product['reward'];
			}

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
				'customer'           => $order_query->row['customer'],
				'customer_group_id'  => $order_query->row['customer_group_id'],
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
				'reward'             => $reward,
				'order_status_id'    => $order_query->row['order_status_id'],
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
				'date_added'         => $order_query->row['date_added'],
				'date_modified'      => $order_query->row['date_modified']
			);
		} else {
			return;
		}
	}

	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id,o.customer_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.recurring_order, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";

		$join_data  = array();
		$where_data = array();

		if (isset($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$where_data[] = "(" . implode(" OR ", $implode) . ")";
			}
		} else {
			$where_data[] = "o.order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$where_data[] = "o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_recurring_order_id'])) {
			$join_data[] = "LEFT JOIN `" . DB_PREFIX . "order_product` op ON (op.order_id = o.order_id)";

			$where_data[] = "op.recurring_order_id = '" . (int)$data['filter_recurring_order_id'] . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$where_data[] = "o.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$where_data[] = "CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (isset($data['filter_has_affiliate']) || isset($data['filter_has_ext_aff_id'])) {
			$join_data[] = "LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = o.customer_id";

			if (isset($data['filter_has_affiliate'])) {
				if ($data['filter_has_affiliate']) {
					$where_data[] = "c.affiliate_id != 0";
				} else {
					$where_data[] = "c.affiliate_id = 0";
				}
			}

			if (isset($data['filter_has_ext_aff_id'])) {
				if ($data['filter_has_ext_aff_id']) {
					$where_data[] = "c.ext_aff_id != ''";
				} else {
					$where_data[] = "c.ext_aff_id = ''";
				}
			}
		}

		if (!empty($data['filter_date_added'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_added'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$where_data[] = "o.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_modified'])) {
			$filter_date_modified_start = new \DateTime($data['filter_date_modified'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_modified_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_modified_end = new \DateTime($data['filter_date_modified'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_modified_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_modified >= '" . $this->db->escape($filter_date_modified_start->format('Y-m-d H:i:s')) . " 00:00:00'";
			$where_data[] = "o.date_modified <= '" . $this->db->escape($filter_date_modified_end->format('Y-m-d H:i:s')) . " 23:59:59'";
		}

		if (!empty($data['filter_total'])) {
			$where_data[] = "o.total = '" . (float)$data['filter_total'] . "'";
		}

		if (isset($data['filter_recurring_order']) && !is_null($data['filter_recurring_order'])) {
			$where_data[] = "o.recurring_order = '" . (int)$data['filter_recurring_order'] . "'";
		}

		if ($join_data) {
			$sql .= " " . implode(" ", $join_data);
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$sql .= " GROUP BY o.order_id";

		$sort_data = array(
			'o.order_id',
			'customer',
			'status',
			'o.recurring_order',
			'o.total',
			'o.date_added',
			'o.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}

			if (!isset($data['limit']) || $data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "' GROUP BY order_product_id");

		return $query->rows;
	}

	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderVoucherByVoucherId($voucher_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int)$voucher_id . "'");

		return $query->row;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(DISTINCT o.order_id) AS total FROM `" . DB_PREFIX . "order` o";

		$join_data  = array();
		$where_data = array();

		if (isset($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$where_data[] = "(" . implode(" OR ", $implode) . ")";
			}
		} else {
			$where_data[] = "o.order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$where_data[] = "o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_recurring_order_id'])) {
			$join_data[] = "LEFT JOIN `" . DB_PREFIX . "order_product` op ON (op.order_id = o.order_id)";

			$where_data[] = "op.recurring_order_id = '" . (int)$data['filter_recurring_order_id'] . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$where_data[] = "o.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$where_data[] = "CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (isset($data['filter_has_affiliate']) || isset($data['filter_has_ext_aff_id'])) {
			$join_data[] = "LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = o.customer_id";

			if (isset($data['filter_has_affiliate'])) {
				if ($data['filter_has_affiliate']) {
					$where_data[] = "c.affiliate_id != 0";
				} else {
					$where_data[] = "c.affiliate_id = 0";
				}
			}

			if (isset($data['filter_has_ext_aff_id'])) {
				if ($data['filter_has_ext_aff_id']) {
					$where_data[] = "c.ext_aff_id != ''";
				} else {
					$where_data[] = "c.ext_aff_id = ''";
				}
			}
		}

		if (!empty($data['filter_date_added'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_added'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$where_data[] = "o.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_modified'])) {
			$filter_date_modified_start = new \DateTime($data['filter_date_modified'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_modified_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_modified_end = new \DateTime($data['filter_date_modified'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_modified_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_modified >= '" . $this->db->escape($filter_date_modified_start->format('Y-m-d H:i:s')) . " 00:00:00'";
			$where_data[] = "o.date_modified <= '" . $this->db->escape($filter_date_modified_end->format('Y-m-d H:i:s')) . " 23:59:59'";
		}

		if (!empty($data['filter_total'])) {
			$where_data[] = "o.total = '" . (float)$data['filter_total'] . "'";
		}

		if (isset($data['filter_recurring_order']) && !is_null($data['filter_recurring_order'])) {
			$where_data[] = "o.recurring_order = '" . (int)$data['filter_recurring_order'] . "'";
		}

		if ($join_data) {
			$sql .= " " . implode(" ", $join_data);
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrdersByStoreId($store_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrdersByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByProcessingStatus() {
		$implode = array();

		$order_statuses = $this->config->get('config_processing_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode));

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByCompleteStatus() {
		$implode = array();

		$order_statuses = $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . "");

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByLanguageId($language_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByCurrencyId($currency_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function createInvoiceNo($order_id) {
		$order_info = $this->getOrder($order_id);

		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}

	public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM `" . DB_PREFIX . "order_history` oh LEFT JOIN `" . DB_PREFIX . "order_status` os ON (os.order_status_id = oh.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') WHERE oh.order_id = '" . (int)$order_id . "' ORDER BY oh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		$store_timezone = new DateTimeZone($this->config->get('config_timezone'));

		$order_history_data = array();

		foreach ($query->rows as $order_history) {
			$date_added = new DateTime($order_history['date_added']);
			$date_added->setTimezone($store_timezone);

			$order_history['date_added'] = $date_added->format('Y-m-d H:i:s');

			$order_history_data[] = $order_history;
		}

		return $order_history_data;
	}

	public function getTotalOrderHistories($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");

		return $query->row['total'];
	}

	public function getEmailsByProductsOrdered($products, $start, $end) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);

		return $query->rows;
	}

	public function getTotalEmailsByProductsOrdered($products) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");

		return $query->row['total'];
	}
}