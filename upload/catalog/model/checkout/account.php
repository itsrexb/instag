<?php
class ModelCheckoutAccount extends Model {
	public function editExpiration($account_id, $date) {
		$result = $this->instaghive->account->edit_expiration($account_id, $date);

		$this->db->query("UPDATE `" . DB_PREFIX . "account` SET date_expires = '" . $this->db->escape($date) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "'");

		return $result;
	}

	public function updateExpiration($account_id, $extension, $frequency) {
		$result = $this->instaghive->account->update_expiration($account_id, $extension, $frequency);

		$this->load->model('account/account');

		$account_info = $this->model_account_account->getAccountFromCache($account_id);

		if ($account_info) {
			if ($account_info['date_expires'] && strtotime($account_info['date_expires']) > time()) {
				$date = date('Y-m-d H:i:s', strtotime('+' . $extension . ' ' . $frequency, strtotime($account_info['date_expires'])));
			} else {
				$date = date('Y-m-d H:i:s', strtotime('+' . $extension . ' ' . $frequency));
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "account` SET date_expires = '" . $this->db->escape($date) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "'");
		}

		return $result;
	}
}