<?php
class ModelCatalogProduct extends Model {
	public function addProduct($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product` SET model = '" . $this->db->escape($data['model']) . "', minimum = '" . (int)$data['minimum'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', time_extension = '" . (int)$data['time_extension'] . "', time_extension_frequency = '" . $this->db->escape($data['time_extension_frequency']) . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");

		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		foreach ($data['product_prices'] as $currency_id => $price) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_price` SET product_id = '" . (int)$product_id . "', currency_id = '" . (int)$currency_id . "', price = '" . (float)$price . "'");
		}

		if (isset($data['product_capabilities'])) {
			foreach ($data['product_capabilities'] as $capability_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_capability` SET product_id = '" . (int)$product_id . "', capability_id = '" . (int)$capability_id . "'");
			}
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_special` SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_download` SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_category` SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_reward` SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		return $product_id;
	}

	public function editProduct($product_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET model = '" . $this->db->escape($data['model']) . "', minimum = '" . (int)$data['minimum'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', time_extension = '" . (int)$data['time_extension'] . "', time_extension_frequency = '" . $this->db->escape($data['time_extension_frequency']) . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_price` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_prices'] as $currency_id => $price) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_price` SET product_id = '" . (int)$product_id . "', currency_id = '" . (int)$currency_id . "', price = '" . (float)$price . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_capability` WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_capabilities'])) {
			foreach ($data['product_capabilities'] as $capability_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_capability` SET product_id = '" . (int)$product_id . "', capability_id = '" . (int)$capability_id . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_store` WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_special` WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_special` SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_download` SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_category` SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_reward` WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_reward` SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$product_recurring['customer_group_id'] . ", `recurring_id` = " . (int)$product_recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');
	}

	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "product` p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['status'] = '0';

			$data['product_capabilities'] = $this->getProductCapabilities($product_id);
			$data['product_category']     = $this->getProductCategories($product_id);
			$data['product_description']  = $this->getProductDescriptions($product_id);
			$data['product_download']     = $this->getProductDownloads($product_id);
			$data['product_prices']       = $this->getProductPrices($product_id);
			$data['product_recurrings']   = $this->getRecurrings($product_id);
			$data['product_reward']       = $this->getProductRewards($product_id);
			$data['product_special']      = $this->getProductSpecials($product_id);
			$data['product_store']        = $this->getProductStores($product_id);

			$this->addProduct($data);
		}
	}

	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_capability` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_price` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_reward` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_special` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_store` WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProducts($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $product_description_data;
	}

	public function getProductPrices($product_id) {
		$product_price_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_price` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_price_data[$result['currency_id']] = $result['price'];
		}

		return $product_price_data;
	}

	public function getProductCapabilities($product_id) {
		$product_capability_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_capability` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_capability_data[] = $result['capability_id'];
		}

		return $product_capability_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_special` WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_reward` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_store` WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_recurring` WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}
}