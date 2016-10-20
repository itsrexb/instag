<?php
namespace MailChimp;
class Lists extends MailChimpAPI {
	private $route = 'lists/';

	// http://developer.mailchimp.com/documentation/mailchimp/reference/lists/#read-get_lists
	public function get_lists($data = array()) {
		$result = $this->client->request($this->route, $data, 'GET');

		if ($result && isset($result->lists)) {
			return $result->lists;
		}

		return array();
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/#read-get_lists_list_id_merge_fields
	public function get_fields($list_id, $data = array()) {
		$result = $this->client->request($this->route . $list_id . '/merge-fields', $data, 'GET');

		if ($result && isset($result->merge_fields)) {
			return $result->merge_fields;
		}

		return array();
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#read-get_lists_list_id_members_subscriber_hash
	public function get_member($list_id, $mailchimp_id) {
		$result = $this->client->request($this->route . $list_id . '/members/' . $mailchimp_id);

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#edit-put_lists_list_id_members_subscriber_hash
	public function update_member($list_id, $data, $mailchimp_id = '') {
		if (!$mailchimp_id) {
			$mailchimp_id = md5(strtolower($data['email_address']));
		}

		$result = $this->client->request($this->route . $list_id . '/members/' . $mailchimp_id, $data, 'PUT');

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#delete-delete_lists_list_id_members_subscriber_hash
	public function delete_member($list_id, $mailchimp_id, $data = array()) {
		$result = $this->client->request($this->route . $list_id . '/members/' . $mailchimp_id, $data, 'DELETE');

		return $result;
	}
}