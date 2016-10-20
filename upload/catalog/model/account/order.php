<?php
class ModelAccountOrder extends Model {
	public function getAccountOrders($account_id, $limit = 0) {
		$sql = "SELECT o.* FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id) WHERE op.account_id = '" . $this->db->escape($account_id) . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY o.order_id ORDER BY o.date_added DESC";

		if ($limit) {
			$sql .= " LIMIT " . (int)$limit;
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "' ORDER BY order_id ASC");

		return $query->rows;
	}
}