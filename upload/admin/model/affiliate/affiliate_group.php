<?php
class ModelAffiliateAffiliateGroup extends Model {
	public function addAffiliateGroup($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_group` VALUES ()");

		$affiliate_group_id = $this->db->getLastId();

		foreach ($data['affiliate_group_descriptions'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_group_description` SET affiliate_group_id = '" . (int)$affiliate_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		foreach ($data['affiliate_group_commissions'] as $commission_affiliate_group_id => $levels) {
			foreach ($levels as $level => $commission) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_group_commission` SET affiliate_group_id = '" . (int)$affiliate_group_id . "', commission_affiliate_group_id = '" . (int)$commission_affiliate_group_id . "', level = '" . (int)$level . "', commission = '" . (float)$commission . "'");
			}
		}
	}

	public function editAffiliateGroup($affiliate_group_id, $data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_group_description` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");

		foreach ($data['affiliate_group_descriptions'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_group_description` SET affiliate_group_id = '" . (int)$affiliate_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_group_commission` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");

		foreach ($data['affiliate_group_commissions'] as $commission_affiliate_group_id => $levels) {
			foreach ($levels as $level => $commission) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_group_commission` SET affiliate_group_id = '" . (int)$affiliate_group_id . "', commission_affiliate_group_id = '" . (int)$commission_affiliate_group_id . "', level = '" . (int)$level . "', commission = '" . (float)$commission . "'");
			}
		}
	}

	public function deleteAffiliateGroup($affiliate_group_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_group` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_group_commission` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_group_description` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");
	}

	public function getAffiliateGroup($affiliate_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "affiliate_group_description` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getAffiliateGroups($data = array()) {
		$sql = "SELECT agd.*, agc.commission AS commission FROM `" . DB_PREFIX . "affiliate_group_description` agd LEFT JOIN `" . DB_PREFIX . "affiliate_group_commission` agc ON (agc.affiliate_group_id = agd.affiliate_group_id AND agc.commission_affiliate_group_id = '0' AND agc.level = '0') WHERE agd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'agd.name',
			'commission'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY agd.name";
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

	public function getAffiliateGroupDescriptions($affiliate_group_id) {
		$affiliate_group_description_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_group_description` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");

		foreach ($query->rows as $result) {
			$affiliate_group_description_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}

		return $affiliate_group_description_data;
	}

	public function getAffiliateGroupCommissions($affiliate_group_id) {
		$affiliate_group_commission_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_group_commission` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");

		foreach ($query->rows as $result) {
			if (isset($affiliate_group_commission_data[$result['commission_affiliate_group_id']])) {
				$affiliate_group_commission_data[$result['commission_affiliate_group_id']][$result['level']] = $result['commission'];
			} else {
				$affiliate_group_commission_data[$result['commission_affiliate_group_id']] = array($result['level'] => $result['commission']);
			}
		}

		return $affiliate_group_commission_data;
	}

	public function getTotalAffiliateGroups() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate_group`");

		return $query->row['total'];
	}
}