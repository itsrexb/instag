<?php
class ControllerHeader extends Controller {
	public function index() {
		$data['title'] = $this->document->getTitle();
		$data['base']  = HTTP_SERVER;

		return $this->load->view('header.tpl', $data);
	}
}