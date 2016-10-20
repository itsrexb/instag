<?php
class ControllerModuleGoogleHangouts extends Controller {
	public function index() {
		$data = $this->load->language('module/google_hangouts');

		if ($this->request->server['HTTPS']) {
			$data['code'] = str_replace('http', 'https', html_entity_decode($this->config->get('google_hangouts_code')));
		} else {
			$data['code'] = html_entity_decode($this->config->get('google_hangouts_code'));
		}

		return $this->load->view('module/google_hangouts', $data);
	}
}