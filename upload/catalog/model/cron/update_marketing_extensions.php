<?php
class ModelCronUpdateMarketingExtensions extends Model {
	public function getExpiredAccounts($date_expires_start, $date_expires_end) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "account` WHERE date_expires >= '" . $this->db->escape($date_expires_start) . "' AND date_expires <= '" . $this->db->escape($date_expires_end) . "'";

		$query = $this->db->query($sql);

		return $query->rows;
	}
}