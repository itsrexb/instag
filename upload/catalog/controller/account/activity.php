<?php 
class ControllerAccountActivity extends Controller {
	private $error = array();

	public function index() {
		$this->getList();
	}

	public function html() {
		$this->getList();
	}

	private function getList() {
		$data = $this->load->language('account/activity');

		$data['activities'] = array();

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

			$this->load->model('account/activity');

			$activity_data = $this->model_account_activity->getActivities($account_id, '', $limit, $last_evaluated_key);

			foreach ($activity_data['data'] as $activity) {
				switch ($activity->Activity)
				{
				case 'follow':
					$activity->Description = sprintf($this->language->get('text_activity_description_follow'), $activity->Username);
					break;
				case 'unfollow':
					$activity->Description = sprintf($this->language->get('text_activity_description_unfollow'), $activity->Username);
					break;
				case 'like':
					if (!empty($activity->MediaLink)) {
						$activity->Description = sprintf($this->language->get('text_activity_description_like_link'), $activity->MediaLink, $activity->Username);
					} else {
						$activity->Description = sprintf($this->language->get('text_activity_description_like_no_link'), $activity->Username);
					}
					break;
				default:
					$activity->Description = $activity->Activity;
				}

				$data['activities'][] = $activity;
			}

			$data['last_evaluated_key'] = $activity_data['last_evaluated_key'];
		} else if (isset($this->error['redirect'])) {
			$data['redirect'] = $this->error['redirect'];
		} else {
			$data['errors'] = $this->error;
		}

		$this->response->setOutput($this->load->view('account/activity', $data));
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