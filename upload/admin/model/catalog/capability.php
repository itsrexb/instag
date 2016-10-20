<?php
class ModelCatalogCapability extends Model {
	public function addCapability($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "capability` SET name = '" . $this->db->escape($data['name']) . "', free_trial= '" . (int)(isset($data['free_trial']) ? $data['free_trial'] : 0) . "'");

		return $this->db->getLastId();
	}

	public function editCapability($capability_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "capability` SET name = '" . $this->db->escape($data['name']) . "' , free_trial = '" . (int)(isset($data['free_trial']) ? $data['free_trial'] : 0) . "' WHERE capability_id = '" . (int)$capability_id . "'");
	}

	public function deleteCapability($capability_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "capability` WHERE capability_id = '" . (int)$capability_id . "'");
	}

	public function getCapability($capability_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "capability` WHERE capability_id = '" . (int)$capability_id . "'");

		return $query->row;
	}

	public function getCapabilities($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "capability`";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

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

	public function getTotalCapabilities() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "capability`");

		return $query->row['total'];
	}
}