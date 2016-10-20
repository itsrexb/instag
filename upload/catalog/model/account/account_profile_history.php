<?php
class ModelAccountAccountProfileHistory extends Model {
	public function addAccountProfileHistory($account_id, $date_added, $meta_data) {
		$this->db->insert(array(
			'table'  => 'account_profile_history',
			'fields' => array(
				'account_id' => $account_id,
				'date_added' => $date_added,
				'meta_data'  => json_encode($meta_data)
			)
		));
	}

	public function getAccountProfileHistory($account_id, $date_added) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_profile_history` WHERE account_id = '" . $this->db->escape($account_id) . "' AND date_added = '" . $this->db->escape($date_added) . "'");

		return $query->row;
	}

	public function getOldestAccountProfileHistory($account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_profile_history` WHERE account_id = '" . $this->db->escape($account_id) . "' ORDER BY date_added ASC LIMIT 1");

		if ($query->row) {
			$query->row['meta_data'] = json_decode($query->row['meta_data'], true);
		}

		return $query->row;
	}

	public function getLatestAccountProfileHistory($account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_profile_history` WHERE account_id = '" . $this->db->escape($account_id) . "' ORDER BY date_added DESC LIMIT 1");

		if ($query->row) {
			$query->row['meta_data'] = json_decode($query->row['meta_data'], true);
		}

		return $query->row;
	}
}