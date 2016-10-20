<?php
class ModelReportActivity extends Model {
	public function getActivities() {
		$query = $this->db->query("SELECT a.key, a.data, a.date_added FROM ((SELECT CONCAT('customer_', ca.key) AS `key`, ca.data, ca.date_added FROM `" . DB_PREFIX . "customer_activity` ca ORDER BY ca.date_added DESC LIMIT 0,10) UNION (SELECT CONCAT('affiliate_', aa.key) AS `key`, aa.data, aa.date_added FROM `" . DB_PREFIX . "affiliate_activity` aa ORDER BY aa.date_added DESC LIMIT 0,10)) a ORDER BY a.date_added DESC LIMIT 0,10");

		return $query->rows;
	}
}