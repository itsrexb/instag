<?php
class ModelCustomerCustomer extends Model {
	public function addCustomer($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', timezone = '" . $this->db->escape($this->config->get('config_timezone')) . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', newsletter = '" . (int)$data['newsletter'] . "', country_id = '" . (int)$data['country_id'] . "', discount = '" . (float)$data['discount'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', managed_billing = '" . (int)$data['managed_billing'] . "',currency_code = '" . $this->db->escape($data['currency_code']) . "',  language_code = '" . $this->db->escape($data['language_code']) . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', affiliate_commission_count = '" . (int)$data['affiliate_commission_count'] . "', ext_aff_id = '" . $this->db->escape($data['ext_aff_id']) . "', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");

		$customer_id = $this->db->getLastId();

		if ($data['affiliate_id']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET date_affiliate = NOW() WHERE customer_id = '" . (int)$customer_id . "'");
		}

		return $customer_id;
	}

	public function editCustomer($customer_id, $data) {
		if (!isset($data['custom_field'])) {
			$data['custom_field'] = array();
		}

		// update affiliate date if the affiliate changed
		if ($data['affiliate_id']) {
			$customer_query = $this->db->query("SELECT affiliate_id FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");

			if ($data['affiliate_id'] != $customer_query->row['affiliate_id']) {
				$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET date_affiliate = NOW() WHERE customer_id = '" . (int)$customer_id . "'");
			}
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', newsletter = '" . (int)$data['newsletter'] . "', country_id = '" . (int)$data['country_id'] . "', discount = '" . (float)$data['discount'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', managed_billing = '" . (int)$data['managed_billing'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', language_code = '" . $this->db->escape($data['language_code']) . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', affiliate_commission_count = '" . (int)$data['affiliate_commission_count'] . "', ext_aff_id = '" . $this->db->escape($data['ext_aff_id']) . "', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', safe = '" . (int)$data['safe'] . "' WHERE customer_id = '" . (int)$customer_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}
	}

	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteCustomer($customer_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");

		$query = $this->db->query("SELECT account_id FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$customer_id . "'");

		if ($query->rows) {
			$this->instaghive->login($customer_id);

			foreach ($query->rows as $account) {
				$this->instaghive->account->delete($account['account_id'], true);

				$this->db->query("DELETE FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account['account_id']) . "'");
				$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET active = '0', date_canceled = NOW() WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account['account_id']) . "'");
			}

			$this->instaghive->logout();
		}
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT c.*, (SELECT GROUP_CONCAT(CONCAT(a.firstname, ' ', a.lastname) ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM `" . DB_PREFIX . "affiliate_path` ap LEFT JOIN `" . DB_PREFIX . "affiliate` a ON (a.affiliate_id = ap.path_id) WHERE ap.affiliate_id = c.affiliate_id GROUP BY ap.affiliate_id) AS affiliate FROM `" . DB_PREFIX . "customer` c WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "customer` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT c.*, CONCAT(c.firstname, ' ', c.lastname) AS name, co.name AS country, cgd.name AS customer_group, (SELECT GROUP_CONCAT(CONCAT(a.firstname, ' ', a.lastname) ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM `" . DB_PREFIX . "affiliate_path` ap LEFT JOIN `" . DB_PREFIX . "affiliate` a ON (a.affiliate_id = ap.path_id) WHERE ap.affiliate_id = c.affiliate_id GROUP BY ap.affiliate_id) AS affiliate, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') AS total_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '1') AS deleted_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') AS active_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires = '0000-00-00 00:00:00') AS kickoff_accounts, (SELECT COUNT(DISTINCT ro.account_id) FROM `" . DB_PREFIX . "recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') AS active_recurring_accounts, (SELECT COUNT(*) FROM `" . DB_PREFIX . "account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.customer_id IN (SELECT customer_id FROM `" . DB_PREFIX . "recurring_order` WHERE account_id = ca.account_id AND active = '0') AND ca.date_expires != '0000-00-00 00:00:00' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') AS inactive_recurring_accounts, (SELECT SUM(o.total) FROM `" . DB_PREFIX . "order` o WHERE o.customer_id = c.customer_id AND o.order_status_id IN ('" . implode("','", $this->config->get('config_complete_status')) . "')) AS total_revenue, (SELECT pd.name FROM `" . DB_PREFIX . "recurring_order` ro LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = ro.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') WHERE ro.customer_id = c.customer_id AND ro.active = '1' ORDER BY ro.recurring_price DESC LIMIT 1) AS plan FROM `" . DB_PREFIX . "customer` c LEFT JOIN `" . DB_PREFIX . "country` co ON (co.country_id = c.country_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (cgd.customer_group_id = c.customer_group_id AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "')";

		$where_data = array();

		if (!empty($data['filter_name'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_country'])) {
			$where_data[] = "co.name LIKE '" . $this->db->escape($data['filter_country']) . "%'";
		}

		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$where_data[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$where_data[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (!empty($data['filter_affiliate_id'])) {
			$where_data[] = "c.affiliate_id = '" . (int)$data['filter_affiliate_id'] . "'";
		}

		if (!empty($data['filter_ext_aff_id'])) {
			$where_data[] = "c.ext_aff_id = '" . $this->db->escape($data['filter_ext_aff_id']) . "'";
		}

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

		if (!empty($data['filter_ip'])) {
			$where_data[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where_data[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$where_data[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_added'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$where_data[] = "c.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_account_status'])) {
			switch($data['filter_account_status'])
			{
			case 'new':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id) = '0'";
				break;
			case 'kickoff':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires = '0000-00-00 00:00:00') > '0'";
				break;
			case 'free_trial':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0' AND (SELECT COUNT(DISTINCT ro.account_id) FROM `recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.customer_id IN (SELECT customer_id FROM `recurring_order` WHERE account_id = ca.account_id AND active = '0') AND ca.date_expires != '0000-00-00 00:00:00' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') = '0'";
				break;
			case 'active':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0' AND (SELECT COUNT(DISTINCT ro.account_id) FROM `recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') > '0'";
				break;
			case 'inactive':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0' AND (SELECT COUNT(DISTINCT ro.account_id) FROM `recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.customer_id IN (SELECT customer_id FROM `recurring_order` WHERE account_id = ca.account_id AND active = '0') AND ca.date_expires != '0000-00-00 00:00:00' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0'";
				break;
			case 'expired':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND (ca.date_expires = '0000-00-00 00:00:00' OR ca.date_expires >= '" . date('Y-m-d H:i:s') . "')) = '0'";
				break;
			case 'deleted':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '1') > '0'";
				break;
			}
		}

		if ($where_data) {
			if (isset($data['implode']) && $data['implode'] == 'or') {
				$sql .= " WHERE " . implode(" OR ", $where_data);
			} else {
				$sql .= " WHERE " . implode(" AND ", $where_data);
			}
		}

		$sort_data = array(
			'name',
			'c.email',
			'country',
			'customer_group',
			'total_accounts',
			'deleted_accounts',
			'total_revenue',
			'plan',
			'affiliate',
			'c.status',
			'c.approved',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function approve($customer_id) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");
		}
	}

	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM  `" . DB_PREFIX . "customer` c LEFT JOIN `" . DB_PREFIX . "country` co ON (co.country_id = c.country_id)";

		$where_data = array();

		if (!empty($data['filter_name'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_country'])) {
			$where_data[] = "co.name LIKE '" . $this->db->escape($data['filter_country']) . "%'";
		}

		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$where_data[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$where_data[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (!empty($data['filter_affiliate_id'])) {
			$where_data[] = "c.affiliate_id = '" . (int)$data['filter_affiliate_id'] . "'";
		}

		if (!empty($data['filter_ext_aff_id'])) {
			$where_data[] = "c.ext_aff_id = '" . $this->db->escape($data['filter_ext_aff_id']) . "'";
		}

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

		if (!empty($data['filter_ip'])) {
			$where_data[] = "c.customer_id IN (SELECT customer_id FROM `" . DB_PREFIX . "customer_ip` WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where_data[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$where_data[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_added'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$where_data[] = "c.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_account_status'])) {
			switch($data['filter_account_status'])
			{
			case 'new':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id) = '0'";
				break;
			case 'kickoff':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires = '0000-00-00 00:00:00') > '0'";
				break;
			case 'free_trial':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0' AND (SELECT COUNT(DISTINCT ro.account_id) FROM `recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.customer_id IN (SELECT customer_id FROM `recurring_order` WHERE account_id = ca.account_id AND active = '0') AND ca.date_expires != '0000-00-00 00:00:00' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') = '0'";
				break;
			case 'active':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0' AND (SELECT COUNT(DISTINCT ro.account_id) FROM `recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') > '0'";
				break;
			case 'inactive':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0' AND (SELECT COUNT(DISTINCT ro.account_id) FROM `recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND ca.customer_id IN (SELECT customer_id FROM `recurring_order` WHERE account_id = ca.account_id AND active = '0') AND ca.date_expires != '0000-00-00 00:00:00' AND ca.date_expires >= '" . date('Y-m-d H:i:s') . "') > '0'";
				break;
			case 'expired':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') > '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0' AND (ca.date_expires = '0000-00-00 00:00:00' OR ca.date_expires >= '" . date('Y-m-d H:i:s') . "')) = '0'";
				break;
			case 'deleted':
				$where_data[] = "(SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '0') = '0' AND (SELECT COUNT(*) FROM `account` ca WHERE ca.customer_id = c.customer_id AND ca.deleted = '1') > '0'";
				break;
			}
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalCustomersAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE status = '0' OR approved = '0'");

		return $query->row['total'];
	}

	public function getTotalCustomersByCustomerGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function addHistory($customer_id, $comment) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_history` SET customer_id = '" . (int)$customer_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getHistories($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT comment, date_added FROM `" . DB_PREFIX . "customer_history` WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalHistories($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_history` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description = '', $amount = 0, $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			// do not send negative amounts to customers
			if ($amount > 0) {
				$this->load->language('mail/customer');

				$this->load->model('setting/store');

				$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

				if ($store_info) {
					$store_name = $store_info['name'];
				} else {
					$store_name = $this->config->get('config_name');
				}

				$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
				$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($customer_id)));

				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($customer_info['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
				$mail->setText($message);
				$mail->send();
			}
		}
	}

	public function deleteTransaction($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactions($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTransactionTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function addReward($customer_id, $description = '', $points = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_reward` SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();
		}
	}

	public function deleteReward($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_reward` WHERE order_id = '" . (int)$order_id . "' AND points > 0");
	}

	public function getRewards($customer_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalRewards($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomerRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE order_id = '" . (int)$order_id . "' AND points > 0");

		return $query->row['total'];
	}

	public function getIps($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}

	public function getTotalIps($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByIp($ip) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ip` WHERE ip = '" . $this->db->escape($ip) . "'");

		return $query->row['total'];
	}

	public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}

	public function getAccounts($customer_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$customer_id. "' ORDER BY username ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalAccounts($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}
}