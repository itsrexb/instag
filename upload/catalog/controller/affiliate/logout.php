<?php
class ControllerAffiliateLogout extends Controller {
	public function index() {
		if ($this->affiliate->isLogged()) {
			$this->affiliate->logout();

			$this->response->redirect($this->url->link('affiliate/logout', '', true));
		}

		$data = $this->load->language('affiliate/logout');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['continue'] = $this->url->link('common/home');

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}