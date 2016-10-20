<?php
class ModelAccountInstagram extends Model {
	public function getMetaData(&$account_info, $required_data = array(), $refresh_stale = true) {
		$meta_data = array();
		$updated   = false;

		$account_query = $this->db->query("SELECT meta_data, date_meta_data FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($account_info->Id) . "'");

		if ($account_query->row) {
			// only use meta data if it's recent enough
			if ($account_query->row && $account_query->row['meta_data']) {
				$meta_data = json_decode($account_query->row['meta_data'], true);
			}

			// get array of required_data that is needed to be refreshed, do it in one call (need to make endpoint)
			$refresh_data = array();

			// if info is required, make sure meta data has it, unless account has a status message
			if (in_array('info', $required_data) && (!isset($account_info->StatusMessage) || $account_info->StatusMessage != 'invalid_token') && (!isset($meta_data['info']) || ($refresh_stale && strtotime($account_query->row['date_meta_data']) < strtotime('-' . $this->config->get('config_stale_meta_data_limit') . ' minutes')))) {
				$refresh_data[] = 'info';
			}

			// if recent_media is required, make sure meta data has it, unless account has a status message
			if (in_array('recent_media', $required_data) && (!isset($account_info->StatusMessage) || $account_info->StatusMessage != 'invalid_token') && (!isset($meta_data['recent_media']) || ($refresh_stale && strtotime($account_query->row['date_meta_data']) < strtotime('-' . $this->config->get('config_stale_meta_data_limit') . ' minutes')))) {
				$refresh_data[] = 'recent_media';
			}

			// if there's data that needs to be refreshed, refresh it
			if ($refresh_data) {
				$result = $this->instaghive->instagram->get($account_info->Id, $refresh_data);
				if ($result->success && !empty($result->data)) {
					foreach ($result->data as $key => $value) {
						//Make sure no empty data will override the local cache
						if(!empty($value)){
							$meta_data[$key] = json_decode(json_encode($value), true);
						}
					}

					$updated = true;
				}
			}

			// set the username in case it's changed
			if (!empty($meta_data['info']['username']) && $meta_data['info']['username'] != $account_info->Username) {
				$account_info->Username = $meta_data['info']['username'];

				$this->instaghive->account->update_username($account_info->Id, $meta_data['info']['username']);
			}

			// if any of the required data does not exist in meta data, add a blank array for it
			foreach ($required_data as $key) {
				if (!isset($meta_data[$key])) {
					$meta_data[$key] = array();
				}
			}

			if ($updated) {
				$this->db->update(array(
					'table'  => 'account',
					'fields' => array(
						'username'       => $account_info->Username,
						'meta_data'      => json_encode($meta_data),
						'date_meta_data' => date('Y-m-d H:i:s')
					),
					'conditions' => array(
						'customer_id' => (int)$this->customer->getId(),
						'account_id'  => $account_info->Id
					)
				));
			}
		}

		return $meta_data;
	}
}