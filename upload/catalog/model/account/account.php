<?php
class ModelAccountAccount extends Model {
	public function addAccount($type, $username, $password) {
		switch($type)
		{
		case 'instagram':
			$result = $this->instaghive->instagram->insert($username, $password);
			break;
		default:
			$result = false;
		}

		if ($result && !empty($result->data)) {
			$this->db->insert(array(
				'table'  => 'account',
				'fields' => array(
					'customer_id' => (int)$this->customer->getId(),
					'account_id'  => $result->account_id,
					'network_id'  => $result->network_id,
					'username'    => $username,
					'type'        => $type,
					'status'      => 'stopped',
					'date_added'  => date('Y-m-d H:i:s')
				)
			));
		}

		return $result;
	}

	public function setAccountExpirationDate($account_id, $expiration_date) {
		$this->db->query("UPDATE `" . DB_PREFIX . "account` SET date_expires = '" . $this->db->escape(date('Y-m-d H:i:s', strtotime($expiration_date))) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id  = '" . $this->db->escape($account_id) . "'");
	}

	public function reconnectAccount($type, $account_id, $username, $password) {
		switch($type)
		{
		case 'instagram':
			$result = $this->instaghive->instagram->reconnect($account_id, $username, $password);
			break;
		default:
			$result = false;
		}

		return $result;
	}

	public function startAccount($account_id) {
		$result = $this->instaghive->account->start($account_id);

		if ($result) {
			$expired = false;

			// check cached account for expiration date, if none set one
			$account_cache_info = $this->getAccountFromCache($account_id);

			if ($account_cache_info) {
				if ($account_cache_info['date_expires'] == '0000-00-00 00:00:00') {
					$date_expires = '';

					$account_info = $this->instaghive->account->get($account_id);

					if ($account_info) {
						if (isset($account_info->ExpiresDateTime)) {
							$date_expires = $account_info->ExpiresDateTime;
						} else {
							// if an account has ever existed do not give a free trial
							if ($this->instaghive->instagram->already_existed($account_info->NetworkId)) {
								$this->instaghive->account->edit_expiration($account_id, date('Y-m-d H:i:s'));

								$date_expires = date('Y-m-d H:i:s');
								$expired      = true;
							} else {
								// give free trial of 5 days (make configurable in the future)
								$this->instaghive->account->update_expiration($account_id, 5, 'day');

								$date_expires = date('Y-m-d H:i:s', strtotime('+5 days'));
							}
						}
					}

					if ($date_expires) {
						// update local cache with expiration date
						$this->setAccountExpirationDate($account_id, $date_expires);
					}
				} else if (strtotime($account_cache_info['date_expires']) < time()) {
					$expired = true;
				}
			}

			if (!$expired) {
				$this->db->update(array(
					'table'      => 'account',
					'fields'     => array('status' => 'started'),
					'conditions' => array(
						'customer_id' => (int)$this->customer->getId(),
						'account_id'  => $account_id
					)
				));
			}

			return $result;
		}

		return false;
	}

	public function stopAccount($account_id) {
		$result = $this->instaghive->account->stop($account_id);

		if ($result) {
			$this->db->update(array(
				'table'      => 'account',
				'fields'     => array('status' => 'stopped'),
				'conditions' => array(
					'customer_id' => (int)$this->customer->getId(),
					'account_id'  => $account_id
				)
			));

			return $result;
		}

		return false;
	}

	public function deleteAccount($account_id) {
		$result = $this->instaghive->account->delete($account_id);

		if ($result) {
			$this->db->query("UPDATE `" . DB_PREFIX . "account` SET deleted = '1', date_deleted = NOW() WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id  = '" . $this->db->escape($account_id) . "'");
		}
	}

	public function getAccount($account_id) {
		$account_info = $this->instaghive->account->get($account_id, true);

		if ($account_info) {
			switch($account_info->Type)
			{
			case 'instagram':
				$this->load->model('account/instagram');
				$account_info->MetaData = $this->model_account_instagram->getMetaData($account_info, array('info', 'recent_media'));
				break;
			}
		}

		return $account_info;
	}

	public function getAccountFromCache($account_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id  = '" . $this->db->escape($account_id) . "'");

		if ($query->row) {
			$query->row['meta_data'] = json_decode($query->row['meta_data'], true);
		}

		return $query->row;
	}

	public function getAccounts($type = '') {
		// get list of existing accounts in local database
		$existing_accounts = array();

		$account_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		foreach ($account_query->rows as $account) {
			$existing_accounts[$account['account_id']] = $account;
		}

		$account_data = $this->instaghive->account->get_list($type);

		foreach ($account_data as $key => $account_info) {
			if (isset($existing_accounts[$account_info->Id])) {
				$update_data = array();

				if ($account_info->NetworkId != $existing_accounts[$account_info->Id]['network_id']) {
					$update_data['network_id'] = $account_info->NetworkId;
				}

				if ($account_info->Username != $existing_accounts[$account_info->Id]['username']) {
					$update_data['username'] = $account_info->Username;
				}

				if (isset($account_info->ExpiresDateTime) && $account_info->ExpiresDateTime != $existing_accounts[$account_info->Id]['date_expires']) {
					$update_data['date_expires'] = $account_info->ExpiresDateTime;
				}

				if ($account_info->AddedDateTime != $existing_accounts[$account_info->Id]['date_added']) {
					$update_data['date_added'] = $account_info->AddedDateTime;
				}

				if ($account_info->Status != $existing_accounts[$account_info->Id]['status']) {
					$update_data['status'] = $account_info->Status;
				}

				if ($existing_accounts[$account_info->Id]['deleted']) {
					$update_data['deleted'] = 0;
				}

				if ($update_data) {
					$this->db->update(array(
						'table'  => 'account',
						'fields' => $update_data,
						'conditions' => array(
							'customer_id' => (int)$this->customer->getId(),
							'account_id'  => $account_info->Id
						)
					));
				}

				// remove account from array so we know which accounts no longer exist
				unset($existing_accounts[$account_info->Id]);
			} else {
				// account doesn't exist in local table, insert it
				$insert_data = array(
					'customer_id' => (int)$this->customer->getId(),
					'account_id'  => $account_info->Id,
					'network_id'  => $account_info->NetworkId,
					'username'    => $account_info->Username,
					'type'        => $account_info->Type,
					'date_added'  => $account_info->AddedDateTime
				);

				if (isset($account_info->ExpiresDateTime)) {
					$insert_data['date_expires'] = $account_info->ExpiresDateTime;
				}

				$this->db->insert(array(
					'table'  => 'account',
					'fields' => $insert_data
				));
			}

			switch($account_info->Type)
			{
			case 'instagram':
				$this->load->model('account/instagram');
				$account_data[$key]->MetaData = $this->model_account_instagram->getMetaData($account_info, array('info'), false);

				break;
			}
		}

		// remove any existing accounts that no longer exist or haven't been set to deleted yet
		foreach ($existing_accounts as $account_id => $account) {
			if (!$account['deleted']) {
				$this->db->query("UPDATE  `" . DB_PREFIX . "account` SET deleted = '1', date_deleted = NOW() WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "'");
			}
		}

		return $account_data;
	}

	public function getAccountType($account_id) {
		$account_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_id) . "'");

		return ($account_query->row ? $account_query->row['type'] : '');
	}

	public function changeFlow($account_id, $flow) {
		return $this->instaghive->account->flow($account_id, $flow);
	}

	public function getFollowers($account_id, $date_start, $date_end, $code = 'follow', $limit = 0, $organic = true){
		return $this->instaghive->follower->get_list($account_id, $date_start, $date_end, $code, $limit, $organic);
	}
}