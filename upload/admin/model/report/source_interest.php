<?php
class ModelReportSourceInterest extends Model {

	public function getSourceInterests($data = array()) {
		$sql = "SELECT cp.source_interest_id AS source_interest_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, (SELECT count(*) FROM `" . DB_PREFIX . "source_interest_history` WHERE source_interest_id = cp.source_interest_id) as history FROM `" . DB_PREFIX . "source_interest_path` cp LEFT JOIN `" . DB_PREFIX . "source_interest` c1 ON (cp.source_interest_id = c1.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest` c2 ON (cp.path_id = c2.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest_description` cd1 ON (cp.path_id = cd1.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest_description` cd2 ON (cp.source_interest_id = cd2.source_interest_id) RIGHT JOIN `source_interest_history` sih ON sih.source_interest_id = cp.source_interest_id WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND sih.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND sih.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " GROUP BY cp.source_interest_id";

		$sort_data = array(
			'name'
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

	public function getTotalSourceInterests($data = array()) {
		$sql = "SELECT count(*) as total FROM `" . DB_PREFIX . "source_interest_path` cp LEFT JOIN `" . DB_PREFIX . "source_interest` c1 ON (cp.source_interest_id = c1.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest` c2 ON (cp.path_id = c2.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest_description` cd1 ON (cp.path_id = cd1.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest_description` cd2 ON (cp.source_interest_id = cd2.source_interest_id) RIGHT JOIN `source_interest_history` sih ON sih.source_interest_id = cp.source_interest_id WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND sih.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND sih.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " GROUP BY cp.source_interest_id";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}


}