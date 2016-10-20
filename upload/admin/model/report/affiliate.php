<?php
class ModelReportAffiliate extends Model {
	public function getCommission($data = array()) {
		$sql = "SELECT at.affiliate_id,
		(SELECT COUNT(*) FROM `" . DB_PREFIX . "customer` c INNER JOIN `" . DB_PREFIX . "recurring_order` ro 
		ON c.customer_id  = ro.customer_id WHERE at.affiliate_id = c.affiliate_id  AND ro.active = '1' ".(!empty($data['filter_date_start']) ? "AND c.date_affiliate >= '".$data['filter_date_start']."'" : "")." ".(!empty($data['filter_date_end']) ? " AND c.date_affiliate <= '".$data['filter_date_end']."'" : "").") AS total_active_customers,
		(SELECT COUNT(*) FROM `" . DB_PREFIX . "customer` c WHERE at.affiliate_id = c.affiliate_id ".(!empty($data['filter_date_start']) ? " AND c.date_affiliate >= '".$data['filter_date_start']."'" : "")." ".(!empty($data['filter_date_end']) ? " AND c.date_affiliate <= '".$data['filter_date_end']."'" : '').") AS total_customers, 
		CONCAT(a.firstname, ' ', a.lastname) AS affiliate, a.email, a.status, SUM(at.amount) AS commission, 0 AS orders, 0 AS total FROM " . DB_PREFIX . "affiliate_transaction at LEFT JOIN `" . DB_PREFIX . "affiliate` a ON (at.affiliate_id = a.affiliate_id)";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "at.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "at.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " GROUP BY at.affiliate_id ORDER BY commission DESC";

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

	public function getTotalCommission($data = array()) {
		$sql = "SELECT COUNT(DISTINCT affiliate_id) AS total FROM `" . DB_PREFIX . "affiliate_transaction`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProducts($data = array()) {
		$sql = "SELECT at.product_id, CONCAT(a.firstname, ' ', a.lastname) AS affiliate, a.email, a.status, SUM(at.amount) AS commission, 0 AS orders, 0 AS total FROM " . DB_PREFIX . "affiliate_transaction at LEFT JOIN `" . DB_PREFIX . "affiliate` a ON (at.affiliate_id = a.affiliate_id) LEFT JOIN " . DB_PREFIX . "product";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "at.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "at.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " GROUP BY at.affiliate_id ORDER BY commission DESC";

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

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT product_id) AS total FROM `" . DB_PREFIX . "affiliate_transaction`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getAffiliateActivities($data = array()) {
		$sql = "SELECT aa.affiliate_activity_id, aa.affiliate_id, aa.key, aa.data, aa.ip, aa.date_added FROM " . DB_PREFIX . "affiliate_activity aa LEFT JOIN " . DB_PREFIX . "affiliate a ON (aa.affiliate_id = a.affiliate_id)";

		$implode = array();

		if (!empty($data['filter_affiliate'])) {
			$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_affiliate']) . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "aa.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "aa.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "aa.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY aa.date_added DESC";

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

	public function getTotalAffiliateActivities($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate_activity` aa LEFT JOIN " . DB_PREFIX . "affiliate a ON (aa.affiliate_id = a.affiliate_id)";

		$implode = array();

		if (!empty($data['filter_affiliate'])) {
			$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_affiliate']) . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "aa.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "aa.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "aa.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
