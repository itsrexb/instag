<?php
class ControllerAccountInstagram extends Controller {
	private $error = array();

	/*
	 * This will return account specific information
	 * @param $_GET $account_id 	
	*/
	public function account() {
		$data = $this->load->language('account/instagram');

		if ($this->customer->isLogged() && isset($this->request->get['account_id'])) {
			$this->load->model('account/account');

			$account_info = $this->model_account_account->getAccount($this->request->get['account_id']);

			if ($account_info) {
				if (!isset($account_info->StatusMessage)) {
					$account_info->StatusMessage = '';
				}

				if (empty($account_info->MetaData)) {
					$account_info->Status        = 'stopped';
					$account_info->StatusMessage = 'invalid_token';
				}
			}
		} else {
			$account_info = array();
		}

		if ($this->validateAccount($account_info)) {
			// account settings
			foreach ($account_info->settings as $key => $value) {
				if (is_array($value) && $value) {
					$data[$key] = array_reverse($value);
				} else {
					$data[$key] = $value;
				}
			}

			// add missing settings
			if (!isset($data['follow_source_tags'])) {
				$data['follow_source_tags'] = array();
			}

			if (!isset($data['follow_source_locations'])) {
				$data['follow_source_locations'] = array();
			}

			if (!isset($data['like_status'])) {
				$data['like_status'] = false;
			}

			if (!isset($data['unfollow_source'])) {
				$data['unfollow_source'] = 'all';
			}

			$data['account_id']       = $this->request->get['account_id'];
			$data['account_username'] = $account_info->Username;

			if (isset($account_info->StartDateTime)) {
				// sleep variables, simplify the ones from hive
				$data['sleep_time']     = $account_info->settings->sleep_start_min;
				$data['sleep_time_key'] = $account_info->settings->sleep_start_min;

				if ($account_info->settings->sleep_start_min <= $account_info->settings->sleep_end_min) {
					$data['sleep_duration'] = $account_info->settings->sleep_end_min - $account_info->settings->sleep_start_min;
				} else {
					$data['sleep_duration'] = ($account_info->settings->sleep_end_min + 24) - $account_info->settings->sleep_start_min;
				}

				$today = date('Y-m-d');

				$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

				$customer_timezone_offset = $customer_timezone->getOffset(new DateTime('now'));

				$data['sleep_hours'] = array();

				for ($i = 0; $i < 24; $i++) {
					if ($i < 10) {
						$datetime = new DateTime($today . '0' . $i . ':00:00');
					} else {
						$datetime = new DateTime($today . $i . ':00:00');
					}

					$datetime->setTimezone($customer_timezone);

					$data['sleep_hours'][$datetime->format('G')] = array(
						'utc'   => $i,
						'label' => $datetime->format('ga')
					);

					if ($data['sleep_time_key'] == $i) {
						$data['sleep_time_key'] = $datetime->format('G');
					}
				}

				ksort($data['sleep_hours']);

				// set expiration date to 5 days in the future if no expiration date is set
				if (!isset($account_info->ExpiresDateTime)) {
					// if an account has ever existed do not give a free trial
					if ($this->instaghive->instagram->already_existed($account_info->NetworkId)) {
						$this->instaghive->account->edit_expiration($this->request->get['account_id'], date('Y-m-d H:i:s'));

						$account_info->ExpiresDateTime = date('Y-m-d H:i:s');
						$account_info->StatusMessage   = 'expired';
					} else {
						$this->instaghive->account->update_expiration($this->request->get['account_id'], 5, 'day');

						$account_info->ExpiresDateTime = date('Y-m-d H:i:s', strtotime('+5 days'));
					}

					// update local cache with expiration date
					$this->model_account_account->setAccountExpirationDate($this->request->get['account_id'], $account_info->ExpiresDateTime);
				}

				// account has been started at least once, show normal dashboard
				$data['href_account_profile'] = 'https://instagram.com/' . $account_info->Username;

				$data['speeds'] = array(
					array(
						'code'   => 'slow',
						'name'   => $this->language->get('text_speed_slow'),
						'status' => false
					),
					array(
						'code'   => 'medium',
						'name'   => $this->language->get('text_speed_medium'),
						'status' => false
					),
					array(
						'code'   => 'fast',
						'name'   => $this->language->get('text_speed_fast'),
						'status' => false
					)
				);

				$this->load->model('account/capability');

				$account_capabilities = $this->model_account_capability->getAccountCapabilities($this->request->get['account_id']);

				// only show speeds available to this users plan

				$data['follow_unfollow_tab_class'] = '';

				foreach ($data['speeds'] as $key => $speed) {
					if (in_array('speed_' . $speed['code'], $account_capabilities)) {
						$data['speeds'][$key]['status'] = true;
					} else {
						$data['follow_unfollow_tab_class'] = 'upgrade';
					}
				}

				// is plan expired?
				if (strtotime($account_info->ExpiresDateTime) > strtotime('+5 days') || $account_info->StatusMessage == 'expired') {
					$data['account_plan_trial'] = false;
				} else {
					// plan is not expired, check to see if account is in free trial period
					$this->load->model('account/recurring_order');

					$last_recurring_order = $this->model_account_recurring_order->getLastRecurringOrder($this->request->get['account_id']);

					if (!$last_recurring_order) {
						// account is in free trial period, show countdown
						$data['account_plan_trial'] = true;

						$expires = new DateTime($account_info->ExpiresDateTime);
						$now     = new DateTime();

						$time_remaining = $now->diff($expires);

						$data['account_plan_trial_days_remaining']    = $time_remaining->format('%a');
						$data['account_plan_trial_hours_remaining']   = $time_remaining->format('%h');
						$data['account_plan_trial_minutes_remaining'] = $time_remaining->format('%I');

						if ($data['account_plan_trial_days_remaining'] > 1) {
							$data['text_free_trial_message'] = $this->language->get('text_free_trial_message_early');
						} else if ($data['account_plan_trial_days_remaining'] == 1) {
							$data['text_free_trial_message'] = $this->language->get('text_free_trial_message_48');
						} else if ($data['account_plan_trial_days_remaining'] < 1) {
							$data['text_free_trial_message'] = $this->language->get('text_free_trial_message_24');
						} else if ($account_info->StatusMessage == 'expired') {
							$data['text_free_trial_message'] = $this->language->get('text_free_trial_message_expired');
						}
					} else {
						$data['account_plan_trial'] = false;
					}
				}

				$data['account_info']   = $account_info;
				$data['account_status'] = $account_info->Status;

				if ($account_info->Status == 'started') {
					$data['account_action'] = 'stop';
					$data['account_flow']   = $this->language->get('text_flow_' . $account_info->Flow);
				} else {
					$data['account_action'] = 'start';

					if ($account_info->StatusMessage) {
						$data['account_flow'] = $this->language->get('text_flow_' . $account_info->Status . '_' . $account_info->StatusMessage);

						if ($account_info->StatusMessage == 'invalid_token') {
							$data['account_action'] = 'reconnect';
						} else if ($account_info->StatusMessage == 'expired') {
							$data['account_action'] = 'expired';
						}
					} else {
						$data['account_flow'] = $this->language->get('text_flow_' . $account_info->Status);
					}
				}

				$data['account_action_button'] = $this->language->get('button_' . $data['account_action']);

				$this->load->language('account/tooltip');

				if ($account_info->Status == 'started') {
					$data['account_tooltip'] = $this->language->get('instagram_tooltip_started_' . $account_info->Flow);
				} else {
					if (!empty($account_info->StatusMessage)) {
						$data['account_tooltip'] = $this->language->get('instagram_tooltip_' . $account_info->Status . '_' . $account_info->StatusMessage);
					} else {
						$data['account_tooltip'] = $this->language->get('instagram_tooltip_' . $account_info->Status);
					}
				}

				if (isset($account_info->MetaData['info']['profile_picture'])) {
					$data['profile_picture'] = $account_info->MetaData['info']['profile_picture'];
				} else {
					$data['profile_picture'] = '';
				}

				if (isset($account_info->MetaData['info']['counts'])) {
					$data['posts']     = $account_info->MetaData['info']['counts']['media'];
					$data['followers'] = $account_info->MetaData['info']['counts']['followed_by'];
					$data['follows']   = $account_info->MetaData['info']['counts']['follows'];
				} else {
					$data['posts']     = 0;
					$data['followers'] = 0;
					$data['follows']   = 0;
				}

				$data['recent_media'] = array();

				if (isset($account_info->MetaData['recent_media'])) {
					foreach ($account_info->MetaData['recent_media'] as $recent_media) {
						$data['recent_media'][] = $recent_media['images']['thumbnail']['url'];
					}
				}

				$data['events'] = array();

				$this->load->model('account/event');

				$event_data = $this->model_account_event->getEvents($this->request->get['account_id'], 20);

				foreach ($event_data as $event) {
					$event['date_added'] = date('m/d/Y H:i:s', strtotime($event['date_added']));

					$data['events'][] = $event;
				}

				$this->load->model('account/account_profile_history');
				$oldest_account_profile_history = $this->model_account_account_profile_history->getOldestAccountProfileHistory($this->request->get['account_id']);

				if ($oldest_account_profile_history) {
					$data['followers_oldest'] = (int)$oldest_account_profile_history['meta_data']['CountsFollowedBy'];

					$data['text_followers_as_of_date'] = sprintf($this->language->get('text_followers_as_of_date'), date($this->language->get('date_format_short'), strtotime($oldest_account_profile_history['date_added'])));
				} else {
					$data['followers_oldest'] = (int)$data['followers'];

					$data['text_followers_as_of_date'] = sprintf($this->language->get('text_followers_as_of_date'), date($this->language->get('date_format_short')));
				}

				$data['followers_current']    = (int)$data['followers'];
				$data['followers_difference'] = $data['followers_current'] - $data['followers_oldest'];

				if ($data['followers_oldest']) {
					$data['followers_pct_difference'] = abs(round((($data['followers_difference'] / $data['followers_oldest']) * 100), 2));
				} else {
					$data['followers_pct_difference'] = abs(round((($data['followers_difference'] / 1) * 100), 2));
				}

				if ($data['followers_pct_difference'] > 0) {
					$data['text_followers_comment'] = $this->language->get('text_followers_comment_positive');
				} else if ($data['followers_pct_difference'] < 0) {
					$data['text_followers_comment'] = $this->language->get('text_followers_comment_negative');
				} else {
					$data['text_followers_comment'] = $this->language->get('text_followers_comment_new');
				}

				$data['customer_timezone_offset'] = $customer_timezone->getOffset(new DateTime('now'));

				// check to see if this customer should not see the billing tab
				$this->load->model('customer/customer');
				$customer_info = $this->model_customer_customer->getCustomer($this->customer->getId());

				$this->user = new Cart\User($this->registry);

				$data['show_billing'] = ((!$customer_info['managed_billing'] || $this->user->isLogged()) ? true : false);

				$this->response->setOutput($this->load->view('account/instagram_account', $data));
			} else {
				// look in session for any selected source interests
				if (isset($this->session->data['selected_source_interest_ids']) && isset($this->session->data['selected_source_interest_ids'][$this->request->get['account_id']])) {
					$selected_source_interest_ids = $this->session->data['selected_source_interest_ids'][$this->request->get['account_id']];
				} else {
					$selected_source_interest_ids = array();
				}

				$data['source_interests']                = array();
				$data['total_selected_source_interests'] = 0;

				$this->load->model('catalog/source_interest');

				// get top level source interests
				$source_interest_data = $this->model_catalog_source_interest->getSourceInterests(0,$this->customer->getCountryId());

				foreach ($source_interest_data as $source_interest) {
					// get children
					$children = array();

					$children_data = $this->model_catalog_source_interest->getSourceInterests($source_interest['source_interest_id'],$this->customer->getCountryId());

					foreach ($children_data as $child) {
						if (in_array($child['source_interest_id'], $selected_source_interest_ids)) {
							$data['total_selected_source_interests']++;
						}

						$children[] = array(
							'source_interest_id' => $child['source_interest_id'],
							'name'               => $child['name'],
							'description'        => html_entity_decode($child['description'], ENT_QUOTES, 'UTF-8'),
							'selected'           => (in_array($child['source_interest_id'], $selected_source_interest_ids) ? true : false)
						);
					}

					$data['source_interests'][] = array(
						'source_interest_id' => $source_interest['source_interest_id'],
						'name'               => $source_interest['name'],
						'description'        => html_entity_decode($source_interest['description'], ENT_QUOTES, 'UTF-8'),
						'children'           => $children
					);
				}

				// if customer wants to skip whitelist for this account, make sure the template knows about it
				$data['skip_whitelist'] = (isset($this->session->data['kickoff_skip_whitelist'][$this->request->get['account_id']]) ? true : false);

				// account has never been started, show kickoff
				$this->response->setOutput($this->load->view('account/instagram_kickoff', $data));
			}
		} else {
			$data['heading_title'] = $this->language->get('error_account_not_found');

			$data['text_error'] = $this->language->get('error_account_not_found');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$this->response->setOutput($this->load->view('error/account_not_found', $data));
		}
	}

	private function validateAccount($account_info) {
		if (!checkRequestOrigin($this->request->server['HTTP_HOST'])) {
			$this->error['request_origin'] = $this->language->get('error_request_origin');
		} else if (!$account_info) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		return !$this->error;
	}

	public function search_users() {
		$this->load->language('account/instagram');

		$json = array();

		if ($this->validateSearchUsers()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$username   = (isset($this->request->post['username']) ? $this->request->post['username'] : $this->request->get['username']);

			$this->load->model('account/account');
			$account_info = $this->model_account_account->getAccountFromCache($account_id);

			$user_data = $this->instaghive->instagram->search_users($account_id, $username);

			foreach ($user_data as $key => $user) {
				if ($account_info && $user->username == $account_info['username']) {
					unset($user_data[$key]);

					$user_data = array_values($user_data);

					break;
				}
			}

			$json['users'] = $user_data;
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function search_tags() {
		$this->load->language('account/instagram');

		$json = array();

		if ($this->validateSearchTags()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$tag        = (isset($this->request->post['tag']) ? $this->request->post['tag'] : $this->request->get['tag']);

			$json['tags'] = $this->instaghive->instagram->search_tags($account_id, $tag);
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function search_locations() {
		$this->load->language('account/instagram');

		$json = array();

		if ($this->validateSearchLocations()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$query      = (isset($this->request->post['query']) ? $this->request->post['query'] : $this->request->get['query']);

			$json['locations'] = array();

			$location_data = $this->instaghive->instagram->search_locations($account_id, $query);

			foreach ($location_data as $location) {
				$json['locations'][] = array(
					'id'       => $location->location->pk,
					'name'     => $location->title,
					'subtitle' => $location->subtitle
				);
			}
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function import_whitelist_users() {
		$this->load->language('account/instagram');

		$json = array('success' => false);

		if ($this->validateImportWhitelistUsers()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			if (isset($this->request->post['limit'])) {
				$limit = $this->request->post['limit'];
			} else if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 0;
			}

			$whitelist_users = $this->instaghive->instagram->import_whitelist($account_id, $limit);

			if ($whitelist_users) {
				$json['success'] = true;
				$json['data']    = $whitelist_users;
			}
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateSearchUsers() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}

			if (empty($this->request->post['username']) && empty($this->request->get['username'])) {
				$this->error['username'] = $this->language->get('error_username');
			}
		}

		return !$this->error;
	}

	private function validateSearchTags() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}

			if (empty($this->request->post['tag']) && empty($this->request->get['tag'])) {
				$this->error['tag'] = $this->language->get('error_tag');
			}
		}

		return !$this->error;
	}

	private function validateSearchLocations() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}

			if (empty($this->request->post['query']) && empty($this->request->get['query'])) {
				$this->error['query'] = $this->language->get('error_query');
			}
		}

		return !$this->error;
	}

	private function validateImportWhitelistUsers() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}
		}

		return !$this->error;
	}


	///////////////////////////////////////////////////
	// USED FOR THE ACCOUNT AUTHORIZATION WITH OAUTH2.0
	//

	public function insert() {
		$this->load->language('account/instagram');

		$json = array('success' => false);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateInsert()) {
			$this->load->model('account/account');

			$result = $this->model_account_account->addAccount('instagram', $this->request->post['username'], $this->request->post['password']);

			if ($result->success) {
				$json['success']    = true;
				$json['account_id'] = $result->data;

				if (!empty($this->request->post['redirect']) || !empty($this->request->get['redirect'])) {
					$json['redirect'] = $this->url->link('account/dashboard', '', true);
				} else {
					$this->load->language('account/tooltip');

					$json['kickoff_tooltip'] = $this->language->get('instagram_tooltip_kickoff');

					$account_info = $this->model_account_account->getAccount($result->data);

					if (isset($account_info->MetaData['info']['profile_picture'])) {
						$json['profile_picture'] = $account_info->MetaData['info']['profile_picture'];
					} else {
						$json['profile_picture'] = '';
					}
				}
			}
		}

		if (!$json['success']) {
			$json['errors']['warning'] = $this->language->get('error_connect_account');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function reconnect() {
		$this->load->language('account/instagram');

		$json = array('success' => false);

		if ($this->customer->isLogged() && isset($this->request->get['account_id'])) {
			$this->load->model('account/account');

			$account_info = $this->model_account_account->getAccount($this->request->get['account_id']);
		} else {
			$account_info = array();
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateReconnect($account_info)) {
			$username = (!empty($this->request->post['username']) ? $this->request->post['username'] : $account_info->Username);

			$result = $this->model_account_account->reconnectAccount('instagram', $this->request->get['account_id'], $username, $this->request->post['password']);

			if ($result->success) {
				$this->load->language('account/event');
				$this->load->language('account/tooltip');

				$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

				$date_added = new DateTime('now');
				$date_added->setTimezone($customer_timezone);

				$json['success'] = true;

				$json['tooltip'] = $this->language->get('instagram_tooltip_stopped');

				$json['event'] = array(
					'code'        => 'reconnected',
					'message'     => '',
					'title'       => $this->language->get('event_title_reconnected'),
					'description' => $this->language->get('event_description_reconnected'),
					'date_added'  => $date_added->format('Y-m-d H:i:s')
				);
			}
		}

		if (!$json['success']) {
			$json['errors']['warning'] = $this->language->get('error_connect_account');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateInsert() {
		if (empty($this->request->post['username'])) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (empty($this->request->post['password'])) {
			$this->error['password'] = $this->language->get('error_password');
		}

		return !$this->error;
	}

	private function validateReconnect($account_info) {
		if (!$account_info) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		if (empty($this->request->post['password'])) {
			$this->error['password'] = $this->language->get('error_password');
		}

		return !$this->error;
	}
}