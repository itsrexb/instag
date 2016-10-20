<?php
class ModelAffiliateTransaction extends Model {
	public function addTransaction($affiliate_id, $customer_id = 0, $amount = 0, $description = '', $gross_amount = 0, $fee = 0) {
		$this->load->model('affiliate/affiliate');

		$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_transaction` SET affiliate_id = '" . (int)$affiliate_id . "', customer_id = '" . (int)$customer_id . "', amount = '" . (float)$this->currency->format($amount, '', '', false) . "', description = '" . $this->db->escape($description) . "', gross_amount = '" . (float)$this->currency->format($gross_amount, '', '', false) . "', fee = '" . (float)$this->currency->format($fee, '', '', false) . "', date_added = NOW()");
		}
	}

	public function deleteTransaction($affiliate_transaction_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_transaction_id = '" . (int)$affiliate_transaction_id . "'");
	}

	public function getTransactionTotal($affiliate_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		return $query->row['total'];
	}

	public function getTransactions($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "'";

		$where_data = array();

		if (isset($data['filter_customer_id'])) {
			$where_data[] = "customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if ($where_data) {
			$sql .= " AND " . implode(" AND ", $where_data);
		}

		$sort_data = array(
			'amount',
			'description',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalTransactions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "'");

		return $query->row['total'];
	}

	public function getBalance() {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "' GROUP BY affiliate_id");

		if ($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}
}