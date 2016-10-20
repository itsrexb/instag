<?php
class ModelAccountAccountReport extends Model {
	public function addAccountReport($account_id, $report, $meta_data) {
		$this->db->insert(array(
			'table'  => 'account_report',
			'fields' => array(
				'account_id' => $account_id,
				'report'     => $report,
				'date_added' => date('Y-m-d'),
				'meta_data'  => json_encode($meta_data)
			)
		));
	}

	public function getAccountReport($account_id, $report, $date_added) {
		$account_report_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_report` WHERE account_id = '" . $this->db->escape($account_id) . "' AND report = '" . $this->db->escape($report) . "' AND date_added = '" . $this->db->escape($date_added) . "'");

		return ($account_report_query->row ? json_decode($account_report_query->row['meta_data'], true) : false);
	}



	public function getAccountReportFollowerGrowth($account_id, $date_start = '0000-00-00') {
		$account_report_data = $this->getAccountReport($account_id, 'follower_growth', date('Y-m-d'));

		if ($account_report_data === false) {
			$account_followers = array();

			$last_cached_date = date('Y-m-d', strtotime('-1 day', strtotime($date_start)));

			$account_profile_history_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_profile_history` WHERE account_id = '" . $this->db->escape($account_id) . "' AND date_added >= '" . $this->db->escape($date_start) . "' AND date_added < '" . $this->db->escape(date('Y-m-d')) . "' ORDER BY date_added ASC");

			if ($account_profile_history_query->rows) {
				end($account_profile_history_query->rows);

				$last_key = key($account_profile_history_query->rows);

				foreach ($account_profile_history_query->rows as $key => $account_profile_history) {
					$meta_data = json_decode($account_profile_history['meta_data']);

					$account_followers[] = array(
						'date'      => $account_profile_history['date_added'],
						'followers' => $meta_data->CountsFollowedBy
					);

					if ($key == $last_key) {
						$last_cached_date = $account_profile_history['date_added'];
					}
				}
			}

			// if last cached date is at least 2 days old, get all missing dates up to yesterday
			if (strtotime($last_cached_date) <= strtotime('-2 days')) {
				$new_account_profile_history_data = $this->instaghive->profilehistory->get_list($account_id, array(
					'date_added_start' => date('Y-m-d', strtotime('+1 day', strtotime($last_cached_date))),
					'date_added_end'   => date('Y-m-d', strtotime('-1 day'))
				));

				// add any new profile history data into the table
				foreach ($new_account_profile_history_data as $new_account_profile_history) {
					$meta_data = array();

					foreach ($new_account_profile_history as $key => $value) {
						if ($key != 'AccountId' && $key != 'AddedDate') {
							$meta_data[$key] = $value;
						}
					}

					// add followers for each date to $account_followers
					$account_followers[] = array(
						'date'      => $new_account_profile_history->AddedDate,
						'followers' => $new_account_profile_history->CountsFollowedBy
					);

					// store all new profile histories in cache
					$this->db->query("DELETE FROM `" . DB_PREFIX . "account_profile_history` WHERE account_id = '" . $this->db->escape($account_id) . "' AND date_added = '" . $this->db->escape($new_account_profile_history->AddedDate) . "'");
					$this->db->query("INSERT INTO `" . DB_PREFIX . "account_profile_history` SET account_id = '" . $this->db->escape($account_id) . "', date_added = '" . $this->db->escape($new_account_profile_history->AddedDate) . "', meta_data = '" . $this->db->escape(json_encode($meta_data)) . "'");
				}
			}

			$total_account_followers = count($account_followers);

			// if there are more than 4 dates, we need to pick 4
			if ($total_account_followers > 4) {
				// make sure $total_account_followers is divisble by 4
				$extra_account_followers = $total_account_followers % 4;

				if ($extra_account_followers) {
					if ($extra_account_followers >= 1) {
						// remove last element
						array_pop($account_followers);
					}

					if ($extra_account_followers >= 2) {
						// remove middle element
						array_splice($account_followers, ($total_account_followers / 2), 1);
					}

					if ($extra_account_followers == 3) {
						// remove second element
						array_splice($account_followers, 1, 1);
					}

					$total_account_followers = count($account_followers);
				}

				// go through $account_followers and get only 0%, 25%, 50%, 75%
				foreach ($account_followers as $key => $account_follower) {
					if (!$key || $key / $total_account_followers == 0.25 || $key / $total_account_followers == 0.50 || $key / $total_account_followers == 0.75) {
						$account_report_data[] = $account_follower;

						// already have the 4 needed, don't check anymore
						if (count($account_followers) == 4) {
							break;
						}
					}
				}
			} else {
				// no more than 4 dates, use them all
				$account_report_data = $account_followers;
			}

			// store $account_report_data in cache so we don't need to do this next time
			//$this->addAccountReport($account_id, 'follower_growth', $account_report_data);
		}

		return $account_report_data;
	}

	public function getAccountReportSourceTotals($account_id, $date_start = '0000-00-00') {
		$source_totals = array();
		$source_data   = array();

		$today      = new DateTime('today 00:00:00');
		$date_start = new DateTime($date_start . ' 00:00:00');

		$interval = $today->diff($date_start);

		$account_source_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_source_total` WHERE account_id = '" . $this->db->escape($account_id) . "' AND date_added >= '" . $this->db->escape($date_start->format('Y-m-d')) . "' AND date_added < '" . $this->db->escape(date('Y-m-d')) . "' ORDER BY date_added ASC");

		// if enough rows were pulled, use local cache, otherwise get from instag hive
		if ($account_source_total_query->num_rows == (int)$interval->format('%a')) {
			foreach ($account_source_total_query->rows as $key => $account_source_total) {
				$meta_data = json_decode($account_source_total['meta_data']);

				foreach ($meta_data->Sources as $source) {
					$source_data[] = $source;
				}
			}
		} else {
			$new_account_source_total_data = $this->instaghive->sourcetotal->get_list($account_id, array(
				'date_added_start' => $date_start->format('Y-m-d')
			));

			// add any new source totals data into the table
			foreach ($new_account_source_total_data as $new_account_source_total) {
				$meta_data = array();

				foreach ($new_account_source_total as $key => $value) {
					if ($key != 'AccountId' && $key != 'AddedDate') {
						$meta_data[$key] = $value;
					}
				}

				foreach ($new_account_source_total->Sources as $source) {
					$source_data[] = $source;
				}

				// store all new source totals in cache
				$this->db->query("DELETE FROM `" . DB_PREFIX . "account_source_total` WHERE account_id = '" . $this->db->escape($account_id) . "' AND date_added = '" . $this->db->escape($new_account_source_total->AddedDate) . "'");
				$this->db->query("INSERT INTO `" . DB_PREFIX . "account_source_total` SET account_id = '" . $this->db->escape($account_id) . "', date_added = '" . $this->db->escape($new_account_source_total->AddedDate) . "', meta_data = '" . $this->db->escape(json_encode($meta_data)) . "'");
			}
		}

		// combine all dates into source_totals
		foreach ($source_data as $source) {
			$source_id = $source->Source;

			if (!isset($source_totals[$source_id])) {
				if (isset($source->SourceUsername)) {
					$source_name = $source->SourceUsername;
				} else if (isset($source->SourceName)) {
					$source_name = $source->SourceName;
				} else {
					$source_name = $source_id;
				}

				$source_totals[$source_id] = array(
					'source'   => $source_id,
					'name'     => $source_name,
					'type'     => $source->SourceType,
					'actions'  => array(),
					'activity' => array()
				);

				switch($source->SourceType)
				{
				case 'user':
					$source_totals[$source_id]['actions']['commenters'] = 0;
					$source_totals[$source_id]['actions']['likers']     = 0;
					$source_totals[$source_id]['actions']['followers']  = 0;
					$source_totals[$source_id]['actions']['follows']    = 0;
					$source_totals[$source_id]['actions']['likes']      = 0;

					$source_totals[$source_id]['activity']['commenters'] = 0;
					$source_totals[$source_id]['activity']['likers']     = 0;
					$source_totals[$source_id]['activity']['followers']  = 0;
					$source_totals[$source_id]['activity']['follows']    = 0;
					$source_totals[$source_id]['activity']['likes']      = 0;
					break;
				case 'location':
					$source_totals[$source_id]['actions']['locations'] = 0;
					$source_totals[$source_id]['actions']['follows']   = 0;
					$source_totals[$source_id]['actions']['likes']     = 0;

					$source_totals[$source_id]['activity']['locations'] = 0;
					$source_totals[$source_id]['activity']['follows']   = 0;
					$source_totals[$source_id]['activity']['likes']     = 0;
					break;
				case 'tag':
					$source_totals[$source_id]['actions']['taggers'] = 0;
					$source_totals[$source_id]['actions']['follows'] = 0;
					$source_totals[$source_id]['actions']['likes']   = 0;

					$source_totals[$source_id]['activity']['taggers'] = 0;
					$source_totals[$source_id]['activity']['follows'] = 0;
					$source_totals[$source_id]['activity']['likes']   = 0;
					break;
				case 'unknown':
					$source_totals[$source_id]['actions']['follows'] = 0;

					$source_totals[$source_id]['activity']['follows'] = 0;
					break;
				case 'unfollow':
					$source_totals[$source_id]['actions']['unfollows'] = 0;
					break;
				}
			} else {
				// fix locations with bad names
				if ($source->SourceType == 'location' && isset($source->SourceName) && $source_totals[$source_id]['name'] == ltrim($source_id, 'l') && $source->SourceName != ltrim($source_id, 'l')) {
					$source_totals[$source_id]['name'] = $source->SourceName;
				}
			}

			switch($source->SourceType)
			{
			case 'user':
				if (isset($source->Actions)) {
					$source_totals[$source_id]['actions']['commenters'] += $source->Actions->Commenters;
					$source_totals[$source_id]['actions']['likers']     += $source->Actions->Likers;
					$source_totals[$source_id]['actions']['followers']  += $source->Actions->Followers;
					$source_totals[$source_id]['actions']['follows']    += $source->Actions->Follows;
					$source_totals[$source_id]['actions']['likes']      += $source->Actions->Likes;
				}

				if (isset($source->Activity)) {
					$source_totals[$source_id]['activity']['commenters'] += $source->Activity->Commenters;
					$source_totals[$source_id]['activity']['likers']     += $source->Activity->Likers;
					$source_totals[$source_id]['activity']['followers']  += $source->Activity->Followers;
					$source_totals[$source_id]['activity']['follows']    += $source->Activity->Follows;
					$source_totals[$source_id]['activity']['likes']      += $source->Activity->Likes;
				}
				break;
			case 'location':
				if (isset($source->Actions)) {
					$source_totals[$source_id]['actions']['locations'] += $source->Actions->Locations;
					$source_totals[$source_id]['actions']['follows']   += $source->Actions->Follows;
					$source_totals[$source_id]['actions']['likes']     += $source->Actions->Likes;
				}

				if (isset($source->Activity)) {
					$source_totals[$source_id]['activity']['locations'] += $source->Activity->Locations;
					$source_totals[$source_id]['activity']['follows']   += $source->Activity->Follows;
					$source_totals[$source_id]['activity']['likes']     += $source->Activity->Likes;
				}
				break;
			case 'tag':
				if (isset($source->Actions)) {
					$source_totals[$source_id]['actions']['taggers'] += $source->Actions->Taggers;
					$source_totals[$source_id]['actions']['follows'] += $source->Actions->Follows;
					$source_totals[$source_id]['actions']['likes']   += $source->Actions->Likes;
				}

				if (isset($source->Activity)) {
					$source_totals[$source_id]['activity']['taggers'] += $source->Activity->Taggers;
					$source_totals[$source_id]['activity']['follows'] += $source->Activity->Follows;
					$source_totals[$source_id]['activity']['likes']   += $source->Activity->Likes;
				}
				break;
			case 'unknown':
				if (isset($source->Actions)) {
					$source_totals[$source_id]['actions']['follows'] += $source->Actions->Follows;
				}

				if (isset($source->Activity)) {
					$source_totals[$source_id]['activity']['follows'] += $source->Activity->Follows;
				}
				break;
			case 'unfollow':
				if (isset($source->Actions)) {
					$source_totals[$source_id]['actions']['unfollows'] += $source->Actions->Unfollows;
				}
				break;
			}
		}

		return $source_totals;
	}
}