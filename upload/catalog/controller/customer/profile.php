<?php
class ControllerCustomerProfile extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('customer/profile', '', true);

			$this->response->redirect($this->url->link('customer/login'));
		}

		$data = $this->load->language('customer/profile');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/javascript/chosen/chosen.bootstrap.min.css');
		$this->document->addScript('catalog/view/javascript/chosen/chosen.jquery.min.js', 'footer');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js', 'footer');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js', 'footer');

		// get installed marketing extensions
		$this->load->model('extension/extension');
		$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

		$this->load->model('customer/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_customer_customer->editCustomer($this->request->post);

			// Add to activity log
			$this->load->model('customer/activity');

			$activity_data = array(
				'customer_id' => $this->customer->getId(),
				'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
			);

			$this->model_customer_activity->addActivity('edit', $activity_data);

			if (!empty($this->request->post['password'])) {
				$this->model_customer_customer->editPassword($this->customer->getEmail(), $this->request->post['password']);

				// Add to activity log
				$this->model_customer_activity->addActivity('password', $activity_data);
			}

			// update any enabled marketing extensions
			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing['code'] . '_status')) {
					$this->{$marketing['code']} = new $marketing['code']($this->registry);

					$this->load->model('extension/marketing/' . $marketing['code']);
					$this->{'model_extension_marketing_' . $marketing['code']}->updateCustomer($this->customer->getId(), $this->request->post);
				}
			}

			// change language for customer
			if (!empty($this->request->post['language_code'])) {
				$this->session->data['language'] = $this->request->post['language_code'];
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('customer/profile', '', true));
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['custom_field'])) {
			$data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$data['error_custom_field'] = array();
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		$data['action'] = $this->url->link('customer/profile', '', true);

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else {
			$data['firstname'] = $this->customer->getFirstName();
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = $this->customer->getLastName();
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = $this->customer->getEmail();
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else {
			$data['telephone'] = $this->customer->getTelephone();
		}

		// Time Zone
		$data['timezones'] = array();

		$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

		foreach ($timezones as $timezone) {
			$timezone_data = explode('/', $timezone, 2);

			if (isset($timezone_data[1])) {
				$data['timezones'][] = array(
					'key'   => $timezone,
					'group' => $timezone_data[0],
					'label' => str_replace('_', ' ', $timezone)
				);
			}
		}

		if (isset($this->request->post['timezone'])) {
			$data['timezone'] = $this->request->post['timezone'];
		} else {
			$data['timezone'] = $this->customer->getTimeZone();
		}

		// Country
		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} else {
			$data['country_id'] = $this->customer->getCountryId();
		}

		// Language
		$data['languages'] = array();

		$this->load->model('localisation/language');
		$language_data = $this->model_localisation_language->getLanguages();

		foreach ($language_data as $key => $language) {
			$data['languages'][] = array(
				'language_id' => $language['language_id'],
				'code'        => $language['code'],
				'name'        => $language['name']
			);
		}

		if (isset($this->request->post['language_code'])) {
			$data['language_code'] = $this->request->post['language_code'];
		} else {
			$data['language_code'] = $this->customer->getLanguageCode();
		}

		// Currency
		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCustomerCurrencies();

		if (isset($this->request->post['currency_code'])) {
			$data['currency_code'] = $this->request->post['currency_code'];
		} else {
			$data['currency_code'] = $this->customer->getCurrencyCode();
		}

		// Custom Fields
		$this->load->model('customer/custom_field');

		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		if (isset($this->request->post['custom_field'])) {
			$data['account_custom_field'] = $this->request->post['custom_field'];
		} else if (isset($customer_info)) {
			$data['account_custom_field'] = json_decode($customer_info['custom_field'], true);
		} else {
			$data['account_custom_field'] = array();
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

		if (isset($this->request->post['newsletter'])) {
			$data['newsletter'] = $this->request->post['newsletter'];
		} else {
			$data['newsletter'] = $this->customer->getNewsletter();
		}

		// sync local customer with any installed marketing extensions
		foreach ($marketing_extensions as $marketing) {
			if ($this->config->get($marketing['code'] . '_status')) {
				$this->{$marketing['code']} = new $marketing['code']($this->registry);

				$this->load->model('extension/marketing/' . $marketing['code']);
				$this->{'model_extension_marketing_' . $marketing['code']}->localSync($this->customer->getId(), $data);
			}
		}

		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['footer']         = $this->load->controller('common/footer');
		$data['header']         = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('customer/profile', $data));
	}

	protected function validate() {
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (utf8_strlen($this->request->post['email']) > 96 || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_customer_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		// Custom field validation
		$this->load->model('customer/custom_field');

		$custom_fields = $this->model_customer_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if (($custom_field['location'] == 'account') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			}
		}

		if (!empty($this->request->post['password'])) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['confirm'] != $this->request->post['password']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		return !$this->error;
	}
}