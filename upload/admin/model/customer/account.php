<?php
class ModelCustomerAccount extends Model {
	public function editAccount($account_id, $data) {
		if (!empty($data['date_expires'])) {
			// convert local time zone to UTC
			$date_expires = new DateTime($data['date_expires'], new DateTimeZone($this->config->get('config_timezone')));
			$date_expires->setTimezone(new DateTimeZone('UTC'));

			$data['date_expires'] = $date_expires->format('Y-m-d H:i:s');

			$this->instaghive->login($data['customer_id']);

			$this->instaghive->account->edit_expiration($account_id, date('Y-m-d H:i:s', strtotime($data['date_expires'])));

			$this->instaghive->logout();

			$this->db->query("UPDATE `" . DB_PREFIX . "account` SET date_expires = '" . $this->db->escape(date('Y-m-d H:i:s', strtotime($data['date_expires']))) . "' WHERE account_id = '" . $this->db->escape($account_id) . "'");

			$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET  date_next_recurring = '" . $this->db->escape(date('Y-m-d H:i:s', strtotime($data['date_expires']))) . "' WHERE account_id = '" . $this->db->escape($account_id) . "'  LIMIT 1");
		}

		if (!empty($data['new_customer_id'])) {
			$result = $this->instaghive->adminaccount->migrate($data['customer_id'], $account_id, $data['new_customer_id']);

			if ($result) {
				// update account record to use the new customer
				$this->db->query("UPDATE `" . DB_PREFIX . "account` SET customer_id = '" . (int)$data['new_customer_id'] . "' WHERE customer_id = '" . (int)$data['customer_id'] . "' AND account_id = '" . $this->db->escape($account_id) . "'");

				// cancel recurring orders
				$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET active = '0', date_canceled = NOW() WHERE customer_id = '" . (int)$data['customer_id'] . "' AND account_id = '" . $this->db->escape($account_id) . "'");
			}
		}
	}

	public function deleteAccount($customer_id, $account_id) {
		$this->instaghive->login($customer_id);

		$this->instaghive->account->delete($account_id);

		$this->db->query("UPDATE `" . DB_PREFIX . "account` SET deleted = '1', date_deleted = NOW() WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "'");

		$this->db->query("UPDATE `" . DB_PREFIX . "recurring_order` SET active = '0', date_canceled = NOW() WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "'");

		$this->instaghive->logout();
	}

	public function reactivateAccount($customer_id, $account_id) {
		$this->instaghive->adminaccount->reactivate($customer_id, $account_id);

		$this->db->query("UPDATE `" . DB_PREFIX . "account` SET deleted = '0', date_deleted = '0000-00-00 00:00:00' WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "'");
	}

	public function getAccount($account_id) {
		$account_data = array();

		$query = $this->db->query("SELECT a.*, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM `" . DB_PREFIX . "account` a LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = a.customer_id) WHERE a.account_id = '" . $this->db->escape($account_id) . "'");

		if ($query->row) {
			if (!$query->row['deleted']) {
				$this->instaghive->login($query->row['customer_id']);

				$account_data = $this->instaghive->account->get($account_id, true);

				if ($account_data) {
					// update local database cache of account information
					$update_data = array();

					if ($account_data->NetworkId != $query->row['network_id']) {
						$update_data['network_id'] = $account_data->NetworkId;
					}

					if ($account_data->Username != $query->row['username']) {
						$update_data['username'] = $account_data->Username;
					}

					if ($account_data->Status != $query->row['status']) {
						$update_data['status'] = $account_data->Status;
					}

					if (isset($account_data->ExpiresDateTime) && $account_data->ExpiresDateTime != $query->row['date_expires']) {
						$update_data['date_expires'] = $account_data->ExpiresDateTime;
					}

					if ($account_data->AddedDateTime != $query->row['date_added']) {
						$update_data['date_added'] = $account_data->AddedDateTime;
					}

					if ($update_data) {
						$this->db->update(array(
							'table'  => 'account',
							'fields' => $update_data,
							'conditions' => array(
								'customer_id' => (int)$query->row['customer_id'],
								'account_id'  => $account_id
							)
						));
					}

					$account_data->Customer   = $query->row['customer'];
					$account_data->CustomerId = $query->row['customer_id'];

					// convert UTC to local time zone
					$store_timezone = new DateTimeZone($this->config->get('config_timezone'));

					if (isset($account_data->ExpiresDateTime)) {
						$date_expires = new DateTime($account_data->ExpiresDateTime);
						$date_expires->setTimezone($store_timezone);

						$account_data->ExpiresDateTime = $date_expires->format('Y-m-d H:i:s');
					} else {
						$account_data->ExpiresDateTime = '';
					}

					$date_added = new DateTime($account_data->AddedDateTime);
					$date_added->setTimezone($store_timezone);

					$account_data->AddedDateTime = $date_added->format('Y-m-d H:i:s');
				} else {
					// account was not returned from hive, must be deleted
					$this->db->query("UPDATE `" . DB_PREFIX . "account` SET deleted = '1', date_deleted = NOW() WHERE customer_id = '" . (int)$query->row['customer_id'] . "' AND account_id = '" . $this->db->escape($account_id) . "'");
				}

				$this->instaghive->logout();
			}

			if (!$account_data) {
				// return local cache data if account has been deleted.
				$account_data = new stdClass();
				$account_data->Deleted      = 1;
				$account_data->CustomerId   = $query->row['customer_id'];
				$account_data->Id           = $query->row['account_id'];
				$account_data->NetworkId    = $query->row['network_id'];
				$account_data->Username     = $query->row['username'];
				$account_data->Type         = $query->row['type'];
				$account_data->DateMetaData = $query->row['date_meta_data'];
				$account_data->Status       = $query->row['status'];
				$account_data->Customer     = $query->row['customer'];

				// convert UTC to local time zone
				$store_timezone = new DateTimeZone($this->config->get('config_timezone'));

				if (isset($query->row['date_expires'])) {
					$date_expires = new DateTime($query->row['date_expires']);
					$date_expires->setTimezone($store_timezone);

					$account_data->ExpiresDateTime = $date_expires->format('Y-m-d H:i:s');
				} else {
					$account_data->ExpiresDateTime = '';
				}

				$date_added = new DateTime($query->row['date_added']);
				$date_added->setTimezone($store_timezone);

				$account_data->AddedDateTime = $date_added->format('Y-m-d H:i:s');
			}
		}

		return $account_data;
	}

	public function getAccounts($data = array()) {
		$sql = "SELECT a.customer_id, a.account_id, a.network_id, a.username, a.type, a.date_meta_data, a.status, a.deleted, a.date_expires, a.date_added, c.email, c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM `" . DB_PREFIX . "account` a LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = a.customer_id)";

		$where_data = array();

		if (!empty($data['filter_account_id'])) {
			$where_data[] = "a.account_id = '" . $this->db->escape($data['filter_account_id']) . "'";
		}

		if (!empty($data['filter_network_id'])) {
			$where_data[] = "a.network_id = '" . $this->db->escape($data['filter_network_id']) . "'";
		}

		if (!empty($data['filter_username'])) {
			$where_data[] = "a.username LIKE '%" . $this->db->escape($data['filter_username']) . "%'";
		}

		if (!empty($data['filter_type'])) {
			$where_data[] = "a.type = '" . $this->db->escape($data['filter_type']) . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$where_data[] = "a.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email = '" . $data['filter_email'] . "'";
		}

		if (!empty($data['filter_status'])) {
			$where_data[] = "a.status = '" . $this->db->escape($data['filter_status']) . "'";
		}

		if (!is_null($data['filter_deleted'])) {
			$where_data[] = "a.deleted = '" . $this->db->escape($data['filter_deleted']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_expires_start'])) {
			$filter_date_expires_start = new \DateTime($data['filter_date_expires_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_expires_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_expires >= '" . $this->db->escape($filter_date_expires_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_expires_end'])) {
			$filter_date_expires_end = new \DateTime($data['filter_date_expires_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_expires_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_expires <= '" . $this->db->escape($filter_date_expires_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$sort_data = array(
			'a.account_id',
			'a.username',
			'a.type',
			'customer',
			'c.email',
			'a.status',
			'a.deleted',
			'a.date_expires',
			'a.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.username";
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

	public function getTotalAccounts($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "account` a LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = a.customer_id)";
		
		$where_data = array();

		if (!empty($data['filter_account_id'])) {
			$where_data[] = "a.account_id = '" . $this->db->escape($data['filter_account_id']) . "'";
		}

		if (!empty($data['filter_network_id'])) {
			$where_data[] = "a.network_id = '" . $this->db->escape($data['filter_network_id']) . "'";
		}

		if (!empty($data['filter_username'])) {
			$where_data[] = "a.username LIKE '%" . $this->db->escape($data['filter_username']) . "%'";
		}

		if (!empty($data['filter_type'])) {
			$where_data[] = "a.type = '" . $this->db->escape($data['filter_type']) . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$where_data[] = "a.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_email'])) {
			$where_data[] = "c.email = '" . $data['filter_email'] . "'";
		}

		if (!empty($data['filter_status'])) {
			$where_data[] = "a.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_deleted'])) {
			$where_data[] = "a.deleted = '" . $this->db->escape($data['filter_deleted']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$where_data[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_expires_start'])) {
			$filter_date_expires_start = new \DateTime($data['filter_date_expires_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_expires_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_expires >= '" . $this->db->escape($filter_date_expires_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_expires_end'])) {
			$filter_date_expires_end = new \DateTime($data['filter_date_expires_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_expires_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_expires <= '" . $this->db->escape($filter_date_expires_end->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_start'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_added_end'])) {
			$filter_date_added_end = new \DateTime($data['filter_date_added_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "a.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getStatuses() {
		$query = $this->db->query("SELECT DISTINCT status FROM `" . DB_PREFIX . "account` order by status ASC");

		return $query->rows;
	}
}