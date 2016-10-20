<?php
class ModelCronCron extends Model {
	public function updateCron($cron, $data = array()) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cron` WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND cron = '" . $this->db->escape($cron) . "'");

		$query = $this->db->query("INSERT INTO `" . DB_PREFIX . "cron` SET store_id = '" . (int)$this->config->get('config_store_id') . "', cron = '" . $this->db->escape($cron) . "', data = '" . $this->db->escape(json_encode($data)) . "', date_last_run = NOW()");
	}

	public function getCron($cron) {
		$query = $this->db->query("SELECT date_last_run FROM `" . DB_PREFIX . "cron` WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND cron = '" . $this->db->escape($cron) . "'");

		return $query->row;
	}
}