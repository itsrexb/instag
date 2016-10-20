<?php
class ControllerCronUpdateMarketingExtensions extends Controller {
	public function index() {
		set_time_limit(0);

		if (isset($this->request->get['secret']) && $this->request->get['secret'] == $this->config->get('config_cron_secret')) {
			$this->load->model('cron/cron');
			$this->load->model('cron/update_marketing_extensions');

			$cron_data = array();

			// get last run cron information
			$cron = $this->model_cron_cron->getCron('update_marketing_extensions');

			if ($cron && $cron['date_last_run']) {
				$date_last_run = $cron['date_last_run'];
			} else {
				$date_last_run = date('Y-m-d H:i:s', strtotime('-1 hour'));
			}

			// get installed marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

			// pre-load enabled marketing extensions
			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing['code'] . '_status')) {
					$this->{$marketing['code']} = new $marketing['code']($this->registry);

					$this->load->model('extension/marketing/' . $marketing['code']);
				}
			}

			// time last run
			$date_expires_start = date('Y-m-d H:i:s', strtotime($date_last_run));

			// now
			$date_expires_end = date('Y-m-d H:i:s');

			$results = $this->model_cron_update_marketing_extensions->getExpiredAccounts($date_expires_start, $date_expires_end);

			foreach ($results as $result) {
				// update any enabled marketing extensions
				foreach ($marketing_extensions as $marketing) {
					if ($this->config->get($marketing['code'] . '_status')) {
						$this->{'model_extension_marketing_' . $marketing['code']}->updateCustomer($result['customer_id']);
					}
				}
			}

			$this->model_cron_cron->updateCron('update_marketing_extensions', $cron_data);
		}
	}
}