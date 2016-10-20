<?php
class ControllerCsrFooter extends Controller {
	private $user;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->user = new Cart\User($registry);
	}

	public function index() {
		$this->load->language('csr/footer');

		$data['text_footer'] = sprintf($this->language->get('text_footer'), (date('Y') > 2015 ? '2015-' . date('Y') : date('Y')));

		if ($this->user->isLogged()) {
			$data['text_version'] = sprintf($this->language->get('text_version'), VERSION);
		} else {
			$data['text_version'] = '';
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

		return $this->load->view('csr/footer', $data);
	}
}