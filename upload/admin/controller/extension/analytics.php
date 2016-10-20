<?php
class ControllerExtensionAnalytics extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/analytics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/extension');

		$this->getList();
	}

	public function install() {
		$this->load->language('extension/analytics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/extension');

		if ($this->validate()) {
			$this->model_extension_extension->install('analytics', $this->request->get['extension']);

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'analytics/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'analytics/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('analytics/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = $this->language->get('success_install');

			$this->response->redirect($this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true));
		}

		$this->getList();
	}

	public function uninstall() {
		$this->load->language('extension/analytics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/extension');

		if ($this->validate()) {
			$this->model_extension_extension->uninstall('analytics', $this->request->get['extension']);

			$this->load->model('setting/setting');

			$this->model_setting_setting->deleteSetting($this->request->get['extension']);

			// Call uninstall method if it exsits
			$this->load->controller('analytics/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = $this->language->get('success_uninstall');

			$this->response->redirect($this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true));
		}
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
			'href' => $this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true)
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

		$extensions = $this->model_extension_extension->getInstalled('analytics');

		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/analytics/' . $value . '.php')) {
				$this->model_extension_extension->uninstall('analytics', $value);

				unset($extensions[$key]);
			}
		}

		$data['extensions'] = array();

		$files = glob(DIR_APPLICATION . 'controller/analytics/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language('analytics/' . $extension);

				$data['extensions'][] = array(
					'name'      => $this->language->get('heading_title'),
					'status'    => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'install'   => $this->url->link('extension/analytics/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
					'uninstall' => $this->url->link('extension/analytics/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
					'installed' => in_array($extension, $extensions),
					'edit'      => $this->url->link('analytics/' . $extension, 'token=' . $this->session->data['token'], true)
				);
			}
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/analytics', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/analytics')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}