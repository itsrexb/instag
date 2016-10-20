<?php
// TODO: this will need to be enhanced when we start doing upsell plans
class ModelAccountCapability extends Model {
	public function getAccountCapabilities($account_id) {
		$capability_data = array();

		// get capabilities from last active plan they were on
		$query = $this->db->query("SELECT DISTINCT c.name FROM `" . DB_PREFIX . "capability` c LEFT JOIN `" . DB_PREFIX . "product_capability` pc ON (pc.capability_id = c.capability_id) WHERE pc.product_id = (SELECT ro.product_id FROM `" . DB_PREFIX . "recurring_order` ro WHERE ro.customer_id = '" . (int)$this->customer->getId() . "' AND ro.account_id = '" . $this->db->escape($account_id) . "' ORDER BY ro.active DESC, ro.recurring_order_id DESC LIMIT 1)");

		if (!$query->rows) {
			// this account has never been on a plan before so they must be in the free trial or an expired free trial
			$query = $this->db->query("SELECT DISTINCT name FROM `" . DB_PREFIX . "capability` WHERE free_trial = '1'");
		}

		foreach ($query->rows as $capability) {
			$capability_data[] = $capability['name'];
		}

		return $capability_data;
	}
}