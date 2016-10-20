<?php
namespace instagHive;
class Cron extends instagHiveAPI {
	private $route = 'admin/cron/';

	public function get_profile_for_all_accounts($dry_run = false) {
		$data = array(
			'dry_run' => (int)$dry_run,
		);

		$result = $this->client->request($this->route . 'get_profile_for_all_accounts', $data);

		return $result;
	}

	public function get_source_totals_for_all_accounts($dry_run = false) {
		$data = array(
			'dry_run' => (int)$dry_run,
		);

		$result = $this->client->request($this->route . 'get_source_totals_for_all_accounts', $data);

		return $result;
	}

	public function update_table_throughput($table, $index = '', $read = '', $write = '') {
		$data = array(
			'table' => $table,
			'index' => $index,
			'read'  => $read,
			'write' => $write,
		);

		$result = $this->client->request($this->route . 'update_table_throughput', $data);

		return $result;
	}
}