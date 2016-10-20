<?php
class ControllerAffiliateSuccess extends Controller {
	public function index() {
		$data = $this->load->language('affiliate/success');

		$this->document->setTitle($this->language->get('heading_title'));

		if (!$this->config->get('config_affiliate_approval')) {
			$data['text_message'] = sprintf($this->language->get('text_message'), $this->config->get('config_name'), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('affiliate/dashboard', '', true);

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}