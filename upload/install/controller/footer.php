<?php
class ControllerFooter extends Controller {
	public function index() {
		$data['text_documentation'] = $this->language->get('text_documentation');
		$data['text_footer']        = $this->language->get('text_footer');
		$data['text_project']       = $this->language->get('text_project');

		$data['link_documentation'] = 'https://github.com/instagsocial/frontend';
		$data['link_project']       = 'https://instagsocial.com/';

		return $this->load->view('footer.tpl', $data);
	}
}