<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$data = $this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = ($this->request->server['HTTPS'] ? $this->config->get('config_ssl') : $this->config->get('config_url')) . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		// page specific js files
		if (isset($this->request->get['route'])) {
			$parts = explode('/', str_replace('../', '', $this->request->get['route']));

			// Break apart the route
			while ($parts) {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/dist/js/' . implode('/', $parts) . '.min.js')) {
					$this->document->addScript('catalog/view/theme/' . $this->config->get('config_template') . '/dist/js/' . implode('/', $parts) . '.min.js', 'footer');
					break;
				} else if (file_exists(DIR_TEMPLATE . 'default/dist/js/' . implode('/', $parts) . '.min.js')) {
					$this->document->addScript('catalog/view/theme/default/dist/js/' . implode('/', $parts) . '.min.js', 'footer');
					break;
				}

				array_pop($parts);
			}
		}

		$data['scripts'] = $this->document->getScripts('footer');

		return $this->load->view('common/footer', $data);
	}
}