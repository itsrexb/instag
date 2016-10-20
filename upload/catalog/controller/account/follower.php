<?php 
class ControllerAccountFollower extends Controller {
	
	private function getFollowerData(){

		$json = array();

		$json = $this->load->language('account/follower');

		if ($this->validateFollowers()) {
			
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			
			$date_start = (isset($this->request->post['date_start']) ? $this->request->post['date_start'] : $this->request->get['date_start']);
			$date_end = (isset($this->request->post['date_end']) ? $this->request->post['date_end'] : $this->request->get['date_end']);
		
			$organic = (isset($this->request->post['organic']) ? $this->request->post['organic'] : $this->request->get['organic']);

			if (isset($this->request->post['limit'])) {
				$limit = $this->request->post['limit'];
			} else if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 12;
			}

			$this->load->model('account/account');

			$json['followers'] = $this->model_account_account->getFollowers($account_id, $date_start, $date_end, 'follow', $limit);
			

		} else if (isset($this->error['redirect'])) {
			$json['redirect'] = $this->error['redirect'];
		} else {
			$json['errors'] = $this->error;
		}

		return $json;
	}
	public function html() {
		
		$data = array();
		$data = $this->getFollowerData();
		$this->response->setOutput($this->load->view('account/folower_html', $data));
	
	}

	public function json() {
		$json = $this->getFollowerData();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	
	}

	private function validateFollowers() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->error['redirect'] = $this->url->link('customer/login');
		} else {
			if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}
		}

		return !$this->error;
	}


}