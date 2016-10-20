<?php
class ControllerUpgrade extends Controller {
	public function index() {
		set_time_limit(0);

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('upgrade');

			$this->model_upgrade->upgrade();

			$this->response->redirect($this->url->link('upgrade/success'));
		}

		$this->document->setTitle($this->language->get('heading_upgrade'));

		$data['heading_upgrade']       = $this->language->get('heading_upgrade');
		$data['heading_upgrade_small'] = $this->language->get('heading_upgrade_small');

		$data['button_continue'] = $this->language->get('button_continue');

		$data['text_application'] = $this->language->get('text_application');
		$data['text_finished']  = $this->language->get('text_finished');
		$data['text_upgrade']   = $this->language->get('text_upgrade');
		$data['text_upgrade_1'] = $this->language->get('text_upgrade_1');
		$data['text_upgrade_2'] = $this->language->get('text_upgrade_2');
		$data['text_upgrade_3'] = $this->language->get('text_upgrade_3');

		$data['link_action'] = $this->url->link('upgrade');

		$data['header'] = $this->load->controller('header');
		$data['footer'] = $this->load->controller('footer');

		$this->response->setOutput($this->load->view('upgrade', $data));
	}

	public function success() {
		$this->document->setTitle($this->language->get('heading_success_upgrade'));

		$data['heading_success']       = $this->language->get('heading_success_upgrade');
		$data['heading_success_small'] = $this->language->get('heading_success_upgrade_small');

		$data['text_admin']       = $this->language->get('text_admin');
		$data['text_application'] = $this->language->get('text_application');
		$data['text_forget']      = $this->language->get('text_forget');
		$data['text_frontend']    = $this->language->get('text_frontend');

		$data['header'] = $this->load->controller('header');
		$data['footer'] = $this->load->controller('footer');

		$this->response->setOutput($this->load->view('success', $data));
	}
}