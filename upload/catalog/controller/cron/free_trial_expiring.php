<?php
class ControllerCronFreeTrialExpiring extends Controller {
	public function index() {
		set_time_limit(0);

		if (isset($this->request->get['secret']) && $this->request->get['secret'] == $this->config->get('config_cron_secret')) {
			$this->load->model('cron/cron');
			$this->load->model('cron/free_trial_expiring');

			$cron_data = array();

			// get last run cron information
			$cron = $this->model_cron_cron->getCron('free_trial_expiring');

			if ($cron && $cron['date_last_run']) {
				$date_last_run = $cron['date_last_run'];
			} else {
				$date_last_run = date('Y-m-d H:i:s', strtotime('-1 hour'));
			}

			// time last run + x days
			$date_expires_start = date('Y-m-d H:i:s', strtotime('+' . $this->config->get('config_free_trial_expiring_days_before') . ' days', strtotime($date_last_run)));

			// now + x days
			$date_expires_end = date('Y-m-d H:i:s', strtotime('+' . $this->config->get('config_free_trial_expiring_days_before') . ' days'));

			$mail_data = $this->language->load('mail/free_trial_expiring');

			// setup common mail variables
			$mail_data['text_footer'] = sprintf($this->language->get('text_footer'), date('Y'), $this->config->get('config_name'));

			$mail_data['href_home']      = $this->url->link('common/home');
			$mail_data['href_dashboard'] = $this->url->link('account/dashboard', '', true);

			$mail_data['logo']      = $this->config->get('config_url') . 'image/' . $this->config->get('config_email_logo');
			$mail_data['site_name'] = $this->config->get('config_name');

			$mail = new Mail();
			$mail->protocol      = $this->config->get('config_mail_protocol');
			$mail->parameter     = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($mail_data['site_name'], ENT_QUOTES, 'UTF-8'));

			$results = $this->model_cron_free_trial_expiring->getFreeTrialExpiringAccounts($date_expires_start, $date_expires_end);

			foreach ($results as $result) {
				$mail_data['firstname'] = $result['firstname'];
				$mail_data['lastname']  = $result['lastname'];
				$mail_data['message']   = sprintf($this->language->get('message_default'), $result['username'] , $result['type']);

				$mail->setTo($result['email']);
				$mail->setSubject(html_entity_decode($this->language->get('subject_default'), ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($this->load->view('mail/free_trial_expiring', $mail_data));
				$mail->setText($this->load->view('mail/free_trial_expiring_text', array_map('strip_tags', $mail_data)));

				$mail->send();
			}

			$this->model_cron_cron->updateCron('free_trial_expiring', $cron_data);
		}
	}
}