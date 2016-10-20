<?php
class ControllerStep3 extends Controller {
	private $error = array();

	public function index() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('install');

			$this->model_install->database($this->request->post);

			$output_data = array();

			$output_data[] = "<?php";
			$output_data[] = "define('HTTP_SERVER', '" . HTTP_instag . "');";
			$output_data[] = "";
			$output_data[] = "define('HTTPS_SERVER', '" . HTTP_instag . "');";
			$output_data[] = "";
			$output_data[] = "define('DIR_APPLICATION', '" . DIR_instag . "catalog/');";
			$output_data[] = "define('DIR_SYSTEM', '" . DIR_instag . "system/');";
			$output_data[] = "define('DIR_LANGUAGE', '" . DIR_instag . "catalog/language/');";
			$output_data[] = "define('DIR_TEMPLATE', '" . DIR_instag . "catalog/view/theme/');";
			$output_data[] = "define('DIR_CONFIG', '" . DIR_instag . "system/config/');";
			$output_data[] = "define('DIR_IMAGE', '" . DIR_instag . "image/');";
			$output_data[] = "define('DIR_CACHE', '" . DIR_instag . "system/storage/cache/');";
			$output_data[] = "define('DIR_DOWNLOAD', '" . DIR_instag . "system/storage/download/');";
			$output_data[] = "define('DIR_LOGS', '" . DIR_instag . "system/storage/logs/');";
			$output_data[] = "define('DIR_UPLOAD', '" . DIR_instag . "system/storage/upload/');";
			$output_data[] = "";
			$output_data[] = "define('DB_DRIVER', '" . addslashes($this->request->post['db_driver']) . "');";
			$output_data[] = "define('DB_HOSTNAME', '" . addslashes($this->request->post['db_hostname']) . "');";
			$output_data[] = "define('DB_USERNAME', '" . addslashes($this->request->post['db_username']) . "');";
			$output_data[] = "define('DB_PASSWORD', '" . addslashes(html_entity_decode($this->request->post['db_password'], ENT_QUOTES, 'UTF-8')) . "');";
			$output_data[] = "define('DB_DATABASE', '" . addslashes($this->request->post['db_database']) . "');";
			$output_data[] = "define('DB_PORT', '" . addslashes($this->request->post['db_port']) . "');";
			$output_data[] = "define('DB_PREFIX', '" . addslashes($this->request->post['db_prefix']) . "');";

			$file = fopen(DIR_instag . 'config.php', 'w');

			fwrite($file, implode("\n", $output_data));

			fclose($file);

			$output_data = array();

			$output_data[] = "<?php";
			$output_data[] = "define('HTTP_SERVER', '" . HTTP_instag . "admin/');";
			$output_data[] = "define('HTTP_CATALOG', '" . HTTP_instag . "');";
			$output_data[] = "";
			$output_data[] = "define('HTTPS_SERVER', '" . HTTP_instag . "admin/');";
			$output_data[] = "define('HTTPS_CATALOG', '" . HTTP_instag . "');";
			$output_data[] = "";
			$output_data[] = "define('DIR_APPLICATION', '" . DIR_instag . "admin/');";
			$output_data[] = "define('DIR_SYSTEM', '" . DIR_instag . "system/');";
			$output_data[] = "define('DIR_LANGUAGE', '" . DIR_instag . "admin/language/');";
			$output_data[] = "define('DIR_TEMPLATE', '" . DIR_instag . "admin/view/template/');";
			$output_data[] = "define('DIR_CONFIG', '" . DIR_instag . "system/config/');";
			$output_data[] = "define('DIR_IMAGE', '" . DIR_instag . "image/');";
			$output_data[] = "define('DIR_CACHE', '" . DIR_instag . "system/storage/cache/');";
			$output_data[] = "define('DIR_DOWNLOAD','" . DIR_instag . "system/storage/download/');";
			$output_data[] = "define('DIR_LOGS', '" . DIR_instag . "system/storage/logs/');";
			$output_data[] = "define('DIR_UPLOAD', '" . DIR_instag . "system/storage/upload/');";
			$output_data[] = "define('DIR_CATALOG', '" . DIR_instag . "catalog/');";
			$output_data[] = "";
			$output_data[] = "define('DB_DRIVER', '" . addslashes($this->request->post['db_driver']) . "');";
			$output_data[] = "define('DB_HOSTNAME', '" . addslashes($this->request->post['db_hostname']) . "');";
			$output_data[] = "define('DB_USERNAME', '" . addslashes($this->request->post['db_username']) . "');";
			$output_data[] = "define('DB_PASSWORD', '" . addslashes(html_entity_decode($this->request->post['db_password'], ENT_QUOTES, 'UTF-8')) . "');";
			$output_data[] = "define('DB_DATABASE', '" . addslashes($this->request->post['db_database']) . "');";
			$output_data[] = "define('DB_PORT', '" . addslashes($this->request->post['db_port']) . "');";
			$output_data[] = "define('DB_PREFIX', '" . addslashes($this->request->post['db_prefix']) . "');";

			$file = fopen(DIR_instag . 'admin/config.php', 'w');

			fwrite($file, implode("\n", $output_data));

			fclose($file);

			$this->response->redirect($this->url->link('step_4'));
		}

		$this->document->setTitle($this->language->get('heading_step_3'));

		$data['heading_step_3'] = $this->language->get('heading_step_3');
		$data['heading_step_3_small'] = $this->language->get('heading_step_3_small');

		$data['text_application']        = $this->language->get('text_application');
		$data['text_configuration']      = $this->language->get('text_configuration');
		$data['text_config_admin_user']  = $this->language->get('text_config_admin_user');
		$data['text_config_database']    = $this->language->get('text_config_database');
		$data['text_config_instaghive']  = $this->language->get('text_config_instaghive');
		$data['text_config_instagram']   = $this->language->get('text_config_instagram');
		$data['text_finished']           = $this->language->get('text_finished');
		$data['text_installation']       = $this->language->get('text_installation');
		$data['text_license']            = $this->language->get('text_license');

		$data['entry_client_id']     = $this->language->get('entry_client_id');
		$data['entry_client_secret'] = $this->language->get('entry_client_secret');
		$data['entry_db_database']   = $this->language->get('entry_db_database');
		$data['entry_db_driver']     = $this->language->get('entry_db_driver');
		$data['entry_db_hostname']   = $this->language->get('entry_db_hostname');
		$data['entry_db_password']   = $this->language->get('entry_db_password');
		$data['entry_db_port']       = $this->language->get('entry_db_port');
		$data['entry_db_prefix']     = $this->language->get('entry_db_prefix');
		$data['entry_db_username']   = $this->language->get('entry_db_username');
		$data['entry_email']         = $this->language->get('entry_email');
		$data['entry_environment']   = $this->language->get('entry_environment');
		$data['entry_key']           = $this->language->get('entry_key');
		$data['entry_password']      = $this->language->get('entry_password');
		$data['entry_username']      = $this->language->get('entry_username');

		$data['button_back']     = $this->language->get('button_back');
		$data['button_continue'] = $this->language->get('button_continue');

		$data['link_action'] = $this->url->link('step_3');
		$data['link_back']   = $this->url->link('step_2');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['db_hostname'])) {
			$data['error_db_hostname'] = $this->error['db_hostname'];
		} else {
			$data['error_db_hostname'] = '';
		}
		
		if (isset($this->error['db_port'])) {
			$data['error_db_port'] = $this->error['db_port'];
		} else {
			$data['error_db_port'] = '';
		}

		if (isset($this->error['db_database'])) {
			$data['error_db_database'] = $this->error['db_database'];
		} else {
			$data['error_db_database'] = '';
		}

		if (isset($this->error['db_username'])) {
			$data['error_db_username'] = $this->error['db_username'];
		} else {
			$data['error_db_username'] = '';
		}
		
		if (isset($this->error['db_prefix'])) {
			$data['error_db_prefix'] = $this->error['db_prefix'];
		} else {
			$data['error_db_prefix'] = '';
		}

		if (isset($this->error['instaghive_environment'])) {
			$data['error_instaghive_environment'] = $this->error['instaghive_environment'];
		} else {
			$data['error_instaghive_environment'] = '';
		}

		if (isset($this->error['instaghive_key'])) {
			$data['error_instaghive_key'] = $this->error['instaghive_key'];
		} else {
			$data['error_instaghive_key'] = '';
		}

		if (isset($this->error['instagram_client_id'])) {
			$data['error_instagram_client_id'] = $this->error['instagram_client_id'];
		} else {
			$data['error_instagram_client_id'] = '';
		}

		if (isset($this->error['instagram_client_secret'])) {
			$data['error_instagram_client_secret'] = $this->error['instagram_client_secret'];
		} else {
			$data['error_instagram_client_secret'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->request->post['db_driver'])) {
			$data['db_driver'] = $this->request->post['db_driver'];
		} else {
			$data['db_driver'] = '';
		}

		if (isset($this->request->post['db_hostname'])) {
			$data['db_hostname'] = $this->request->post['db_hostname'];
		} else {
			$data['db_hostname'] = 'localhost';
		}
		
		if (isset($this->request->post['db_port'])) {
			$data['db_port'] = $this->request->post['db_port'];
		} else {
			$data['db_port'] = 3306;
		}

		if (isset($this->request->post['db_database'])) {
			$data['db_database'] = $this->request->post['db_database'];
		} else {
			$data['db_database'] = '';
		}

		if (isset($this->request->post['db_username'])) {
			$data['db_username'] = $this->request->post['db_username'];
		} else {
			$data['db_username'] = '';
		}

		if (isset($this->request->post['db_password'])) {
			$data['db_password'] = $this->request->post['db_password'];
		} else {
			$data['db_password'] = '';
		}
		
		if (isset($this->request->post['db_prefix'])) {
			$data['db_prefix'] = $this->request->post['db_prefix'];
		} else {
			$data['db_prefix'] = '';
		}

		if (isset($this->request->post['instaghive_environment'])) {
			$data['instaghive_environment'] = $this->request->post['instaghive_environment'];
		} else {
			$data['instaghive_environment'] = 'development';
		}

		$data['instaghive_environments'] = array(
			'development'	=> $this->language->get('text_development'),
			'production'	=> $this->language->get('text_production')
		);

		if (isset($this->request->post['instaghive_key'])) {
			$data['instaghive_key'] = $this->request->post['instaghive_key'];
		} else {
			$data['instaghive_key'] = '';
		}

		if (isset($this->request->post['instagram_client_id'])) {
			$data['instagram_client_id'] = $this->request->post['instagram_client_id'];
		} else {
			$data['instagram_client_id'] = '';
		}

		if (isset($this->request->post['instagram_client_secret'])) {
			$data['instagram_client_secret'] = $this->request->post['instagram_client_secret'];
		} else {
			$data['instagram_client_secret'] = '';
		}

		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		$data['db_drivers'] = array();

		if (extension_loaded('mysqli')) {
			$data['db_drivers']['mysqli'] = $this->language->get('text_mysqli');
		}

		if (extension_loaded('pdo')) {
			$data['db_drivers']['mpdo'] = $this->language->get('text_mpdo');
		}

		if (extension_loaded('pgsql')) {
			$data['db_drivers']['pgsql'] = $this->language->get('text_pgsql');
		}

		$data['header'] = $this->load->controller('header');
		$data['footer'] = $this->load->controller('footer');

		$this->response->setOutput($this->load->view('step_3', $data));
	}

	private function validate() {
		if (!$this->request->post['db_hostname']) {
			$this->error['db_hostname'] = $this->language->get('error_db_hostname');
		}

		if (!$this->request->post['db_port']) {
			$this->error['db_port'] = $this->language->get('error_db_port');
		}

		if (!$this->request->post['db_database']) {
			$this->error['db_database'] = $this->language->get('error_db_database');
		}

		if (!$this->request->post['db_username']) {
			$this->error['db_username'] = $this->language->get('error_db_username');
		}

		if ($this->request->post['db_prefix'] && preg_match('/[^a-z0-9_]/', $this->request->post['db_prefix'])) {
			$this->error['db_prefix'] = $this->language->get('error_db_prefix');
		}

		if ($this->request->post['db_driver'] == 'mysqli') {
			$mysql = @new mysqli($this->request->post['db_hostname'], $this->request->post['db_username'], html_entity_decode($this->request->post['db_password'], ENT_QUOTES, 'UTF-8'), $this->request->post['db_database'], $this->request->post['db_port']);

			if ($mysql->connect_error) {
				$this->error['warning'] = $mysql->connect_error;
			} else {
				$mysql->close();
			}
		} else if ($this->request->post['db_driver'] == 'mpdo') {
			try {
				new \DB\mPDO($this->request->post['db_hostname'], $this->request->post['db_username'], $this->request->post['db_password'], $this->request->post['db_database'], $this->request->post['db_port']);
			} catch(Exception $e) {
				$this->error['warning'] = $e->getMessage();
			}
		}

		if (!$this->request->post['instaghive_environment']) {
			$this->error['instaghive_environment'] = $this->language->get('error_instaghive_environment');
		}

		if (!$this->request->post['instaghive_key']) {
			$this->error['instaghive_key'] = $this->language->get('error_instaghive_key');
		}

		if (!$this->request->post['instagram_client_id']) {
			$this->error['instagram_client_id'] = $this->language->get('error_instagram_client_id');
		}

		if (!$this->request->post['instagram_client_secret']) {
			$this->error['instagram_client_secret'] = $this->language->get('error_instagram_client_secret');
		}

		if (!$this->request->post['username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (utf8_strlen($this->request->post['email']) > 96 || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!is_writable(DIR_instag . 'config.php')) {
			$this->error['warning'] = $this->language->get('error_config') . DIR_instag . 'config.php!';
		}

		if (!is_writable(DIR_instag . 'admin/config.php')) {
			$this->error['warning'] = $this->language->get('error_config') . DIR_instag . 'admin/config.php!';
		}

		return !$this->error;
	}
}