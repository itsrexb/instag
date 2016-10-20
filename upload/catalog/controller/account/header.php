<?php
class ControllerAccountHeader extends Controller {
	public function index() {
		$data = $this->load->language('account/header');

		$data['title'] = $this->document->getTitle();

		$this->document->addStyle('//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
		$this->document->addStyle('catalog/view/theme/default/dist/css/shared.min.css');
		$this->document->addStyle('catalog/view/javascript/chosen/chosen.bootstrap.min.css');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		// if a link has more than one url to it, use the proper canonical link
		$has_canonical = false;

		$links = $this->document->getLinks();

		foreach ($links as $link) {
			if ($link['rel'] == 'canonical') {
				$has_canonical = true;
				break;
			}
		}

		if (!$has_canonical) {
			if (isset($this->request->get['route'])) {
				$route = (string)$this->request->get['route'];
			} else {
				$route = 'common/home';
			}

			if (strpos($route, 'account/') || strpos($route, 'affiliate/') || strpos($route, 'checkout/')) {
				$use_ssl = true;
			} else {
				$use_ssl = false;
			}

			$this->document->addLink($this->url->link($route, '', $use_ssl), 'canonical');
		}

		// Analytics
		$this->load->model('extension/extension');

		$data['analytics'] = array();

		$analytics = $this->model_extension_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get($analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('analytics/' . $analytic['code']);
			}
		}

		if (!isset($this->request->get['route'])) {
			$this->request->get['route'] = 'common/home';
		}

		// page specific css files
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

		$data['base']        = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords']    = $this->document->getKeywords();
		$data['links']       = $this->document->getLinks();
		$data['styles']      = $this->document->getStyles();
		$data['scripts']     = $this->document->getScripts();
		$data['lang']        = $this->language->get('code');
		$data['direction']   = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$data['link_dashboard'] = $this->url->link('account/dashboard', '', true);
		$data['link_home']      = $this->url->link('common/home');
		$data['link_login']     = $this->url->link('customer/login', '', true);
		$data['link_logout']    = $this->url->link('customer/logout', '', true);
		$data['link_order']     = $this->url->link('customer/order', '', true);
		$data['link_profile']   = $this->url->link('customer/profile', '', true);
		$data['link_register']  = $this->url->link('customer/register', '', true);

		$data['email']  = $this->customer->getEmail();
		$data['logged'] = $this->customer->isLogged();

		$status = true;

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", str_replace(array("\r\n", "\r"), "\n", trim($this->config->get('config_robots'))));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}

		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');

		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']);

			if ($class) {
				$data['class'] = ' ' . str_replace('/', '-', $this->request->get['route']) . $class;
			}
		} else {
			$data['class'] = 'common-home';
		}

		return $this->load->view('account/header', $data);
	}
}