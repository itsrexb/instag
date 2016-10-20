<?php
use GeoIp2\Database\Reader;

class ControllerCustomerLogin extends Controller {
	private $error = array();

	public function index() {
		$this->load->model('customer/customer');

		// Login override for admin users
		if (!empty($this->request->get['token'])) {
			$this->customer->logout();
			$this->instaghive->logout();
			$this->cart->clear();

			unset($this->session->data['order_id']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

			$customer_info = $this->model_customer_customer->getCustomerByToken($this->request->get['token']);

			if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
				// update any enabled marketing extensions
				$this->load->model('extension/extension');
				$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

				foreach ($marketing_extensions as $marketing) {
					if ($this->config->get($marketing['code'] . '_status')) {
						$this->{$marketing['code']} = new $marketing['code']($this->registry);

						$this->load->model('extension/marketing/' . $marketing['code']);
						$this->{'model_extension_marketing_' . $marketing['code']}->customerLogin($this->customer->getId());
					}
				}

				// Update customer language
				$this->setLanguage($this->customer->getId());

				$this->response->redirect($this->url->link('account/dashboard', '', true));
			}
		}

		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/dashboard', '', true));
		}

		$data = $this->load->language('customer/login');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			// make sure they do not have an instag token
			$this->instaghive->logout();

			// Add to activity log
			$this->load->model('customer/activity');

			$activity_data = array(
				'customer_id' => $this->customer->getId(),
				'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
			);

			// Update customer language
			$this->setLanguage($this->customer->getId());

			// update customer if no country_id set
			if (!$this->customer->getCountryId()) {
				// get country from ip address
				$reader = new Reader(DIR_SYSTEM . '/vendor/GeoLite2-Country.mmdb');

				try {
					$geoip_record = $reader->country($this->request->server['REMOTE_ADDR']);

					$this->load->model('localisation/country');
					$country_info = $this->model_localisation_country->getCountryByCode($geoip_record->country->isoCode);

					if ($country_info) {
						$this->model_customer_customer->editCountryId($country_info['country_id']);
					}
				} catch (GeoIp2\Exception\AddressNotFoundException $e) {
					// do nothing
				}
			}

			$this->model_customer_activity->addActivity('login', $activity_data);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing['code'] . '_status')) {
					$this->{$marketing['code']} = new $marketing['code']($this->registry);

					$this->load->model('extension/marketing/' . $marketing['code']);
					$this->{'model_extension_marketing_' . $marketing['code']}->customerLogin($this->customer->getId());
				}
			}

			// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
			if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
				$this->response->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
			} else {
				$this->response->redirect($this->url->link('account/dashboard', '', true));
			}
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action']    = $this->url->link('customer/login', '', true);
		$data['forgotten'] = $this->url->link('customer/forgotten', '', true);
		$data['register']  = $this->url->link('customer/register', '', true);

		// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
		if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
			$data['redirect'] = $this->request->post['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
			$data['redirect'] = $this->session->data['redirect'];

			unset($this->session->data['redirect']);
		} else {
			$data['redirect'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/login', $data));
	}

	public function json() {
		$this->load->model('customer/customer');

		$json = array('success' => false);

		if ($this->customer->isLogged()) {
			$json['success']  = true;
			$json['redirect'] = $this->url->link('account/dashboard', '', true);
		} else {
			$this->load->language('customer/login');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				// make sure they do not have an instag token
				$this->instaghive->logout();

				// Add to activity log
				$this->load->model('customer/activity');

				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
				);

				$this->model_customer_activity->addActivity('login', $activity_data);

				// Update customer language
				$this->setLanguage($this->customer->getId());

				// update customer if no country_id set
				if (!$this->customer->getCountryId()) {
					// get country from ip address
					$reader = new Reader(DIR_SYSTEM . '/vendor/GeoLite2-Country.mmdb');

					try {
						$geoip_record = $reader->country($this->request->server['REMOTE_ADDR']);

						$this->load->model('localisation/country');
						$country_info = $this->model_localisation_country->getCountryByCode($geoip_record->country->isoCode);

						if ($country_info) {
							$this->model_customer_customer->editCountryId($country_info['country_id']);
						}
					} catch (GeoIp2\Exception\AddressNotFoundException $e) {
						// do nothing
					}
				}

				$json['success'] = true;

				// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
				if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
					$json['redirect'] = str_replace('&amp;', '&', $this->request->post['redirect']);
				} else {
					$json['redirect'] = $this->url->link('account/dashboard', '', true);
				}
			}
		}

		if (isset($this->error['warning'])) {
			$json['error_warning'] = $this->error['warning'];
		} else {
			$json['error_warning'] = '';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		// Check how many login attempts have been made.
		$login_info = $this->model_customer_customer->getLoginAttempts($this->request->post['email']);

		if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
			$this->error['warning'] = $this->language->get('error_attempts');
		}

		// Check if customer has been approved.
		$customer_info = $this->model_customer_customer->getCustomerByEmail($this->request->post['email']);

		if ($customer_info && !$customer_info['approved']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}

		if (!$this->error) {
			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
				$this->error['warning'] = $this->language->get('error_login');

				$this->model_customer_customer->addLoginAttempt($this->request->post['email']);
			} else {
				$this->model_customer_customer->deleteLoginAttempts($this->request->post['email']);
			}
		}

		return !$this->error;
	}

	private function setLanguage($customer_id) {
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		if (isset($languages[$this->customer->getLanguageCode()])) {
			$this->session->data['language'] = $this->customer->getLanguageCode();
		}
	}
}