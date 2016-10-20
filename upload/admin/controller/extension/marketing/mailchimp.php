<?php
class ControllerExtensionMarketingMailchimp extends Controller {
	private $error = array();

	public function install() {
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'mailchimp_id'");

		if (!$results->row) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `mailchimp_id` varchar(32) NOT NULL");
		}
	}

	public function index() {
		$data = $this->load->language('extension/marketing/mailchimp');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			// create/update mailchimp store
			if (!empty($this->request->post['mailchimp_store_id'])) {
				$mailchimp = new MailChimp($this->registry, $this->request->post['mailchimp_api_key']);

				if ($this->request->post['mailchimp_store_id'] != $this->config->get('mailchimp_store_id')) {
					// new store
					$mailchimp->ecommerce->add_store(array(
						'id'            => $this->request->post['mailchimp_store_id'],
						'list_id'       => $this->request->post['mailchimp_customer_list_id'],
						'name'          => $this->config->get('config_name'),
						'email_address' => $this->config->get('config_email'),
						'currency_code' => $this->config->get('config_currency')
					));
				} else {
					// existing store
					$mailchimp->ecommerce->edit_store($this->request->post['mailchimp_store_id'], array(
						'id'            => $this->request->post['mailchimp_store_id'],
						'list_id'       => $this->request->post['mailchimp_customer_list_id'],
						'name'          => $this->config->get('config_name'),
						'email_address' => $this->config->get('config_email'),
						'currency_code' => $this->config->get('config_currency')
					));
				}
			}

			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('mailchimp', $this->request->post);

			$this->session->data['success'] = $this->language->get('success_update');

			if (!empty($this->request->post['mailchimp_api_key']) && !empty($this->request->post['mailchimp_customer_list_id']) && !empty($this->request->post['mailchimp_store_id']) && !empty($this->request->post['mailchimp_account_status_tag']) && !empty($this->request->post['mailchimp_country_tag']) && !empty($this->request->post['mailchimp_currency_tag']) && !empty($this->request->post['mailchimp_plan_tag'])) {
				$this->response->redirect($this->url->link('extension/marketing', 'token=' . $this->session->data['token'], true));
			}
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_marketing'),
			'href' => $this->url->link('extension/marketing', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/marketing/mailchimp', 'token=' . $this->session->data['token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['api_key'])) {
			$data['error_api_key'] = $this->error['api_key'];
		} else {
			$data['error_api_key'] = '';
		}

		if (isset($this->error['customer_list'])) {
			$data['error_customer_list'] = $this->error['customer_list'];
		} else {
			$data['error_customer_list'] = '';
		}

		$data['action'] = $this->url->link('extension/marketing/mailchimp', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/marketing', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['mailchimp_status'])) {
			$data['mailchimp_status'] = $this->request->post['mailchimp_status'];
		} else {
			$data['mailchimp_status'] = $this->config->get('mailchimp_status');
		}

		if (isset($this->request->post['mailchimp_debug'])) {
			$data['mailchimp_debug'] = $this->request->post['mailchimp_debug'];
		} else {
			$data['mailchimp_debug'] = $this->config->get('mailchimp_debug');
		}

		if (isset($this->request->post['mailchimp_api_key'])) {
			$data['mailchimp_api_key'] = $this->request->post['mailchimp_api_key'];
		} else {
			$data['mailchimp_api_key'] = $this->config->get('mailchimp_api_key');
		}

		if (isset($this->request->post['mailchimp_customer_list_id'])) {
			$data['mailchimp_customer_list_id'] = $this->request->post['mailchimp_customer_list_id'];
		} else {
			$data['mailchimp_customer_list_id'] = $this->config->get('mailchimp_customer_list_id');
		}

		if (isset($this->request->post['mailchimp_store_id'])) {
			$data['mailchimp_store_id'] = $this->request->post['mailchimp_store_id'];
		} else {
			$data['mailchimp_store_id'] = $this->config->get('mailchimp_store_id');
		}

		if (isset($this->request->post['mailchimp_account_status_tag'])) {
			$data['mailchimp_account_status_tag'] = $this->request->post['mailchimp_account_status_tag'];
		} else {
			$data['mailchimp_account_status_tag'] = $this->config->get('mailchimp_account_status_tag');
		}

		if (isset($this->request->post['mailchimp_country_tag'])) {
			$data['mailchimp_country_tag'] = $this->request->post['mailchimp_country_tag'];
		} else {
			$data['mailchimp_country_tag'] = $this->config->get('mailchimp_country_tag');
		}

		if (isset($this->request->post['mailchimp_currency_tag'])) {
			$data['mailchimp_currency_tag'] = $this->request->post['mailchimp_currency_tag'];
		} else {
			$data['mailchimp_currency_tag'] = $this->config->get('mailchimp_currency_tag');
		}

		if (isset($this->request->post['mailchimp_plan_tag'])) {
			$data['mailchimp_plan_tag'] = $this->request->post['mailchimp_plan_tag'];
		} else {
			$data['mailchimp_plan_tag'] = $this->config->get('mailchimp_plan_tag');
		}

		if ($data['mailchimp_api_key']) {
			$mailchimp = new MailChimp($this->registry, $data['mailchimp_api_key']);

			$data['mailchimp_lists'] = $mailchimp->lists->get_lists();

			if ($data['mailchimp_customer_list_id']) {
				$data['mailchimp_fields'] = $mailchimp->lists->get_fields($data['mailchimp_customer_list_id']);
			} else {
				$data['mailchimp_fields'] = array();
			}
		} else {
			$data['mailchimp_lists']  = array();
			$data['mailchimp_fields'] = array();
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/marketing/mailchimp', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/marketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['mailchimp_api_key'])) {
			$this->error['api_key'] = $this->language->get('error_api_key');
		}

		if ($this->request->post['mailchimp_api_key'] == $this->config->get('mailchimp_api_key')) {
			if (empty($this->request->post['mailchimp_customer_list_id'])) {
				$this->error['customer_list'] = $this->language->get('error_customer_list');
			}
		}

		return !$this->error;
	}
}