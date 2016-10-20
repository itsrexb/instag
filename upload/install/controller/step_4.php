<?php
class ControllerStep4 extends Controller {
	public function index() {
		$this->document->setTitle($this->language->get('heading_success_install'));

		$data['heading_success']       = $this->language->get('heading_success_install');
		$data['heading_success_small'] = $this->language->get('heading_success_install_small');

		$data['text_admin']       = $this->language->get('text_admin');
		$data['text_application'] = $this->language->get('text_application');
		$data['text_forget']      = $this->language->get('text_forget');
		$data['text_frontend']    = $this->language->get('text_frontend');

		$data['header'] = $this->load->controller('header');
		$data['footer'] = $this->load->controller('footer');

		$this->response->setOutput($this->load->view('success', $data));
	}
}