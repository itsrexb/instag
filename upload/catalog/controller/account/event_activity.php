<?php 
class ControllerAccountEventActivity extends Controller {
	private $error = array();

	public function index() {
		$this->getList();
	}

	public function html() {
		$this->getList();
	}

	private function getList() {
		$data = $this->load->language('account/event_activity');

		$data['event_activities'] = array();

		if ($this->validateGetList()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			if (isset($this->request->post['limit'])) {
				$limit = $this->request->post['limit'];
			} else if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 12;
			}

			if (isset($this->request->post['last_evaluated_key'])) {
				$last_evaluated_key = $this->request->post['last_evaluated_key'];
			} else if (isset($this->request->get['last_evaluated_key'])) {
				$last_evaluated_key = $this->request->get['last_evaluated_key'];
			} else {
				$last_evaluated_key = '';
			}

			$this->load->model('account/event_activity');

			$event_activity_data = $this->model_account_event_activity->getEventActivities($account_id, 'follow', $limit, $last_evaluated_key);

			foreach ($event_activity_data['data'] as $event_activity) {
				switch ($event_activity->Code)
				{
				case 'follow':
					$event_activity->Description = sprintf($this->language->get('text_event_activity_description_follow'), $event_activity->Username);
					break;
				case 'unfollow':
					$event_activity->Description = sprintf($this->language->get('text_event_activity_description_unfollow'), $event_activity->Username);
					break;
				default:
					$event_activity->Description = $event_activity->Code;
				}

				$data['event_activities'][] = $event_activity;
			}

			$data['last_evaluated_key'] = $event_activity_data['last_evaluated_key'];
		} else if (isset($this->error['redirect'])) {
			$data['redirect'] = $this->error['redirect'];
		} else {
			$data['errors'] = $this->error;
		}

		$this->response->setOutput($this->load->view('account/event_activity', $data));
	}

	private function validateGetList() {
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