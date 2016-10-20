<?php
class ModelCatalogSourceInterest extends Model {
	public function getSourceInterests($parent_id = 0, $country_id = 0) {
		$query = $this->db->query("SELECT si.source_interest_id, sid.name, sid.description FROM `" . DB_PREFIX . "source_interest` si LEFT JOIN `" . DB_PREFIX . "source_interest_description` sid ON (sid.source_interest_id = si.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest_exclude` sie ON (sie.source_interest_id = si.source_interest_id AND sie.country_id = '" . (int)$country_id . "') WHERE si.parent_id = '" . (int)$parent_id . "' AND sid.language_id = '" . (int)$this->config->get('config_language_id') . "' AND si.status = '1' AND sie.country_id IS NULL ORDER BY LCASE(sid.name)");

		return $query->rows;
	}

	public function getSourceInterestAccounts($source_interest_id) {
		$query = $this->db->query("SELECT account, quality FROM `" . DB_PREFIX . "source_interest_account` WHERE source_interest_id = '" . (int)$source_interest_id . "' AND country_id = '" . (int)$this->customer->getCountryId() . "'");

		if (!$query->rows) {
			$query = $this->db->query("SELECT account, quality FROM `" . DB_PREFIX . "source_interest_account` WHERE source_interest_id = '" . (int)$source_interest_id . "' AND country_id = '0'");
		}

		return $query->rows;
	}

	public function getSourceInterestTags($source_interest_id) {
		$query = $this->db->query("SELECT tag, quality FROM `" . DB_PREFIX . "source_interest_tag` WHERE source_interest_id = '" . (int)$source_interest_id . "' AND country_id = '" . (int)$this->customer->getCountryId() . "'");

		if (!$query->rows) {
			$query = $this->db->query("SELECT tag, quality FROM `" . DB_PREFIX . "source_interest_tag` WHERE source_interest_id = '" . (int)$source_interest_id . "' AND country_id = '0'");
		}

		return $query->rows;
	}

	public function addHistory($source_interest_id, $customer_id, $account_id) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_history` SET source_interest_id = '" . (int)$source_interest_id . "', customer_id = '" . (int)$customer_id . "', account_id = '" . $this->db->escape($account_id) . "', date_added = NOW()");
	}
}