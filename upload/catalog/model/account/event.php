<?php
class ModelAccountEvent extends Model {
	public function getEvents($account_id, $limit = 0) {
		$event_data = array();

		$customer_timezone = new DateTimeZone($this->customer->getTimeZone());

		$this->load->language('account/event');

		$events = $this->instaghive->event->get_list($account_id, $limit);

		foreach ($events as $event) {
			$description = 'event_description_' . strtolower($event->Code);

			if (!empty($event->Message)) {
				$description .= '_' . strtolower(str_replace(' ', '_', $event->Message));
			}

			$date_added = new DateTime($event->AddedDateTime);
			$date_added->setTimezone($customer_timezone);

			$event_data[] = array(
				'code'        => $event->Code,
				'message'     => (!empty($event->Message) ? $event->Message : ''),
				'title'       => $this->language->get('event_title_' . strtolower($event->Code)),
				'description' => $this->language->get($description),
				'date_added'  => $date_added->format('Y-m-d H:i:s')
			);
		}

		return $event_data;
	}
}