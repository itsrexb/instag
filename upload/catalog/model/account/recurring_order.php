<?php
class ModelAccountRecurringOrder extends Model {
	public function cancelRecurringOrder($account_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET active = '0', date_canceled = NOW() WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "'");
	}

	public function getActiveRecurringOrder($account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "' AND active = '1' ORDER BY recurring_order_id DESC LIMIT 1");

		return $query->row;
	}

	public function getActiveRecurringOrders($account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "' AND active = '1' ORDER BY date_next_recurring ASC");

		return $query->rows;
	}

	public function getLastRecurringOrder($account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "' ORDER BY active DESC, recurring_order_id DESC LIMIT 1");

		return $query->row;
	}
}