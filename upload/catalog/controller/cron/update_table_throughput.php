<?php
class ControllerCronUpdateTableThroughput extends Controller {
	public function increase() {
		set_time_limit(0);

		if (isset($this->request->get['secret']) && $this->request->get['secret'] == $this->config->get('config_cron_secret')) {
			$this->load->model('cron/cron');

			$cron_data = array();

			// get last run cron information
			$cron = $this->model_cron_cron->getCron('update_table_throughput_increase');

			$this->instaghive->cron->update_table_throughput('account_activity', 'AccountId-AddedDateTime-index', 200);
			$this->instaghive->cron->update_table_throughput('account_event_activity', '', 20);
			$this->instaghive->cron->update_table_throughput('account_source_total', '', '', 15);

			$this->model_cron_cron->updateCron('update_table_throughput_increase', $cron_data);
		}
	}

	public function decrease() {
		set_time_limit(0);

		if (isset($this->request->get['secret']) && $this->request->get['secret'] == $this->config->get('config_cron_secret')) {
			$this->load->model('cron/cron');

			$cron_data = array();

			// get last run cron information
			$cron = $this->model_cron_cron->getCron('update_table_throughput_decrease');

			$this->instaghive->cron->update_table_throughput('account_activity', 'AccountId-AddedDateTime-index', 1);
			$this->instaghive->cron->update_table_throughput('account_event_activity', '', 1);
			$this->instaghive->cron->update_table_throughput('account_source_total', '', '', 1);

			$this->model_cron_cron->updateCron('update_table_throughput_decrease', $cron_data);
		}
	}
}