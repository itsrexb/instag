<?php
class ControllerCronGetSourceTotalsForAllAccounts extends Controller {
	public function index() {
		set_time_limit(0);

		if (isset($this->request->get['secret']) && $this->request->get['secret'] == $this->config->get('config_cron_secret')) {
			$dry_run = (isset($this->request->get['dry_run']) ? (bool)$this->request->get['dry_run'] : false);

			$this->load->model('cron/cron');

			$cron_data = array();

			// get last run cron information
			$cron = $this->model_cron_cron->getCron('get_source_totals_for_all_accounts');

			$result = $this->instaghive->cron->get_source_totals_for_all_accounts($dry_run);

			if ($dry_run) {
				$this->log->write($result);
			}

			$this->model_cron_cron->updateCron('get_source_totals_for_all_accounts', $cron_data);
		}
	}
}