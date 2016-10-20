<?php
class ControllerCsrLogout extends Controller {
	private $user;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->user = new Cart\User($registry);
	}

	public function index() {
		$this->user->logout();

		unset($this->session->data['token']);

		$this->response->redirect($this->url->link('csr/login', '', true));
	}
}