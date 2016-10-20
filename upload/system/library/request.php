<?php
class Request {
	public $get    = array();
	public $post   = array();
	public $cookie = array();
	public $files  = array();
	public $server = array();

	public function __construct() {
		$this->get     = $this->clean($_GET);
		$this->post    = $this->clean($_POST);
		$this->request = $this->clean($_REQUEST);
		$this->cookie  = $this->clean($_COOKIE);
		$this->files   = $this->clean($_FILES);
		$this->server  = $this->clean($_SERVER);

		if (isset($this->server['REQUEST_METHOD']) && $this->server['REQUEST_METHOD'] == 'POST' && isset($this->server['CONTENT_TYPE']) && strpos($this->server['CONTENT_TYPE'], 'application/json') !== false) {
			$this->post = json_decode(file_get_contents('php://input'), true);
		}
	}

	public function clean($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$data[$this->clean($key)] = $this->clean($value);
			}
		} else {
			$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		}

		return $data;
	}
}