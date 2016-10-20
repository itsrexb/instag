<?php
class ControllerExtensionMarketing extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->language('extension/marketing');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/extension');
	}

	public function index() {
		$this->getList();
	}

	public function install() {
		if ($this->validate()) {
			$this->model_extension_extension->install('marketing', $this->request->get['extension']);

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/marketing/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/marketing/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('extension/marketing/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = $this->language->get('success_install');

			$this->response->redirect($this->url->link('extension/marketing', 'token=' . $this->session->data['token'], true));
		}

		$this->getList();
	}

	public function uninstall() {
		if ($this->validate()) {
			$this->model_extension_extension->uninstall('marketing', $this->request->get['extension']);

			$this->load->model('setting/setting');
			$this->model_setting_setting->deleteSetting($this->request->get['extension']);

			// Call uninstall method if it exsits
			$this->load->controller('extension/marketing/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = $this->language->get('success_uninstall');

			$this->response->redirect($this->url->link('extension/marketing', 'token=' . $this->session->data['token'], true));
		}

		$this->getList();
	}

	public function getList() {
		$data = $this->language->all();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/marketing', 'token=' . $this->session->data['token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('extension/extension');

		$extensions = $this->model_extension_extension->getInstalled('marketing');

		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/extension/marketing/' . $value . '.php')) {
				$this->model_extension_extension->uninstall('marketing', $value);

				unset($extensions[$key]);
			}
		}

		$data['extensions'] = array();

		$files = glob(DIR_APPLICATION . 'controller/extension/marketing/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language('extension/marketing/' . $extension);

				$data['extensions'][] = array(
					'name'      => $this->language->get('heading_title'),
					'status'    => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'install'   => $this->url->link('extension/marketing/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
					'uninstall' => $this->url->link('extension/marketing/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
					'installed' => in_array($extension, $extensions),
					'edit'      => $this->url->link('extension/marketing/' . $extension . '', 'token=' . $this->session->data['token'], true)
				);
			}
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/marketing', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/marketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}