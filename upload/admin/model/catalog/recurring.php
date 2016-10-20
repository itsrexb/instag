<?php
class ModelCatalogRecurring extends Model {
	public function addRecurring($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring` SET price_type = '" . $this->db->escape($data['price_type']) . "', price = '" . (float)$data['price'] . "', cycle = '" . (int)$data['cycle'] . "', frequency = '" . $this->db->escape($data['frequency']) . "', duration = '" . (int)$data['duration'] . "', trial_price_type = '" . $this->db->escape($data['trial_price_type']) . "', trial_price = '" . (float)$data['trial_price'] . "', trial_cycle = '" . (int)$data['trial_cycle'] . "', trial_frequency = '" . $this->db->escape($data['trial_frequency']) . "', trial_duration = '" . (int)$data['trial_duration'] . "', trial_status = '" . (int)$data['trial_status'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$recurring_id = $this->db->getLastId();

		foreach ($data['recurring_description'] as $language_id => $recurring_description) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring_description` SET recurring_id = '" . (int)$recurring_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($recurring_description['name']) . "'");
		}

		return $recurring_id;
	}

	public function editRecurring($recurring_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "recurring` SET price_type = '" . $this->db->escape($data['price_type']) . "', price = '" . (float)$data['price'] . "', cycle = '" . (int)$data['cycle'] . "', frequency = '" . $this->db->escape($data['frequency']) . "', duration = '" . (int)$data['duration'] . "', trial_price_type = '" . $this->db->escape($data['trial_price_type']) . "', trial_price = '" . (float)$data['trial_price'] . "', trial_cycle = '" . (int)$data['trial_cycle'] . "', trial_frequency = '" . $this->db->escape($data['trial_frequency']) . "', trial_duration = '" . (int)$data['trial_duration'] . "', trial_status = '" . (int)$data['trial_status'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE recurring_id = '" . (int)$recurring_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "recurring_description` WHERE recurring_id = '" . (int)$recurring_id . "'");

		foreach ($data['recurring_description'] as $language_id => $recurring_description) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring_description` SET recurring_id = '" . (int)$recurring_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($recurring_description['name']) . "'");
		}
	}

	public function copyRecurring($recurring_id) {
		$data = $this->getRecurring($recurring_id);

		$data['recurring_description'] = $this->getRecurringDescription($recurring_id);

		foreach ($data['recurring_description'] as &$recurring_description) {
			$recurring_description['name'] .= ' - 2';
		}

		$this->addRecurring($data);
	}

	public function deleteRecurring($recurring_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "recurring` WHERE recurring_id = '" . (int)$recurring_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "recurring_description` WHERE recurring_id = '" . (int)$recurring_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE recurring_id = '" . (int)$recurring_id . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET recurring_id = 0 WHERE `recurring_id` = '" . (int)$recurring_id . "'");
	}

	public function getRecurring($recurring_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row;
	}

	public function getRecurringDescription($recurring_id) {
		$recurring_description_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_description` WHERE recurring_id = '" . (int)$recurring_id . "'");

		foreach ($query->rows as $result) {
			$recurring_description_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $recurring_description_data;
	}

	public function getRecurrings($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "recurring` r LEFT JOIN `" . DB_PREFIX . "recurring_description` rd ON (rd.recurring_id = r.recurring_id)";

		$where_data = array();

		$where_data[] = "rd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$where_data[] = "rd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$sort_data = array(
			'rd.name',
			'r.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY rd.name";
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

	public function getTotalRecurrings() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "recurring`");

		return $query->row['total'];
	}
}