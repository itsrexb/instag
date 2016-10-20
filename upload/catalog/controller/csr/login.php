<?php
class ControllerCsrLogin extends Controller {
	private $user;
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);

		$this->user = new Cart\User($registry);
	}

	public function index() {
		if ($this->user->isLogged()) {
			$this->response->redirect($this->url->link('csr/checkout', '', true));
		}

		if ($this->config->get('config_url') != $this->config->get('config_ssl') && !$this->request->server['HTTPS']) {
			$this->response->redirect($this->url->link('csr/login', '', true));
		}

		$data = $this->load->language('csr/login');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->response->redirect($this->url->link('csr/checkout', '', true));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['action'] = $this->url->link('csr/login', '', true);

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

		$data['header'] = $this->load->controller('csr/header');
		$data['footer'] = $this->load->controller('csr/footer');

		$this->response->setOutput($this->load->view('csr/login', $data));
	}

	protected function validate() {
		if (!isset($this->request->post['username']) || !isset($this->request->post['password']) || !$this->user->login($this->request->post['username'], $this->request->post['password'])) {
			$this->error['warning'] = $this->language->get('error_login');
		}

		return !$this->error;
	}
}