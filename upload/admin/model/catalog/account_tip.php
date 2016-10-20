<?php
class ModelCatalogAccountTip extends Model {
	public function addAccountTip($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "account_tip` SET status = '" . (int)$data['status'] . "'");

		$account_tip_id = $this->db->getLastId();

		foreach ($data['account_tip_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "account_tip_description` SET account_tip_id = '" . (int)$account_tip_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->cache->delete('account_tip');

		return $account_tip_id;
	}

	public function editAccountTip($account_tip_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "account_tip` SET status = '" . (int)$data['status'] . "' WHERE account_tip_id = '" . (int)$account_tip_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "account_tip_description` WHERE account_tip_id = '" . (int)$account_tip_id . "'");

		foreach ($data['account_tip_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "account_tip_description` SET account_tip_id = '" . (int)$account_tip_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->cache->delete('account_tip');
	}

	public function deleteAccountTip($account_tip_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "account_tip` WHERE account_tip_id = '" . (int)$account_tip_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "account_tip_description` WHERE account_tip_id = '" . (int)$account_tip_id . "'");

		$this->cache->delete('account_tip');
	}

	public function getAccountTip($account_tip_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_tip` WHERE account_tip_id = '" . (int)$account_tip_id . "'");

		return $query->row;
	}

	public function getAccountTips($data = array()) {
		if ($data) {
			$account_tip_data = $this->cache->get('account_tip.' . (int)$this->config->get('config_language_id') . '.' . md5(json_encode($data)));
		} else {
			$account_tip_data = $this->cache->get('account_tip.' . (int)$this->config->get('config_language_id'));
		}

		if (!$account_tip_data) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "account_tip` at LEFT JOIN `" . DB_PREFIX . "account_tip_description` atd ON (atd.account_tip_id = at.account_tip_id) WHERE atd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'atd.title',
				'at.status'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY atd.title";
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

			$account_tip_data = $query->rows;

			if ($data) {
				$this->cache->set('account_tip.' . (int)$this->config->get('config_language_id') . '.' . md5(json_encode($data)), $account_tip_data);
			} else {
				$this->cache->set('account_tip.' . (int)$this->config->get('config_language_id'), $account_tip_data);
			}
		}

		return $account_tip_data;
	}

	public function getAccountTipDescriptions($account_tip_id) {
		$account_tip_description_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_tip_description` WHERE account_tip_id = '" . (int)$account_tip_id . "'");

		foreach ($query->rows as $result) {
			$account_tip_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description']
			);
		}

		return $account_tip_description_data;
	}

	public function getTotalAccountTips() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "account_tip`");

		return $query->row['total'];
	}
}