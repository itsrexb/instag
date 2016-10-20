<?php
use Cart\User;

class ControllerCommonMaintenance extends Controller {
	public function index() {
		if ($this->config->get('config_maintenance')) {
			$route = '';

			if (isset($this->request->get['route'])) {
				$part = explode('/', $this->request->get['route']);

				if (isset($part[0])) {
					$route .= $part[0];
				}
			}

			// Show site if logged in as admin
			$this->user = new Cart\User($this->registry);

			if (($route != 'payment' && $route != 'api') && !$this->user->isLogged()) {
				return new Action('common/maintenance/info');
			}
		}
	}

	public function info() {
		$data = $this->load->language('common/maintenance');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
			$this->response->addHeader('HTTP/1.1 503 Service Unavailable');
		} else {
			$this->response->addHeader('HTTP/1.0 503 Service Unavailable');
		}

		$this->response->addHeader('Retry-After: 3600');
		$this->response->setOutput($this->load->view('common/maintenance', $data));
	}
}
