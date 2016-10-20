<?php
class ControllerAccountDashboard extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('customer/login'));
		}

		$this->load->model('account/account');

		$instagram_accounts = $this->model_account_account->getAccounts('instagram');

		// if this customer has no accounts, redirect to customer kickoff
		if (!$instagram_accounts) {
			$this->response->redirect($this->url->link('customer/kickoff'));
		}

		$this->load->language('account/dashboard');
		$this->load->language('account/tooltip');

		$data = $this->language->all();

		$this->document->setTitle($this->language->get('heading_title'));

		$data['link_logout']    = $this->url->link('customer/logout', '', true);
		$data['link_profile']   = $this->url->link('customer/profile', '', true);
		$data['link_help']      = 'https://instagsocialhelp.zendesk.com/hc/';

		$data['url_instagram_insert'] = $this->url->link('account/instagram/insert', '', true);

		$data['instagram_accounts'] = array();

		$usernames = array();

		foreach ($instagram_accounts as $account) {
			if (empty($account->MetaData)) {
				$account->Status        = 'stopped';
				$account->StatusMessage = 'invalid_token';
			}

			if (isset($account->StartDateTime)) {
				// determine what to set expiration date to
				if (!isset($account->ExpiresDateTime)) {
					// if an account has ever existed do not give a free trial
					if ($this->instaghive->instagram->already_existed($account->NetworkId)) {
						$this->instaghive->account->edit_expiration($account->Id, date('Y-m-d H:i:s'));

						$account->ExpiresDateTime = date('Y-m-d H:i:s');
						$account->StatusMessage   = 'expired';
					} else {
						// give free trial of 5 days (make configurable in the future)
						$this->instaghive->account->update_expiration($account->Id, 5, 'day');

						$account->ExpiresDateTime = date('Y-m-d H:i:s', strtotime('+5 days'));
					}

					// update local cache with expiration date
					$this->model_account_account->setAccountExpirationDate($account->Id, $account->ExpiresDateTime);
				}
			} else {
				// account is still in kickoff
				$account->Status = 'kickoff';
			}

			if ($account->Status == 'started') {
				$tooltip = $this->language->get('instagram_tooltip_started_' . $account->Flow);
			} else {
				if (!empty($account->StatusMessage)) {
					$tooltip = $this->language->get('instagram_tooltip_' . $account->Status . '_' . $account->StatusMessage);
				} else {
					$tooltip = $this->language->get('instagram_tooltip_' . $account->Status);
				}
			}

			$data['instagram_accounts'][] = array(
				'account_id'     => $account->Id,
				'username'       => $account->Username,
				'image'          => (isset($account->MetaData['info']['profile_picture']) ? $account->MetaData['info']['profile_picture'] : ''),
				'status'         => $account->Status,
				'status_message' => (isset($account->StatusMessage) ? $account->StatusMessage : ''),
				'tooltip'        => $tooltip
			);

			$usernames[] = $account->Username;
		}

		array_multisort($usernames, SORT_ASC, $data['instagram_accounts']);

		$data['customer_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/dist/js/account/libraries.min.js')) {
			$this->document->addScript('catalog/view/theme/' . $this->config->get('config_template') . '/dist/js/account/libraries.min.js', 'footer');
		} else {
			$this->document->addScript('catalog/view/theme/default/dist/js/account/libraries.min.js', 'footer');
		}

		$this->document->addScript('//ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js', 'footer');
		$this->document->addScript('//ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular-touch.min.js', 'footer');

		// go through all enabled payment methods and add their assets if they're available
		$this->load->model('extension/extension');
		$results = $this->model_extension_extension->getExtensions('payment');

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('payment/' . $result['code']);
				$this->{'model_payment_' . $result['code']}->addAssets();
			}
		}

		$data['header'] = $this->load->controller('account/header');
		$data['footer'] = $this->load->controller('account/footer');

		$this->response->setOutput($this->load->view('account/dashboard', $data));
	}
}