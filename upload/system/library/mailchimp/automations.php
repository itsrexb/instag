<?php
namespace MailChimp;
class Automations extends MailChimpAPI {
	private $route = 'automations/';

	// http://developer.mailchimp.com/documentation/mailchimp/reference/automations/emails/queue/#create-post_automations_workflow_id_emails_workflow_email_id_queue
	public function add_to_workflow_email($workflow_id, $workflow_email_id, $email) {
		$data = array(
			'email_address' => $email
		);

		$result = $this->client->request($this->route . $workflow_id . '/emails/' . $workflow_email_id . '/queue', $data);

		return $result;
	}
}