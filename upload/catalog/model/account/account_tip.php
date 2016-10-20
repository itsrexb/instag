<?php
class ModelAccountAccountTip extends Model {
	public function getAccountTips() {
		$query = $this->db->query("SELECT a.* FROM ( SELECT atd.* FROM " . DB_PREFIX . "account_tip at LEFT JOIN " . DB_PREFIX . "account_tip_description atd ON (at.account_tip_id = atd.account_tip_id) WHERE atd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND at.status = '1' ORDER BY at.sort_order, LCASE(atd.title) ASC) a ORDER BY RAND() LIMIT 1");

		return $query->rows;
	}
}
