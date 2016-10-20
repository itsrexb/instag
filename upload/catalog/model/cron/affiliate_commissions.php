<?php
class ModelCronAffiliateCommissions extends Model {
	public function getActiveAffiliates() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate` WHERE status = '1' AND approved = '1'");

		return $query->rows;
	}

	public function getAffiliateActiveCustomers($affiliate_id) {
		$query = $this->db->query("SELECT c.customer_id, c.affiliate_commission_count, CONCAT(c.firstname, ' ', c.lastname) AS name FROM `" . DB_PREFIX . "customer` c RIGHT JOIN `" . DB_PREFIX . "recurring_order` ro ON (ro.customer_id = c.customer_id) WHERE c.affiliate_id = '" . (int)$affiliate_id . "' AND c.status = '1' AND c.approved = '1' AND ro.active = '1' GROUP BY c.customer_id");

		return $query->rows;
	}

	public function getCustomerActiveRecurringOrders($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$customer_id . "' AND active = '1' ORDER BY recurring_order_id ASC");

		return $query->rows;
	}

	public function getLastOrderForRecurringOrder($recurring_order_id) {
		$query = $this->db->query("SELECT op.*, o.date_added FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id) WHERE op.recurring_order_id = '" . (int)$recurring_order_id . "' AND o.order_status_id IN ('" . implode("','", $this->config->get('config_complete_status')) . "') GROUP BY op.order_product_id ORDER BY o.date_added DESC LIMIT 1");

		return $query->row;
	}
}