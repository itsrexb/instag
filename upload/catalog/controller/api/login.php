<?php
class ControllerApiLogin extends Controller {
	public function index() {
		$this->load->language('api/login');

		$json = array('success' => false);

		$this->load->model('customer/api');

		// Login with API Key
		$api_info = $this->model_customer_api->getApiByKey($this->request->post['key']);

		if ($api_info) {
			$session_name = 'temp_session_' . uniqid();

			$session = new Session();
			$session->start($this->session->getId(), $session_name);

			// Set API ID
			$session->data['api_id'] = $api_info['api_id'];

			// Create Token
			$json['token'] = $this->model_customer_api->addApiSession($api_info['api_id'], $session_name, $session->getId(), $this->request->server['REMOTE_ADDR']);

			$json['success'] = true;
		} else {
			$json['error']['key'] = $this->language->get('error_key');
		}

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}