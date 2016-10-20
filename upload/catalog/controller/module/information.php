<?php
class ControllerModuleInformation extends Controller {
	public function index() {
		$data = $this->load->language('module/information');

		$data['informations'] = array();

		$this->load->model('catalog/information');

		foreach ($this->model_catalog_information->getInformations() as $result) {
			$data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
			);
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['sitemap'] = $this->url->link('information/sitemap');

		return $this->load->view('module/information', $data);
	}
}