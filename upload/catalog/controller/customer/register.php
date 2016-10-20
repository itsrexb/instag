<?php
use GeoIp2\Database\Reader;

class ControllerCustomerRegister extends Controller {
	private $error = array();

	public function index() {
		if ($this->customer->isLogged()) {
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				$this->customer->logout();
				$this->instaghive->logout();
			} else {
				$this->response->redirect($this->url->link('account/dashboard', '', true));
			}
		}

		$data = $this->load->language('customer/register');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js', 'footer');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js', 'footer');

		$this->load->model('customer/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$customer_data = $this->request->post;

			// get country from ip address
			$reader = new Reader(DIR_SYSTEM . '/vendor/GeoLite2-Country.mmdb');

			try {
				$geoip_record = $reader->country($this->request->server['REMOTE_ADDR']);

				$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountryByCode($geoip_record->country->isoCode);
			} catch (GeoIp2\Exception\AddressNotFoundException $e) {
				$country_info = array();
			}

			if ($country_info) {
				$customer_data['country_id'] = $country_info['country_id'];
			} else {
				$customer_data['country_id'] = 0;
			}

			if (isset($this->request->cookie['ext_aff_id'])) {
				$customer_data['ext_aff_id'] = $this->request->cookie['ext_aff_id'];
			} else {
				$customer_data['ext_aff_id'] = '';
			}

			// Check to see if this customer came in via an affiliate or marketing link
			if (isset($this->request->cookie['tracking'])) {
				$customer_data['tracking'] = $this->request->cookie['tracking'];

				// Affiliate
				$this->load->model('affiliate/affiliate');

				$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$customer_data['affiliate_id'] = $affiliate_info['affiliate_id'];
					$customer_data['marketing_id'] = 0;
				} else {
					$customer_data['affiliate_id'] = 0;

					// Marketing
					$this->load->model('marketing/marketing');

					$marketing_info = $this->model_marketing_marketing->getMarketingByCode($this->request->cookie['tracking']);

					if ($marketing_info) {
						$customer_data['marketing_id'] = $marketing_info['marketing_id'];
					} else {
						$customer_data['marketing_id'] = 0;
					}
				}
			} else {
				$customer_data['tracking']     = '';
				$customer_data['affiliate_id'] = 0;
				$customer_data['marketing_id'] = 0;
			}

			if (!isset($customer_data['timezone'])) {
				$customer_data['timezone'] = $this->config->get('config_timezone');
			}

			// set language from browser
			$customer_data['language_code'] = $this->session->data['language'];

			$customer_id = $this->model_customer_customer->addCustomer($customer_data);

			// Clear any previous login attempts for unregistered customers.
			$this->model_customer_customer->deleteLoginAttempts($this->request->post['email']);

			$this->customer->login($this->request->post['email'], $this->request->post['password']);

			// Add to activity log
			$this->load->model('customer/activity');

			$activity_data = array(
				'customer_id' => $customer_id,
				'name'        => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
			);

			$this->model_customer_activity->addActivity('register', $activity_data);

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

			// append any get variables to the request just in case (google needed this for cross domain tracking)
			$get_args = array();

			foreach ($this->request->get as $key => $value) {
				if ($key != 'route' && $key != '_route_') {
					$get_args[$key] = $value;
				}
			}

			// set new customer session variable for the next page so it only displays conversion pixels once
			$this->session->data['new_customer'] = true;

			$this->response->redirect($this->url->link('customer/kickoff', http_build_query($get_args)));
		}

		$data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('customer/login'));

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

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
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

		$data['action'] = $this->url->link('customer/register', '', true);

		$data['customer_groups'] = array();

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('customer/customer_group');

			$customer_groups = $this->model_customer_customer_group->getCustomerGroups();

			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['timezone'])) {
			$data['timezone'] = $this->request->post['timezone'];
		} else {
			$data['timezone'] = $this->config->get('config_timezone');
		}

		// Custom Fields
		$this->load->model('customer/custom_field');

		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();

		if (isset($this->request->post['custom_field'])) {
			if (isset($this->request->post['custom_field']['account'])) {
				$account_custom_field = $this->request->post['custom_field']['account'];
			} else {
				$account_custom_field = array();
			}

			$data['register_custom_field'] = $account_custom_field;
		} else {
			$data['register_custom_field'] = array();
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
			$data['newsletter'] = '1';
		}

		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('captcha/' . $this->config->get('config_captcha'), array($this->error));
		} else {
			$data['captcha'] = '';
		}

		if ($this->config->get('config_account_terms_id') || $this->config->get('config_account_privacy_id')) {
			$this->load->model('catalog/information');

			$terms_info   = $this->model_catalog_information->getInformation($this->config->get('config_account_terms_id'));
			$privacy_info = $this->model_catalog_information->getInformation($this->config->get('config_account_privacy_id'));

			if ($terms_info && $privacy_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree_terms_privacy'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_terms_id'), true), $terms_info['title'], $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_privacy_id'), true), $privacy_info['title']);
			} else if ($terms_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree_terms'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_terms_id'), true), $terms_info['title']);
			} else if ($privacy_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree_privacy'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_privacy_id'), true), $privacy_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/register', $data));
	}

	private function validate() {
		if (!isset($this->request->post['firstname']) || (utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if (!isset($this->request->post['lastname']) || (utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (!isset($this->request->post['email']) || (utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		} else if ($this->model_customer_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if (!isset($this->request->post['telephone']) || (utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		// Customer Group
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		// Custom field validation
		$this->load->model('customer/custom_field');

		$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			} else if (($custom_field['type'] == 'text' && !empty($custom_field['validation'])) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field_validate'), $custom_field['name']);
			}
		}

		if (!isset($this->request->post['password']) || (utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!isset($this->request->post['confirm']) || $this->request->post['confirm'] != $this->request->post['password']) {
			$this->error['confirm'] = $this->language->get('error_confirm');
		}

		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		// Agree to terms
		if (($this->config->get('config_account_terms_id') && empty($this->request->post['agree_terms'])) || ($this->config->get('config_account_privacy_id') && empty($this->request->post['agree_privacy']))) {
			$this->load->model('catalog/information');

			$terms_info   = $this->model_catalog_information->getInformation($this->config->get('config_account_terms_id'));
			$privacy_info = $this->model_catalog_information->getInformation($this->config->get('config_account_privacy_id'));

			if ($terms_info && $privacy_info) {
				$this->error['warning'] = sprintf($this->language->get('error_agree_terms_privacy'), $terms_info['title'], $privacy_info['title']);
			} else if ($terms_info) {
				$this->error['warning'] = sprintf($this->language->get('error_agree_terms'), $terms_info['title']);
			} else if ($privacy_info) {
				$this->error['warning'] = sprintf($this->language->get('error_agree_privacy'), $privacy_info['title']);
			}
		}

		return !$this->error;
	}

	public function customfield() {
		$json = array();

		$this->load->model('customer/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}