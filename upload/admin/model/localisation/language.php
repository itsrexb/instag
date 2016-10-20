<?php
class ModelLocalisationLanguage extends Model {
	public function addLanguage($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "language` SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', locale = '" . $this->db->escape($data['locale']) . "', directory = '" . $this->db->escape($data['directory']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "', status = '" . (int)$data['status'] . "'");

		$this->cache->delete('language');

		$language_id = $this->db->getLastId();

		// Banner
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "banner_image_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $banner_image) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "banner_image_description` SET banner_image_id = '" . (int)$banner_image['banner_image_id'] . "', banner_id = '" . (int)$banner_image['banner_id'] . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($banner_image['title']) . "'");
		}

		$this->cache->delete('banner');

		// Category
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $category) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "category_description` SET category_id = '" . (int)$category['category_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($category['name']) . "', description = '" . $this->db->escape($category['description']) . "', meta_title = '" . $this->db->escape($category['meta_title']) . "', meta_description = '" . $this->db->escape($category['meta_description']) . "', meta_keyword = '" . $this->db->escape($category['meta_keyword']) . "'");
		}

		$this->cache->delete('category');

		// Customer Group
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_group_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $customer_group) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_group_description` SET customer_group_id = '" . (int)$customer_group['customer_group_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($customer_group['name']) . "', description = '" . $this->db->escape($customer_group['description']) . "'");
		}

		// Custom Field
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $custom_field) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_description` SET custom_field_id = '" . (int)$custom_field['custom_field_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($custom_field['name']) . "'");
		}

		// Custom Field Value
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_value_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $custom_field_value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_value_description` SET custom_field_value_id = '" . (int)$custom_field_value['custom_field_value_id'] . "', language_id = '" . (int)$language_id . "', custom_field_id = '" . (int)$custom_field_value['custom_field_id'] . "', name = '" . $this->db->escape($custom_field_value['name']) . "'");
		}

		// Download
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "download_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $download) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "download_description` SET download_id = '" . (int)$download['download_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($download['name']) . "'");
		}

		// Information
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "information_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $information) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "information_description` SET information_id = '" . (int)$information['information_id'] . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($information['title']) . "', description = '" . $this->db->escape($information['description']) . "', meta_title = '" . $this->db->escape($information['meta_title']) . "', meta_description = '" . $this->db->escape($information['meta_description']) . "', meta_keyword = '" . $this->db->escape($information['meta_keyword']) . "'");
		}

		$this->cache->delete('information');

		// Order Status
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $order_status) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "order_status` SET order_status_id = '" . (int)$order_status['order_status_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($order_status['name']) . "'");
		}

		$this->cache->delete('order_status');

		// Product
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $product) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` SET product_id = '" . (int)$product['product_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($product['name']) . "', description = '" . $this->db->escape($product['description']) . "', meta_title = '" . $this->db->escape($product['meta_title']) . "', meta_description = '" . $this->db->escape($product['meta_description']) . "', meta_keyword = '" . $this->db->escape($product['meta_keyword']) . "'");
		}

		$this->cache->delete('product');

		// Voucher Theme
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "voucher_theme_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $voucher_theme) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "voucher_theme_description` SET voucher_theme_id = '" . (int)$voucher_theme['voucher_theme_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($voucher_theme['name']) . "'");
		}

		$this->cache->delete('voucher_theme');

		// Profiles
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $recurring) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring_description` SET recurring_id = '" . (int)$recurring['recurring_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($recurring['name']) . "'");
		}

		// Account Tips
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account_tip_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $account_tip) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "account_tip_description` SET account_tip_id = '" . (int)$account_tip['account_tip_id'] . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($account_tip['title']) . "', description = '" . $this->db->escape($account_tip['description']) . "'");
		}

		// Source Interests
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $source_interest) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_description` SET source_interest_id = '" . (int)$source_interest['source_interest_id'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($source_interest['name']) . "', description = '" . $this->db->escape($source_interest['description']) . "'");
		}

		return $language_id;
	}

	public function editLanguage($language_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "language` SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', locale = '" . $this->db->escape($data['locale']) . "', directory = '" . $this->db->escape($data['directory']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . $this->db->escape($data['sort_order']) . "', status = '" . (int)$data['status'] . "' WHERE language_id = '" . (int)$language_id . "'");

		$this->cache->delete('language');
	}

	public function deleteLanguage($language_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "language` WHERE language_id = '" . (int)$language_id . "'");

		$this->cache->delete('language');

		$this->db->query("DELETE FROM `" . DB_PREFIX . "banner_image_description` WHERE language_id = '" . (int)$language_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_description` WHERE language_id = '" . (int)$language_id . "'");

		$this->cache->delete('category');

		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_group_description` WHERE language_id = '" . (int)$language_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "download_description` WHERE language_id = '" . (int)$language_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "information_description` WHERE language_id = '" . (int)$language_id . "'");

		$this->cache->delete('information');

		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_status` WHERE language_id = '" . (int)$language_id . "'");

		$this->cache->delete('order_status');

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE language_id = '" . (int)$language_id . "'");

		$this->cache->delete('product');

		$this->db->query("DELETE FROM `" . DB_PREFIX . "voucher_theme_description` WHERE language_id = '" . (int)$language_id . "'");

		$this->cache->delete('voucher_theme');

		$this->db->query("DELETE FROM `" . DB_PREFIX . "recurring_description` WHERE language_id = '" . (int)$language_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "account_tip_description` WHERE language_id = '" . (int)$language_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_description` WHERE language_id = '" . (int)$language_id . "'");
	}

	public function getLanguage($language_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");

		return $query->row;
	}

	public function getLanguages($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "language";

		$sort_data = array(
			'name',
			'code',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order, name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalLanguages() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "language");

		return $query->row['total'];
	}
}