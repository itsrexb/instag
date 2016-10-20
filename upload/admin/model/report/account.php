<?php
class ModelReportAccount extends Model {
	public function getAccoutDeclines($data = array()) {
		$sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email AS customer_email, c.telephone AS customer_telephone, co.name AS customer_country, a.account_id, a.username, o.order_id, o.recurring_order, o.date_added AS date_last_decline, (SELECT SUM(total) FROM `" . DB_PREFIX . "order` WHERE customer_id = c.customer_id AND order_status_id IN ('" . implode("','", $this->config->get('config_complete_status')) . "')) AS total_spent FROM (SELECT account_id, MAX(order_id) AS order_id FROM `" . DB_PREFIX . "order_product` GROUP BY account_id) op LEFT JOIN `" . DB_PREFIX . "account` a ON (a.account_id = op.account_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id) LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = o.customer_id) LEFT JOIN `country` co ON (co.country_id = c.country_id) WHERE a.deleted = '0' AND o.order_status_id = '0'";

		$where_data = array();

		if (!empty($data['filter_country'])) {
			$where_data[] = "co.name LIKE '" . $this->db->escape($data['filter_country']) . "%'";
		}

		if (isset($data['filter_recurring_order']) && !is_null($data['filter_recurring_order'])) {
			$where_data[] = "o.recurring_order = '" . (int)$data['filter_recurring_order'] . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($where_data) {
			$sql .= " AND " . implode(" AND ", $where_data);
		}

		$sql .= " GROUP BY a.account_id";

		$sort_data = array(
			'customer',
			'a.username',
			'c.email',
			'c.telephone',
			'co.name',
			'o.recurring_order',
			'total_spent',
			'o.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.date_added";
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

	public function getTotalAccoutDeclines($data = array()){
		$sql = "SELECT COUNT(DISTINCT a.account_id) AS total FROM (SELECT account_id, MAX(order_id) AS order_id FROM `" . DB_PREFIX . "order_product` GROUP BY account_id) op LEFT JOIN `" . DB_PREFIX . "account` a ON (a.account_id = op.account_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id) LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = o.customer_id) LEFT JOIN `country` co ON (co.country_id = c.country_id) WHERE a.deleted = '0' AND o.order_status_id = '0'";

		$where_data = array();

		if (!empty($data['filter_country'])) {
			$where_data[] = "co.name LIKE '" . $this->db->escape($data['filter_country']) . "%'";
		}

		if (isset($data['filter_recurring_order']) && !is_null($data['filter_recurring_order'])) {
			$where_data[] = "o.recurring_order = '" . (int)$data['filter_recurring_order'] . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "o.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($where_data) {
			$sql .= " AND " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getAccountFollowers($data = array()) {
		$sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, a.username, 
			(SELECT meta_data FROM `" . DB_PREFIX . "account_profile_history` WHERE account_id = aph.account_id ORDER BY `date_added` ASC LIMIT 1) AS followers_beginning,
			(SELECT meta_data FROM `" . DB_PREFIX . "account_profile_history` WHERE account_id = aph.account_id ORDER BY `date_added` DESC LIMIT 1) AS followers_ending
			FROM `" . DB_PREFIX . "account_profile_history` aph 
			LEFT JOIN `" . DB_PREFIX . "account` a ON a.account_id = aph.account_id
			LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = a.customer_id
			WHERE aph.meta_data != ''";

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_account'])) {
			$sql .= " AND a.username LIKE '%" . $this->db->escape($data['filter_account']) . "%'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND aph.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND aph.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " GROUP BY aph.account_id";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$account_profile_history_query = $this->db->query($sql);

		$account_followers = array();

		if ($account_profile_history_query->rows) {
			foreach ($account_profile_history_query->rows as $key => $account_profile_history) {
				$followers_beginning = json_decode($account_profile_history['followers_beginning']);
				$followers_ending    = json_decode($account_profile_history['followers_ending']);

				$account_followers[] = array(
					'customer'            => $account_profile_history['customer'],
					'username'            => $account_profile_history['username'],
					'followers_beginning' => $followers_beginning->CountsFollowedBy,
					'followers_ending'    => $followers_ending->CountsFollowedBy
				);
			}
		}

		return $account_followers;
	}

	public function getTotalAccountFollowers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "account_profile_history` aph LEFT JOIN `" . DB_PREFIX . "account` a ON a.account_id = aph.account_id LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = a.customer_id WHERE aph.meta_data != ''";

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_account'])) {
			$sql .= " AND a.username LIKE '%" . $this->db->escape($data['filter_account']) . "%'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND aph.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND aph.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " ORDER BY aph.account_id";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}