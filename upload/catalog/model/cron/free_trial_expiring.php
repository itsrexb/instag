<?php
class ModelCronFreeTrialExpiring extends Model {
	public function getFreeTrialExpiringAccounts($date_expires_start, $date_expires_end) {
		$sql = "SELECT a.*, c.firstname, c.lastname, c.email FROM `" . DB_PREFIX . "account` a LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = a.customer_id) LEFT JOIN `" . DB_PREFIX . "recurring_order` ro ON (ro.customer_id = a.customer_id AND ro.account_id = a.account_id) WHERE a.date_expires >= '" . $this->db->escape($date_expires_start) . "' AND a.date_expires <= '" . $this->db->escape($date_expires_end) . "' AND ro.recurring_order_id IS NULL";

		$query = $this->db->query($sql);

		return $query->rows;
	}
}