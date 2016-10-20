<?php 
class ControllerAccountReportFollowerGrowth extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('account/report_follower_growth');

		if (isset($this->request->post['account_id'])) {
			$account_id = $this->request->post['account_id'];
		} else if (isset($this->request->get['account_id'])) {
			$account_id = $this->request->get['account_id'];
		} else {
			$account_id = '';
		}

		if ($account_id) {
			$this->load->model('account/account');
			$account_info = $this->model_account_account->getAccountFromCache($account_id);
		} else {
			$account_info = array();
		}

		if ($this->validate($account_info)) {
			$this->load->model('account/account_report');

			$data['report_follower_growth'] = array(
				'historical' => $this->model_account_account_report->getAccountReportFollowerGrowth($account_id, date('Y-m-d', strtotime('-3 months'))),
				'projected'  => array()
			);

			if ($data['report_follower_growth']['historical']) {
				$count_followed_by = (isset($account_info['meta_data']['info']['counts']) ? $account_info['meta_data']['info']['counts']['followed_by'] : 0);

				// have at least one day to compare against, so finish making the report
				$data['report_follower_growth']['historical'][] = array(
					'date'      => date('Y-m-d'),
					'followers' => $count_followed_by
				);

				$today = new DateTime();

				// loop through historical report data in reverse order, append to report data for projected
				foreach (array_reverse($data['report_follower_growth']['historical']) as $historical) {
					// find difference in days between today and historical date, use the difference to estimate the projected date
					$historical_date = new DateTime($historical['date']);

					$interval = $historical_date->diff($today);

					$projected_date = new DateTime('+' . $interval->days . ' days');

					$data['report_follower_growth']['projected'][] = array(
						'date'      => $projected_date->format('Y-m-d'),
						'followers' => $count_followed_by + ($count_followed_by - $historical['followers'])
					);
				}
			}

			$this->response->setOutput($this->load->view('account/report_follower_growth', $data));
		}
	}

	protected function validate($account_info) {
		if (!$account_info) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		return !$this->error;
	}
}