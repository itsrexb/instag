<?php
class ModelSaleRecurringOrder extends Model {
	public function getRecurringOrder($recurring_order_id) {
		$query = $this->db->query("SELECT ro.*, CONCAT(c.firstname, ' ', c.lastname) AS customer, IFNULL(a.username, ro.account_username) AS account_username FROM `" . DB_PREFIX . "recurring_order` ro LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = ro.customer_id) LEFT JOIN `" . DB_PREFIX . "account` a ON (a.customer_id = ro.customer_id AND a.account_id = ro.account_id) WHERE recurring_order_id = '" . (int)$recurring_order_id . "'");

		return $query->row;
	}

	public function cancelRecurringOrder($recurring_order_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET active = '0', date_canceled = NOW() WHERE recurring_order_id = '" . (int)$recurring_order_id . "'");
	}

	public function getRecurringOrders($data) {
		$sql = "SELECT ro.*,c.email, CONCAT(c.firstname, ' ', c.lastname) AS customer, IFNULL(a.username, ro.account_username) AS account_username FROM `" . DB_PREFIX . "recurring_order` ro LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = ro.customer_id) LEFT JOIN `" . DB_PREFIX . "account` a ON (a.customer_id = ro.customer_id AND a.account_id = ro.account_id)";

		$where_data = array();

		if (!empty($data['filter_recurring_order_id'])) {
			$where_data[] = "ro.recurring_order_id = '" . (int)$data['filter_recurring_order_id'] . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$where_data[] = "ro.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_account_id'])) {
			$where_data[] = "ro.account_id = '" . $this->db->escape($data['filter_account_id']) . "'";
		}

		if (!empty($data['filter_account_type'])) {
			$where_data[] = "ro.account_type = '" . $this->db->escape($data['filter_account_type']) . "'";
		}

		if (!empty($data['filter_account_username'])) {
			$where_data[] = "((a.username IS NOT NULL AND a.username LIKE '%" . $this->db->escape($data['filter_account_username']) . "%') OR ro.account_username LIKE '%" . $this->db->escape($data['filter_account_username']) . "%')";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email = '" . $this->db->escape($data['filter_email']) . "'";
		}

		if (isset($data['filter_active'])) {
			$where_data[] = "ro.active = '" . (int)$data['filter_active'] . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "ro.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "ro.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$sort_data = array(
			'ro.recurring_order_id',
			'ro.account_type',
			'account_username',
			'a.email',
			'ro.active',
			'ro.date_added',
			'customer'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ro.recurring_order_id";
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

	public function getTotalRecurringOrders($data) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "recurring_order` ro LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = ro.customer_id) LEFT JOIN `" . DB_PREFIX . "account` a ON (a.customer_id = ro.customer_id AND a.account_id = ro.account_id)";

		$where_data = array();

		if (!empty($data['filter_recurring_order_id'])) {
			$where_data[] = "ro.recurring_order_id = '" . (int)$data['filter_recurring_order_id'] . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$where_data[] = "ro.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_account_id'])) {
			$where_data[] = "ro.account_id = '" . $this->db->escape($data['filter_account_id']) . "'";
		}

		if (!empty($data['filter_account_id'])) {
			$where_data[] = "c.email = '" . $this->db->escape($data['filter_email']) . "'";
		}

		if (!empty($data['filter_account_type'])) {
			$where_data[] = "ro.account_type = '" . $this->db->escape($data['filter_account_type']) . "'";
		}

		if (!empty($data['filter_account_username'])) {
			$where_data[] = "((a.username IS NOT NULL AND a.username LIKE '%" . $this->db->escape($data['filter_account_username']) . "%') OR ro.account_username LIKE '%" . $this->db->escape($data['filter_account_username']) . "%')";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email = '" . $this->db->escape($data['filter_email']) . "'";
		}

		if (isset($data['filter_active'])) {
			$where_data[] = "ro.active = '" . (int)$data['filter_active'] . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "ro.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "ro.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
