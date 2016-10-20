<?php
class ModelCustomerCustomer extends Model {
	public function addCustomer($data) {
		if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $data['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$this->load->model('customer/customer_group');

		$customer_group_info = $this->model_customer_customer_group->getCustomerGroup($customer_group_id);

		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$customer_group_id . "', firstname = '" . $this->db->escape(trim($data['firstname'])) . "', lastname = '" . $this->db->escape(trim($data['lastname'])) . "', email = '" . $this->db->escape(trim($data['email'])) . "', telephone = '" . $this->db->escape(trim($data['telephone'])) . "', timezone = '" . $this->db->escape($data['timezone']) . "', currency_code = '" . $this->db->escape($this->currency->getCode()) . "',  language_code = '" . $this->db->escape($data['language_code']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', ext_aff_id = '" . $this->db->escape($data['ext_aff_id']) . "', status = '1', approved = '" . (int)!$customer_group_info['approval'] . "', country_id = '" . (int)$data['country_id'] . "', date_added = NOW()");

		$customer_id = $this->db->getLastId();

		if ($data['affiliate_id']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET date_affiliate = NOW() WHERE customer_id = '" . (int)$customer_id . "'");
		}

		return $customer_id;
	}

	public function editCustomer($data) {
		$customer_id = $this->customer->getId();

		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET firstname = '" . $this->db->escape(trim($data['firstname'])) . "', lastname = '" . $this->db->escape(trim($data['lastname'])) . "', email = '" . $this->db->escape(trim($data['email'])) . "', telephone = '" . $this->db->escape(trim($data['telephone'])) . "', timezone = '" . $this->db->escape($data['timezone']) . "', currency_code = '" . $this->db->escape($data['currency_code']) . "',  language_code = '" . $this->db->escape($data['language_code']) . "', country_id = '" . (int)$data['country_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editPassword($email, $password) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function editNewsletter($newsletter) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editAffiliate($affiliate_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET affiliate_id = '" . (int)$affiliate_id . "', date_affiliate = NOW() WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editCountryId($country_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET country_id = '" . (int)$country_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomerByToken($token) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET token = ''");

		return $query->row;
	}

	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getIps($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}

	public function addLoginAttempt($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_login` SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
		} else {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer_login` SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");
		}
	}

	public function getLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function getAffiliateActiveCustomers($data = array()) {
		$sql = "SELECT c.*, CONCAT(c.firstname, ' ', c.lastname) AS name, IF(c.affiliate_id = '" . (int)$this->affiliate->getId() . "', 1, 0) AS direct FROM `affiliate_path` ap LEFT JOIN `customer` c ON (c.affiliate_id = ap.affiliate_id) RIGHT JOIN `recurring_order` ro ON (ro.customer_id = c.customer_id) WHERE ap.path_id = '" . (int)$this->affiliate->getId() . "' AND c.status = '1' AND c.approved = '1' AND ro.active = '1'";

		$where_data = array();

		if (!empty($data['filter_name'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_date_affiliate'])) {
			$where_data[] = "DATE(c.date_affiliate) = DATE('" . $this->db->escape($data['filter_date_affiliate']) . "')";
		}

		if (!empty($data['filter_direct'])) {
			$where_data[] = "c.affiliate_id = '" . (int)$this->affiliate->getId() . "'";
		}

		if ($where_data) {
			$sql .= " AND " . implode(" AND ", $where_data);
		}

		$sql .= " GROUP BY c.customer_id";

		$sort_data = array(
			'name',
			'email',
			'date_affiliate'
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

	public function getTotalAffiliateActiveCustomers($data = array()) {
		$sql = "SELECT COUNT(DISTINCT c.customer_id) AS total FROM `affiliate_path` ap LEFT JOIN `customer` c ON (c.affiliate_id = ap.affiliate_id) RIGHT JOIN `recurring_order` ro ON (ro.customer_id = c.customer_id) WHERE ap.path_id = '" . (int)$this->affiliate->getId() . "' AND c.status = '1' AND c.approved = '1' AND ro.active = '1'";

		$where_data = array();

		if (!empty($data['filter_name'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_date_affiliate'])) {
			$where_data[] = "DATE(c.date_affiliate) = DATE('" . $this->db->escape($data['filter_date_affiliate']) . "')";
		}

		if (!empty($data['filter_direct'])) {
			$where_data[] = "c.affiliate_id = '" . (int)$this->affiliate->getId() . "'";
		}

		if ($where_data) {
			$sql .= " AND " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}