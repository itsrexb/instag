<?php
class ControllerAccountAccount extends Controller {
	private $error = array();

	public function start() {
		$this->load->language('account/account');

		$json = array('success' => false);

		if ($this->validateStart()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			// check to see if there's any seelected source interests for this account, if there are add them as sources
			if (!empty($this->session->data['selected_source_interest_ids'][$account_id])) {
				$this->load->model('catalog/source_interest');

				$settings_data = array(
					'follow_source_users' => array(),
					'follow_source_tags'  => array()
				);

				foreach ($this->session->data['selected_source_interest_ids'][$account_id] as $source_interest_id) {
					$source_interest_accounts = $this->model_catalog_source_interest->getSourceInterestAccounts($source_interest_id);
					$source_interest_tags     = $this->model_catalog_source_interest->getSourceInterestTags($source_interest_id);

					if ($source_interest_accounts) {
						shuffle($source_interest_accounts);

						$need_quality = array('high', 'medium', 'low');

                        // find one of each quality
						foreach ($source_interest_accounts as $key => $source_interest_account) {
							if (($quality_key = array_search($source_interest_account['quality'], $need_quality)) !== false) {
								$settings_data['follow_source_users'][] = array(
									'username' => $source_interest_account['account']
								);

								unset($source_interest_accounts[$key]);
								unset($need_quality[$quality_key]);

								if (!$need_quality) {
									break;
								}
							}
						}

						// if we still need a quality, fill it with random qualities
						if ($need_quality) {
							$source_interest_accounts = array_values($source_interest_accounts);

							for ($i = 0; $i < count($need_quality); $i++) {
								$settings_data['follow_source_users'][] = array(
									'username' => $source_interest_accounts[$i]['account']
								);
							}
						}
					}

					if ($source_interest_tags) {
						shuffle($source_interest_tags);

						$need_quality = array('high', 'medium', 'low');

						// find one of each quality
						foreach ($source_interest_tags as $key => $source_interest_tag) {
							if (($quality_key = array_search($source_interest_tag['quality'], $need_quality)) !== false) {
								$settings_data['follow_source_tags'][] = array(
									'tag' => $source_interest_tag['tag']
								);

								unset($source_interest_tags[$key]);
								unset($need_quality[$quality_key]);

								if (!$need_quality) {
									break;
								}
							}
						}

						// if we still need a quality, fill it with random qualities
						if ($need_quality) {
							$source_interest_tags = array_values($source_interest_tags);

							for ($i = 0; $i < count($need_quality); $i++) {
								$settings_data['follow_source_tags'][] = array(
									'tag' => $source_interest_tags[$i]['tag']
								);
							}
						}
					}

                    // add history of this source interest being used
                    $this->model_catalog_source_interest->addHistory($source_interest_id, $this->customer->getId(), $account_id);

				}

				if ($settings_data) {
					$this->load->model('account/setting');
					$this->model_account_setting->editSettings($account_id, $settings_data);
				}

				unset($this->session->data['selected_source_interest_ids'][$account_id]);
			}

			$this->load->model('account/account');

			$this->model_account_account->startAccount($account_id);

			$this->load->language('account/event');
			$this->load->language('account/tooltip');

			$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

			$date_added = new DateTime('now');
			$date_added->setTimezone($customer_timezone);

			$json['success'] = true;

			$account_type = $this->model_account_account->getAccountType($account_id);

			if ($account_type) {
				$json['tooltip'] = $this->language->get($account_type . '_tooltip_started');
			} else {
				$json['tooltip'] = '';
			}

			$json['event'] = array(
				'code'        => 'start',
				'message'     => '',
				'title'       => $this->language->get('event_title_start'),
				'description' => $this->language->get('event_description_start'),
				'date_added'  => $date_added->format('m-d-Y H:i:s')
			);
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function stop() {
		$this->load->language('account/account');

		$json = array('success' => false);

		if ($this->validateStop()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			$this->load->model('account/account');
			
			$this->model_account_account->stopAccount($account_id);

			$this->load->language('account/event');
			$this->load->language('account/tooltip');

			$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

			$date_added = new DateTime('now');
			$date_added->setTimezone($customer_timezone);

			$json['success'] = true;

			switch($this->model_account_account->getAccountType($account_id))
			{
			case 'instagram':
				$json['tooltip'] = $this->language->get('instagram_tooltip_stopped');
				break;
			default:
				$json['tooltip'] = '';
			}

			$json['event']   = array(
				'code'        => 'stop',
				'message'     => '',
				'title'       => $this->language->get('event_title_stop'),
				'description' => $this->language->get('event_description_stop'),
				'date_added'  => $date_added->format('m-d-Y H:i:s')
			);
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function flow() {
		$this->load->language('account/account');

		$json = array('success' => false);

		if ($this->validateFlow()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$flow       = (isset($this->request->post['flow']) ? $this->request->post['flow'] : $this->request->get['flow']);

			$this->load->model('account/account');
			
			$this->model_account_account->changeFlow($account_id, $flow);

			$this->load->language('account/event');

			$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

			$date_added = new DateTime('now');
			$date_added->setTimezone($customer_timezone);

			$json['success'] = true;
			$json['event']   = array(
				'code'        => 'start_' . strtolower($flow),
				'message'     => '',
				'title'       => $this->language->get('event_title_start_' . strtolower($flow)),
				'description' => $this->language->get('event_description_start_' . strtolower($flow)),
				'date_added'  => $date_added->format('m-d-Y H:i:s')
			);
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('account/account');

		$json = array('success' => false);

		if ($this->validateDelete()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			$this->load->model('account/account');
			$this->load->model('account/recurring_order');

			$this->model_account_account->deleteAccount($account_id);
			$this->model_account_recurring_order->cancelRecurringOrder($account_id);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing['code'] . '_status')) {
					$this->{$marketing['code']} = new $marketing['code']($this->registry);

					$this->load->model('extension/marketing/' . $marketing['code']);
					$this->{'model_extension_marketing_' . $marketing['code']}->updateCustomer($this->customer->getId());
				}
			}

			$json['success']  = true;
			$json['redirect'] = $this->url->link('account/dashboard', '', true);
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function cancel_plan() {
		$this->load->language('account/account');

		$json = array('success' => false);

		if ($this->validateCancelPlan()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			$this->load->model('account/recurring_order');

			//cancel if there's a recurring being attached to this plan
			$this->model_account_recurring_order->cancelRecurringOrder($account_id);

			$json['success']  = true;
			$json['redirect'] = $this->url->link('account/dashboard', '', true);

		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function source_interest() {
		$this->load->language('account/account');

		$json = array('success' => false);

		if ($this->validateSourceInterest()) {
			$account_id         = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$source_interest_id = (isset($this->request->post['source_interest_id']) ? $this->request->post['source_interest_id'] : $this->request->get['source_interest_id']);

			if (isset($this->request->post['selected'])) {
				$selected = $this->request->post['selected'];
			} else if (isset($this->request->get['selected'])) {
				$selected = $this->request->get['selected'];
			} else {
				$selected = true;
			}

			if (!isset($this->session->data['selected_source_interest_ids'][$account_id])) {
				$this->session->data['selected_source_interest_ids'][$account_id] = array();
			}

			// remove existing entry for this source_interest_id (to avoid duplicates if it gets added multiple times)
			foreach ($this->session->data['selected_source_interest_ids'][$account_id] as $key => $sid) {
				if ($sid == $source_interest_id) {
					unset($this->session->data['selected_source_interest_ids'][$account_id][$key]);
					break;
				}
			}

			if ($selected) {
				$this->session->data['selected_source_interest_ids'][$account_id][] = $source_interest_id;
			}


			$json['success'] = true;
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function skip_whitelist() {
		$this->load->language('account/account');

		$json = array('success' => false);

		if ($this->validateSkipWhitelist()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			if (isset($this->request->post['skip'])) {
				$skip = $this->request->post['skip'];
			} else if (isset($this->request->get['skip'])) {
				$skip = $this->request->get['skip'];
			} else {
				$skip = true;
			}

			if (!isset($this->session->data['kickoff_skip_whitelist'])) {
				$this->session->data['kickoff_skip_whitelist'] = array();
			}

			if ($skip) {
				$this->session->data['kickoff_skip_whitelist'][$account_id] = true;
			} else {
				if (isset($this->session->data['kickoff_skip_whitelist'][$account_id])) {
					unset($this->session->data['kickoff_skip_whitelist'][$account_id]);
				}
			}

			$json['success'] = true;
		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateStart() {
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

	private function validateStop() {
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

	private function validateFlow() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}

			if (empty($this->request->post['flow']) && empty($this->request->get['flow'])) {
				$this->error['flow'] = $this->language->get('error_flow');
			}
		}

		return !$this->error;
	}

	private function validateDelete() {
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

	private function validateCancelPlan() {
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

	private function validateSourceInterest() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}

			if (empty($this->request->post['source_interest_id']) && empty($this->request->get['source_interest_id'])) {
				$this->error['source_interest_id'] = $this->language->get('error_source_interest_id');
			}
		}

		return !$this->error;
	}

	private function validateSkipWhitelist() {
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
}