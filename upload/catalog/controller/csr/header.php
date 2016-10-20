<?php
class ControllerCsrHeader extends Controller {
	private $user;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->user = new Cart\User($registry);
	}

	public function index() {
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		// page specific css files
		if (isset($this->request->get['route'])) {
			$parts = explode('/', str_replace('../', '', $this->request->get['route']));

			// Break apart the route
			while ($parts) {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/dist/css/' . implode('/', $parts) . '.min.css')) {
					$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/dist/css/' . implode('/', $parts) . '.min.css');
					break;
				} else if (file_exists(DIR_TEMPLATE . 'default/dist/css/' . implode('/', $parts) . '.min.css')) {
					$this->document->addStyle('catalog/view/theme/default/dist/css/' . implode('/', $parts) . '.min.css');
					break;
				}

				array_pop($parts);
			}
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang']      = $this->language->get('code');
		$data['title']     = $this->document->getTitle();
		$data['name']      = $this->config->get('config_name');
		$data['base']      = $server;
		$data['links']     = $this->document->getLinks();
		$data['styles']    = $this->document->getStyles();
		$data['scripts']   = $this->document->getScripts();

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('csr/header');

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());
		$data['text_logout'] = $this->language->get('text_logout');

		if ($this->user->isLogged()) {
			$data['logged'] = true;

			$data['logout'] = $this->url->link('csr/logout', '', true);
		} else {
			$data['logged'] = false;
		}

		return $this->load->view('csr/header', $data);
	}
}