<?php
class ControllerApiAccount extends Controller {
	private $error = array();

	/*
	Decription:
	Sends a notification to the customer based on the provided code and message.

	Parameters
	----------
	account_id: (string)
	code:       (string)
	message:    (string)

	RESPONSE
	--------
	success:  true|false
	error:    (array)
	*/
	public function worker_notification() {
		$this->load->language('api/account');

		$json = array('success' => false);

		if ($this->validate()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);
			$code       = (isset($this->request->post['code']) ? $this->request->post['code'] : $this->request->get['code']);

			if (isset($this->request->post['message'])) {
				$message = $this->request->post['message'];
			} else if (isset($this->request->get['message'])) {
				$message = $this->request->get['message'];
			} else {
				$message = '';
			}

			$this->load->model('api/account');

			$account_info = $this->model_api_account->getAccount($account_id);

			if ($account_info) {
				// account expired
				if ($code == 'stop' && $message == 'expired') {
					// update any enabled marketing extensions
					$this->load->model('extension/extension');
					$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

					foreach ($marketing_extensions as $marketing) {
						if ($this->config->get($marketing['code'] . '_status')) {
							$this->{$marketing['code']} = new $marketing['code']($this->registry);

							$this->load->model('extension/marketing/' . $marketing['code']);
							$this->{'model_extension_marketing_' . $marketing['code']}->updateCustomer($account_info['customer_id']);
						}
					}
				}

				//update status of the account since this function is only executed when the account is being stop
				$this->model_api_account->updateAccount($account_id,array('status' => 'stopped'));

				$mail_data = $this->language->all();

				$mail_data['text_footer'] = sprintf($this->language->get('text_footer'), date('Y'), $this->config->get('config_name'));

				$mail_data['href_home']      = $this->url->link('common/home');
				$mail_data['href_dashboard'] = $this->url->link('account/dashboard', '', true);

				$mail_data['logo']      = $this->config->get('config_url') . 'image/' . $this->config->get('config_email_logo');
				$mail_data['site_name'] = $this->config->get('config_name');
				$mail_data['firstname'] = $account_info['customer_firstname'];
				$mail_data['lastname']  = $account_info['customer_lastname'];

				if ($message && $this->language->get('message_' . $code . '_' . $message) != 'message_' . $code . '_' . $message) {
					$mail_data['message'] = sprintf($this->language->get('message_' . $code . '_' . $message), $account_info['username']);
				} else if ($this->language->get('message_' . $code) != 'message_' . $code) {
					$mail_data['message'] = sprintf($this->language->get('message_' . $code), $account_info['username']);
				} else {
					$mail_data['message'] = sprintf($this->language->get('message_default'), $account_info['username']);
				}

				if ($message && $this->language->get('subject_' . $code . '_' . $message) != 'subject_' . $code . '_' . $message) {
					$subject = $this->language->get('subject_' . $code . '_' . $message);
				} else if ($this->language->get('subject_' . $code) != 'subject_' . $code) {
					$subject = $this->language->get('subject_' . $code);
				} else {
					$subject = $this->language->get('subject_default');
				}

				$mail = new Mail();
				$mail->protocol      = $this->config->get('config_mail_protocol');
				$mail->parameter     = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($account_info['customer_email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($this->load->view('mail/worker_notification', $mail_data));
				$mail->setText($this->load->view('mail/worker_notification_text', array_map('strip_tags', $mail_data)));
				$mail->send();

				$json['success'] = true;
			}
		} else {
			$json['error'] = $this->error;
		}

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validate() {
		if (!isset($this->session->data['api_id'])) {
			$this->error[] = $this->language->get('error_permission');
		} else {
			if (!isset($this->request->post['account_id']) && !isset($this->request->get['account_id'])) {
				$this->error[] = $this->language->get('error_account_id');
			}

			if (!isset($this->request->post['code']) && !isset($this->request->get['code'])) {
				$this->error[] = $this->language->get('error_code');
			}
		}

		return !$this->error;
	}
}