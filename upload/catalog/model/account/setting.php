<?php
class ModelAccountSetting extends Model {
	public function editSetting($account_id, $attribute, $value, $action = '') {
		$result = $this->instaghive->accountsetting->update_attribute($account_id, $attribute, $value, $action);
	}

	public function editSettings($account_id, $data) {
		return $this->instaghive->accountsetting->update($account_id, $data);
	}

	public function clearList($account_id, $attribute) {
		$result = $this->instaghive->accountsetting->clear_list($account_id, $attribute);
	}

	public function getSettings($account_id, $attributes = '') {
		$setting_data = $this->instaghive->accountsetting->get($account_id, $attributes);

		return $setting_data;
	}
}