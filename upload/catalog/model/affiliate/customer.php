<?php
class ModelAffiliateCustomer extends Model {
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT c.*, CONCAT(c.firstname, ' ', c.lastname) AS name, ro.active, ap.level, (SELECT SUM(amount) FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "' AND customer_id = c.customer_id) AS total_commission FROM `" . DB_PREFIX . "customer` c LEFT JOIN `" . DB_PREFIX . "affiliate_path` ap ON (ap.affiliate_id = c.affiliate_id) LEFT JOIN `" . DB_PREFIX . "recurring_order` ro ON (ro.customer_id = c.customer_id AND ro.active = '1') WHERE c.customer_id = '" . (int)$customer_id . "' AND ap.path_id = '" . (int)$this->affiliate->getId() . "' AND c.status = '1' AND c.approved = '1' GROUP BY c.customer_id");

		return $query->row;
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT c.*, CONCAT(c.firstname, ' ', c.lastname) AS name, ro.active, ap.level, (SELECT SUM(amount) FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "' AND customer_id = c.customer_id) AS total_commission FROM `" . DB_PREFIX . "affiliate_path` ap LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.affiliate_id = ap.affiliate_id) LEFT JOIN `" . DB_PREFIX . "recurring_order` ro ON (ro.customer_id = c.customer_id AND ro.active = '1') WHERE ap.path_id = '" . (int)$this->affiliate->getId() . "' AND c.status = '1' AND c.approved = '1'";

		$where_data = array();

		if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_affiliate >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$where_data[] = "c.date_affiliate <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (isset($data['filter_active']) && !is_null($data['filter_active'])) {
			if ($data['filter_active']) {
				$where_data[] = "ro.active = '" . (int)$data['filter_active'] . "'";
			} else {
				$where_data[] = "ro.active IS NULL";
			}
		}

		if ($where_data) {
			$sql .= " AND " . implode(" AND ", $where_data);
		}

		$sql .= " GROUP BY c.customer_id";

		$sort_data = array(
			'name',
			'c.email',
			'c.date_affiliate'
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

	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(DISTINCT c.customer_id) AS total FROM `" . DB_PREFIX . "affiliate_path` ap LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.affiliate_id = ap.affiliate_id) LEFT JOIN `" . DB_PREFIX . "recurring_order` ro ON (ro.customer_id = c.customer_id AND ro.active = '1') WHERE ap.path_id = '" . (int)$this->affiliate->getId() . "' AND c.status = '1' AND c.approved = '1'";

		$where_data = array();

		if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_affiliate >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$where_data[] = "c.date_affiliate <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if (isset($data['filter_active']) && !is_null($data['filter_active'])) {
			$where_data[] = "ro.active = '" . (int)$data['filter_active'] . "'";
		}

		if ($where_data) {
			$sql .= " AND " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}