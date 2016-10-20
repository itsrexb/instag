<?php 
class ControllerAccountEvent extends Controller {
	public function get_list() {
		$this->load->language('account/event');

		if (isset($this->request->post['account_id'])) {
			$account_id = $this->request->post['account_id'];
		} else if (isset($this->request->get['account_id'])) {
			$account_id = $this->request->get['account_id'];
		} else {
			$account_id = '';
		}

		if (isset($this->request->post['limit'])) {
			$limit = $this->request->post['limit'];
		} else if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 15;
		}

		$data['events'] = array();

		if ($this->customer->isLogged() && $account_id) {
			$this->load->model('account/event');

			$data['events'] = $this->model_account_event->getEvents($account_id, $limit);
		}

		$this->response->setOutput($this->load->view('account/event', $data));
	}

	//TODO: Needs pagination
	public function json() {
		$this->load->language('account/event');

		if (isset($this->request->post['account_id'])) {
			$account_id = $this->request->post['account_id'];
		} else if (isset($this->request->get['account_id'])) {
			$account_id = $this->request->get['account_id'];
		} else {
			$account_id = '';
		}

		/*if (isset($this->request->post['limit'])) {
			$limit = $this->request->post['limit'];
		} else if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 15;
		}*/
		//Limit is removed since using datatables
		$limit = 0;

		$data['data'] = array();
		
		if ($this->customer->isLogged() && $account_id) {
			$this->load->model('account/event');
			$events = $this->model_account_event->getEvents($account_id, $limit);

			foreach ($events as $event) {
				$data['data'][] = array(
					$event['title'],
					$event['description'],
					$event['date_added']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
}