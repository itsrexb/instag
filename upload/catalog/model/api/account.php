<?php 
class ModelApiAccount extends Model {
	public function getAccount($account_id) {
		$query = $this->db->query("SELECT a.*, c.firstname AS customer_firstname, c.lastname AS customer_lastname, c.email AS customer_email FROM `" . DB_PREFIX . "account` AS a LEFT JOIN  `" . DB_PREFIX . "customer` AS c ON (c.customer_id = a.customer_id) WHERE a.account_id = '" . $this->db->escape($account_id) . "'");

		return $query->row;
	}

	public function updateAccount($account_id, $update_data = array()) {
		if ($update_data) {
			$this->db->update(array(
				'table'      => 'account',
				'fields'     => $update_data,
				'conditions' => array(
					'account_id'  => $account_id
				)
			));
		}
	}
}