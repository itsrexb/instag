<?php
class ModelSaleOrderTransaction extends Model {
	public function addOrderTransaction($order_id, $data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "order_transaction` SET order_id = '" . (int)$order_id . "', date_added = NOW()";

		$set_data = array();

		if (isset($data['transaction_id'])) {
			$set_data[] = "transaction_id = '" . $this->db->escape($data['transaction_id']) . "'";
		}

		if (isset($data['reference_transaction_id'])) {
			$set_data[] = "reference_transaction_id = '" . $this->db->escape($data['reference_transaction_id']) . "'";
		}

		if (isset($data['authorization_code'])) {
			$set_data[] = "authorization_code = '" . $this->db->escape($data['authorization_code']) . "'";
		}

		if (isset($data['payment_method'])) {
			$set_data[] = "payment_method = '" . $this->db->escape($data['payment_method']) . "'";
		}

		if (isset($data['payment_code'])) {
			$set_data[] = "payment_code = '" . $this->db->escape($data['payment_code']) . "'";
		}

		if (isset($data['amount'])) {
			$set_data[] = "amount = '" . (float)$data['amount'] . "'";
		}

		if (isset($data['currency_id'])) {
			$set_data[] = "currency_id = '" . (int)$data['currency_id'] . "'";
		}

		if (isset($data['currency_code'])) {
			$set_data[] = "currency_code = '" . $this->db->escape($data['currency_code']) . "'";
		}

		if (isset($data['currency_value'])) {
			$set_data[] = "currency_value = '" . (float)$data['currency_value'] . "'";
		}

		if (isset($data['status'])) {
			$set_data[] = "status = '" . (int)$data['status'] . "'";
		}

		if (isset($data['meta_data'])) {
			if (is_array($data['meta_data'])) {
				$set_data[] = "meta_data = '" . $this->db->escape(json_encode($data['meta_data'])) . "'";
			} else {
				$set_data[] = "meta_data = '" . $this->db->escape($data['meta_data']) . "'";
			}
		}

		if ($set_data) {
			$sql .= ", " . implode(', ', $set_data);
		}

		$this->db->query($sql);
	}

	public function captureOrderTransaction($order_transaction_id, $amount, $transaction_id = '', $authorization_code = '') {
		$sql = "UPDATE `" . DB_PREFIX . "order_transaction` SET amount = '" . (float)$amount . "', status = '1'";

		$set_data = array();

		if ($transaction_id) {
			$set_data[] = "transaction_id = '" . $this->db->escape($transaction_id) . "'";
		}

		if ($authorization_code) {
			$set_data[] = "authorization_code = '" . $this->db->escape($authorization_code) . "'";
		}

		if ($set_data) {
			$sql .= ", " . implode(', ', $set_data);
		}

		$sql .= " WHERE order_transaction_id = '" . (int)$order_transaction_id . "'";

		$this->db->query($sql);
	}

	public function voidOrderTransaction($order_transaction_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order_transaction` SET amount = '0', status = '2' WHERE order_transaction_id = '" . (int)$order_transaction_id . "'");
	}

	public function getOrderTransaction($order_transaction_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_transaction` WHERE order_transaction_id = '" . (int)$order_transaction_id . "'");

		$order_transaction_info = $query->row;

		if ($order_transaction_info['meta_data']) {
			$order_transaction_info['meta_data'] = json_decode($order_transaction_info['meta_data'], true);
		}

		return $order_transaction_info;
	}

	public function getOrderTransactions($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_transaction` WHERE order_id = '" . (int)$order_id . "' ORDER BY date_added ASC");

		return $query->rows;
	}

	public function getTotalAmountPaidForOrder($order_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "order_transaction` WHERE order_id = '" . (int)$order_id . "'");

		return ($query->row && $query->row['total'] > 0 ? $query->row['total'] : 0);
	}
}