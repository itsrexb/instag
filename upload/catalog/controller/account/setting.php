<?php 
class ControllerAccountSetting extends Controller {
	private $error = array();

	/*
	Updates the settings for an account.

	*NOTE* updating a list will append to whatever exists currently.

	Parameters
	----------
	account_id: (string)
	data: {
		follow_no_private:       1|0
		follow_source_locations: [{id:, name:, subtitle:}, ...]
		follow_source_tags:      ['tag', ...]
		follow_source_users:     [{id:, username:}, ...]
		follow_speed:            slow|medium|fast
		follow_status:           1|0
		follows_max_limit:       (integer)
		follows_min_limit:       (integer)
		like_status:             1|0
		sleep_end_max:           0 - 23 (convert to UTC hour before sending)
		sleep_end_min:           0 - 23 (convert to UTC hour before sending)
		sleep_start_max:         0 - 23 (convert to UTC hour before sending)
		sleep_start_min:         0 - 23 (convert to UTC hour before sending)
		sleep_status:            1|0
		unfollow_speed:          slow|medium|fast
		unfollow_status:         1|0
		whitelist_users:         [{id:, username:}, ...]
	}

	RESPONSE
	--------
	success:  true|false
	data:     data that was used in the settings update, helpful when adding to a list
	redirect: url to redirect to
	error:    (array)
	*/
	public function edit() {
		$this->load->language('account/setting');

		$this->load->model('account/setting');

		$json = array('success' => false);

		if ($this->validateEdit()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$data       = $this->request->post['data'];

			$result = $this->model_account_setting->editSettings($account_id, $data);

			if ($result->success) {
				$json['success'] = true;
				$json['data']    = $data;
			} else if (!empty($result->errors)) {
				$json['errors'] = array();

				foreach ($result->errors as $key => $value) {
					$json['errors'][] = $this->language->get('error_hive_' . strtolower(str_replace(' ', '_', $value)));
				}
			}
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateEdit() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			} else {
				$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

				if (!isset($this->request->post['data']) || !is_array($this->request->post['data'])) {
					$this->error['data'] = $this->language->get('error_data');
				} else {
					foreach ($this->request->post['data'] as $key => $value) {
						switch($key)
						{
						case 'follows_max_limit':
							if (isset($this->request->post['data']['follows_min_limit'])) {
								if ((int)$value <= $this->request->post['data']['follows_min_limit']) {
									$this->error['follows_max_limit'] = $this->language->get('error_follows_max_limit');
								}
							} else if (isset($this->account_settings->follows_min_limit) && (int)$value <= $this->account_settings->follows_min_limit) {
								$this->error['follows_max_limit'] = $this->language->get('error_follows_max_limit');
							}

							break;
						case 'follows_min_limit':
							if (isset($this->request->post['data']['follows_max_limit'])) {
								if ((int)$value >= $this->request->post['data']['follows_max_limit']) {
									$this->error['follows_min_limit'] = $this->language->get('error_follows_min_limit');
								}
							} else if (isset($this->account_settings->follows_max_limit) && (int)$value >= $this->account_settings->follows_max_limit) {
								$this->error['follows_min_limit'] = $this->language->get('error_follows_min_limit');
							}

							break;
						case 'follow_speed':
							if (!isset($account_capabilities)) {
								$this->load->model('account/capability');

								$account_capabilities = $this->model_account_capability->getAccountCapabilities($account_id);
							}

							if (!in_array('speed_' . $value, $account_capabilities)) {
								$this->error['follow_speed'] = $this->language->get('error_follow_speed');
							}

							break;
						case 'unfollow_speed':
							if (!isset($account_capabilities)) {
								$this->load->model('account/capability');

								$account_capabilities = $this->model_account_capability->getAccountCapabilities($account_id);
							}

							if (!in_array('speed_' . $value, $account_capabilities)) {
								$this->error['unfollow_speed'] = $this->language->get('error_unfollow_speed');
							}

							break;
						case 'whitelist_users':
							if (!isset($account_settings)) {
								$account_settings = $this->model_account_setting->getSettings($account_id);
							}

							$whitelist_users = array();

							foreach ($value as $user) {
								if (!isset($user['id']) || !isset($user['username'])) {
									continue;
								}

								// make sure no duplicates were sent over
								foreach ($whitelist_users as $whitelist_user) {
									if ($user['id'] == $whitelist_user->id) {
										continue 2;
									}
								}

								// make sure this user isn't already in the list
								if (isset($account_settings->whitelist_users)) {
									foreach ($account_settings->whitelist_users as $whitelist_user) {
										if ($user['id'] == $whitelist_user->id) {
											continue 2;
										}
									}
								}

								$whitelist_users[] = (object)$user;
							}

							if ($whitelist_users) {
								$this->request->post['data']['whitelist_users'] = $whitelist_users;
							} else {
								$this->error['whitelist_users'] = $this->language->get('error_whitelist_users');
							}

							break;
						case 'follow_source_users':
							if (!isset($account_settings)) {
								$account_settings = $this->model_account_setting->getSettings($account_id);
							}

							$follow_source_users = array();

							foreach ($value as $user) {
								if (!isset($user['id']) || !isset($user['username'])) {
									continue;
								}

								// make sure no duplicates were sent over
								foreach ($follow_source_users as $follow_source_user) {
									if ($user['id'] == $follow_source_user->id) {
										continue 2;
									}
								}

								// make sure this user isn't already in the list
								if (isset($account_settings->follow_source_users)) {
									foreach ($account_settings->follow_source_users as $follow_source_user) {
										if ($user['id'] == $follow_source_user->id) {
											continue 2;
										}
									}
								}

								$follow_source_users[] = (object)$user;
							}

							if ($follow_source_users) {
								$this->request->post['data']['follow_source_users'] = $follow_source_users;
							} else {
								$this->error['follow_source_users'] = $this->language->get('error_follow_source_users');
							}

							break;
						case 'follow_source_locations':
							if (!isset($account_settings)) {
								$account_settings = $this->model_account_setting->getSettings($account_id);
							}

							$follow_source_locations = array();

							foreach ($value as $location) {
								if (!isset($location['id']) || !isset($location['name'])) {
									continue;
								}

								// make sure no duplicates were sent over
								foreach ($follow_source_locations as $follow_source_location) {
									if ($location['id'] == $follow_source_location->id) {
										continue 2;
									}
								}

								// make sure this location isn't already in the list
								if (isset($account_settings->follow_source_locations)) {
									foreach ($account_settings->follow_source_locations as $follow_source_location) {
										if ($location['id'] == $follow_source_location->id) {
											continue 2;
										}
									}
								}

								$follow_source_locations[] = (object)$location;
							}

							if ($follow_source_locations) {
								$this->request->post['data']['follow_source_locations'] = $follow_source_locations;
							} else {
								$this->error['follow_source_locations'] = $this->language->get('error_follow_source_locations');
							}

							break;
						case 'follow_source_tags':
							if (!isset($account_settings)) {
								$account_settings = $this->model_account_setting->getSettings($account_id);
							}

							$follow_source_tags = array();

							foreach ($value as $tag) {
								// make sure no duplicates were sent over
								foreach ($follow_source_tags as $follow_source_tag) {
									if ($tag == $follow_source_tag) {
										continue 2;
									}
								}

								// make sure this tag isn't already in the list
								if (isset($account_settings->follow_source_tags)) {
									foreach ($account_settings->follow_source_tags as $follow_source_tag) {
										if ($tag == $follow_source_tag) {
											continue 2;
										}
									}
								}

								$follow_source_tags[] = $tag;
							}

							if ($follow_source_tags) {
								$this->request->post['data']['follow_source_tags'] = $follow_source_tags;
							} else {
								$this->error['follow_source_tags'] = $this->language->get('error_follow_source_tags');
							}

							break;
						}
					}
				}
			}
		}

		return !$this->error;
	}

	/*
	Description:
	Removes an item within the specified list.

	Parameters:
	account_id: (string) 
	list:       (string) follow_source_users | follow_source_tags | follow_source_locations | whitelist_users
	data:       (array) items in the array change according to the list
		list = follow_source_users | whitelist_users
		['id', ...]

		list = follow_source_tags
		['tag', ...]
	*/
	public function remove_from_list() {
		$this->load->language('account/setting');

		$json = array('success' => false);

		if ($this->validateRemoveFromList()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$list       = (isset($this->request->post['list']) ? $this->request->post['list'] : $this->request->get['list']);
			$data       = $this->request->post['data'];

			$this->load->model('account/setting');

			$this->model_account_setting->editSetting($account_id, $list, $data, 'REMOVE');

			$json['success'] = true;
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateRemoveFromList() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}

			if (empty($this->request->post['list']) && empty($this->request->get['list'])) {
				$this->error['list'] = $this->language->get('error_list');
			}

			if (!isset($this->request->post['data']) || !is_array($this->request->post['data'])) {
				$this->error['data'] = $this->language->get('error_data');
			}
		}

		return !$this->error;
	}

	/*
	Description:
	Empties the specified list.

	Parameters
	----------
	account_id: (string) 
	list:       (string) follow_source_users | follow_source_tags | follow_source_locations | whitelist_users
	*/
	public function clear_list() {
		$this->load->language('account/setting');

		$json = array('success' => false);

		if ($this->validateClearList()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$list       = (isset($this->request->post['list']) ? $this->request->post['list'] : $this->request->get['list']);

			$this->load->model('account/setting');

			$this->model_account_setting->clearList($account_id, $list);

			$json['success'] = true;
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateClearList() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}

			if (empty($this->request->post['list']) && empty($this->request->get['list'])) {
				$this->error['list'] = $this->language->get('error_list');
			}
		}

		return !$this->error;
	}
}