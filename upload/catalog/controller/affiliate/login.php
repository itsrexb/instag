<?php
class ControllerAffiliateLogin extends Controller {
	private $error = array();

	public function index() {
		if ($this->affiliate->isLogged()) {
			$this->response->redirect($this->url->link('affiliate/dashboard', '', true));
		}

		$data = $this->load->language('affiliate/login');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['email']) && isset($this->request->post['password']) && $this->validate()) {
			// Add to activity log
			$this->load->model('affiliate/activity');

			$activity_data = array(
				'affiliate_id' => $this->affiliate->getId(),
				'name'         => $this->affiliate->getFirstName() . ' ' . $this->affiliate->getLastName()
			);

			$this->model_affiliate_activity->addActivity('login', $activity_data);

			// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
			if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
				$this->response->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
			} else {
				$this->response->redirect($this->url->link('affiliate/dashboard', '', true));
			}
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action']    = $this->url->link('affiliate/login', '', true);
		$data['forgotten'] = $this->url->link('affiliate/forgotten', '', true);
		$data['register']  = $this->url->link('affiliate/register', '', true);

		if (isset($this->request->post['redirect'])) {
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

		$this->response->setOutput($this->load->view('affiliate/login', $data));
	}

	protected function validate() {
		// Check how many login attempts have been made.
		$this->load->model('customer/customer');
		$login_info = $this->model_customer_customer->getLoginAttempts($this->request->post['email']);

		if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
			$this->error['warning'] = $this->language->get('error_attempts');
		}

		// Check if affiliate has been approved.
		$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByEmail($this->request->post['email']);

		if ($affiliate_info && !$affiliate_info['approved']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}

		if (!$this->error) {
			if (!$this->affiliate->login($this->request->post['email'], $this->request->post['password'])) {
				$this->error['warning'] = $this->language->get('error_login');

				$this->model_customer_customer->addLoginAttempt($this->request->post['email']);
			} else {
				$this->model_customer_customer->deleteLoginAttempts($this->request->post['email']);
			}
		}

		return !$this->error;
	}
}