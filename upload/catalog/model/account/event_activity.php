<?php
class ModelAccountEventActivity extends Model {
	public function getEventActivities($account_id, $code = '', $limit = 0, $last_evaluated_key = '') {
		$event_activity_data = array();

		$event_activities = $this->instaghive->eventactivity->get_list($account_id, $code, $limit, $last_evaluated_key);

		if ($event_activities->success) {
			$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

			foreach ($event_activities->data as $event_activity) {
				$date_added = new DateTime($event_activity->AddedDateTime);
				$date_added->setTimezone($customer_timezone);

				$event_activity->AddedDateTime = $date_added->format('Y-m-d H:i:s');

				$event_activity_data[] = $event_activity;
			}
		}

		return array(
			'data'               => $event_activity_data,
			'last_evaluated_key' => (isset($event_activities->last_evaluated_key) ? $event_activities->last_evaluated_key : '')
		);
	}
}