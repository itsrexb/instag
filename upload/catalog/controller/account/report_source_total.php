<?php 
class ControllerAccountReportSourceTotal extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('account/report_source_total');

		if ($this->validate()) {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			if (isset($this->request->post['date_start'])) {
				$date_start = $this->request->post['date_start'];
			} else if (isset($this->request->get['date_start'])) {
				$date_start = $this->request->get['date_start'];
			} else {
				$date_start = '-30 days';
			}

			$total_actions     = 0;
			$total_commenters  = 0;
			$total_likers      = 0;
			$total_followers   = 0;
			$total_followbacks = 0;

			$data['report_source_total'] = array();

			$this->load->model('account/account_report');

			$report_source_total_data = $this->model_account_account_report->getAccountReportSourceTotals($account_id, date('Y-m-d', strtotime($date_start)));

			if (!$report_source_total_data && $date_start == '-1 day') {
				$report_source_total_data = $this->model_account_account_report->getAccountReportSourceTotals($account_id, date('Y-m-d', strtotime('-2 days')));
			}

			$data['full_report'] = $report_source_total_data;

			foreach ($report_source_total_data as $report_source_total) {
				if ($report_source_total['type'] == 'unfollow') {
					$actions     = $report_source_total['actions']['unfollows'];
					$commenters  = 0;
					$likers      = 0;
					$followers   = 0;
					$followbacks = 0;
					$ratio       = '';
				} else {
					$actions     = (isset($report_source_total['actions']['follows']) ? $report_source_total['actions']['follows'] : 0);
					$followbacks = (isset($report_source_total['activity']['follows']) ? $report_source_total['activity']['follows'] : 0);

					if ($report_source_total['type'] == 'user') {
						$commenters = $report_source_total['activity']['commenters'];
						$likers     = $report_source_total['activity']['likers'];
						$followers  = $report_source_total['activity']['followers'];
					} else {
						$commenters = 0;
						$likers     = 0;
						$followers  = 0;
					}

					if ($report_source_total['type'] == 'unknown' || !$actions) {
						$ratio = '';
					} else if (!$followbacks) {
						$ratio = 0;
					} else {
						$ratio = round(($followbacks / $actions * 100), 2);
					}
				}

				if ($report_source_total['type'] == 'unknown' || $report_source_total['type'] == 'unfollow') {
					$name = $this->language->get('text_' . $report_source_total['type']);
				} else {
					$name = $report_source_total['name'];
				}

				$data['report_source_total'][] = array(
					'source'      => $report_source_total['source'],
					'name'        => $name,
					'type'        => $report_source_total['type'],
					'actions'     => $actions,
					'commenters'  => ($commenters ? $commenters : '-'),
					'likers'      => ($likers ? $likers : '-'),
					'followers'   => ($followers ? $followers : '-'),
					'followbacks' => $followbacks,
					'ratio'       => ($ratio !== '' ? $ratio . '%' : ''),
					'ratio_val'   => $ratio
				);

				if ($report_source_total['type'] != 'unknown' && $report_source_total['type'] != 'unfollow') {
					$total_actions    += $actions;
					$total_commenters += $commenters;
					$total_likers     += $likers;
					$total_followers  += $followers;
				}

				$total_followbacks += $followbacks;
			}

			// sort best source -> worst source
			usort($data['report_source_total'], array($this, 'sort'));

			if (!$total_actions) {
				$total_ratio = '';
			} else if (!$total_followers) {
				$total_ratio = 0;
			} else {
				$total_ratio = round(($total_followers / $total_actions * 100), 2);
			}

			$data['report_source_total'][] = array(
				'source'      => 'total',
				'name'        => $this->language->get('text_total'),
				'type'        => 'total',
				'actions'     => $total_actions,
				'commenters'  => $total_commenters,
				'likers'      => $total_likers,
				'followers'   => $total_followers,
				'followbacks' => $total_followbacks,
				'ratio'       => ($total_ratio !== '' ? $total_ratio . '%' : ''),
				'ratio_val'   => $total_ratio
			);

			$this->response->setOutput($this->load->view('account/report_source_total', $data));
		}
	}

	protected function validate() {
		if (empty($this->request->post['account_id']) && empty($this->request->get['account_id'])) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		} else {
			$account_id = (isset($this->request->post['account_id']) ? $this->request->post['account_id'] : $this->request->get['account_id']);

			// verify account_id belongs to this customer
			$this->load->model('account/account');

			if (!$this->model_account_account->getAccountFromCache($account_id)) {
				$this->error['account_id'] = $this->language->get('error_account_id');
			}
		}

		return !$this->error;
	}

	protected function sort($a, $b) {
		// always put unfollow to the bottom
		if ($a['type'] == 'unfollow') {
			return 1;
		} else if ($b['type'] == 'unfollow') {
			return -1;
		}

		// unknown at the bottom, but above unfollows
		if ($a['type'] == 'unknown') {
			return ($b['type'] == 'unfollow') ? -1 : 1;
		} else if ($b['type'] == 'unknown') {
			return ($a['type'] == 'unfollow') ? 1 : -1;
		}

		if ($a['followbacks'] != $b['followbacks']) {
			// more new followers is better
			return ($a['followbacks'] < $b['followbacks']) ? 1 : -1;
		} else {
			// new followers are the same, higher ratio is better
			return ($a['ratio_val'] < $b['ratio_val']) ? 1 : -1;
		}
	}
}