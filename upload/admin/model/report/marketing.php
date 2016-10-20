<?php
class ModelReportMarketing extends Model {
	public function getMarketing($data = array()) {
		$sql = "SELECT m.marketing_id, m.name AS campaign, m.code, m.clicks AS clicks, (SELECT COUNT(DISTINCT order_id) FROM `" . DB_PREFIX . "order` o1 WHERE o1.marketing_id = m.marketing_id";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o1.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o1.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o1.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o1.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= ") AS `orders`, (SELECT SUM(total) FROM `" . DB_PREFIX . "order` o2 WHERE o2.marketing_id = m.marketing_id";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o2.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o2.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o2.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o2.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " GROUP BY o2.marketing_id) AS `total` FROM `" . DB_PREFIX . "marketing` m ORDER BY m.date_added ASC";

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

	public function getTotalMarketing($data = array()) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "marketing`");

		return $query->row['total'];
	}
}