<?php
class ModelAccountActivity extends Model {
	public function getActivities($account_id, $activity = '', $limit = 0, $last_evaluated_key = '') {
		$activity_data = array();

		$activities = $this->instaghive->activity->get_list($account_id, $activity, $limit, $last_evaluated_key);

		if ($activities->success) {
			$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

			foreach ($activities->data as $activity) {
				$date_added = new DateTime($activity->AddedDateTime);
				$date_added->setTimezone($customer_timezone);

				$activity->AddedDateTime = $date_added->format('Y-m-d H:i:s');

				$activity_data[] = $activity;
			}
		}

		return array(
			'data'               => $activity_data,
			'last_evaluated_key' => (isset($activities->last_evaluated_key) ? $activities->last_evaluated_key : '')
		);
	}
}