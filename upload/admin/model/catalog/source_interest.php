<?php
class ModelCatalogSourceInterest extends Model {
	public function addSourceInterest($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest` SET parent_id = '" . (int)$data['parent_id'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

		$source_interest_id = $this->db->getLastId();

		foreach ($data['source_interest_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_description` SET source_interest_id = '" . (int)$source_interest_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		if (isset($data['source_interest_excluded_countries'])) {
			foreach($data['source_interest_excluded_countries'] as $country_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_exclude` SET source_interest_id = '" . (int)$source_interest_id . "', country_id = '" . (int)$country_id . "'");
			}
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_path` WHERE source_interest_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_path` SET `source_interest_id` = '" . (int)$source_interest_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_path` SET `source_interest_id` = '" . (int)$source_interest_id . "', `path_id` = '" . (int)$source_interest_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['source_interest_accounts'])) {
			foreach ($data['source_interest_accounts'] as $country_id => $accounts) {
				foreach ($accounts as $account) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_account` SET source_interest_id = '" . (int)$source_interest_id . "', country_id = '" . (int)$country_id . "', account = '" . $this->db->escape($account['account']) . "', quality = '" . $account['quality'] . "'");
				}
			}
		}

		if (isset($data['source_interest_tags'])) {
			foreach ($data['source_interest_tags'] as $country_id => $tags) {
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_tag` SET source_interest_id = '" . (int)$source_interest_id . "', country_id = '" . (int)$country_id . "', tag = '" . $this->db->escape($tag['tag']) . "', quality = '" . $tag['quality'] . "'");
				}
			}
		}

		$this->cache->delete('source_interest');

		return $source_interest_id;
	}

	public function editSourceInterest($source_interest_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "source_interest` SET parent_id = '" . (int)$data['parent_id'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "source_interest` SET image = '" . $this->db->escape($data['image']) . "' WHERE source_interest_id = '" . (int)$source_interest_id . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_description` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		foreach ($data['source_interest_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_description` SET source_interest_id = '" . (int)$source_interest_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_exclude` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		if (isset($data['source_interest_excluded_countries'])) {
			foreach($data['source_interest_excluded_countries'] as $country_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_exclude` SET source_interest_id = '" . (int)$source_interest_id . "', country_id = '" . (int)$country_id . "'");
			}
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_path` WHERE path_id = '" . (int)$source_interest_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $source_interest_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_path` WHERE source_interest_id = '" . (int)$source_interest_path['source_interest_id'] . "' AND level < '" . (int)$source_interest_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_path` WHERE source_interest_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_path` WHERE source_interest_id = '" . (int)$source_interest_path['source_interest_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "source_interest_path` SET source_interest_id = '" . (int)$source_interest_path['source_interest_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_path` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_path` WHERE source_interest_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_path` SET source_interest_id = '" . (int)$source_interest_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "source_interest_path` SET source_interest_id = '" . (int)$source_interest_id . "', `path_id` = '" . (int)$source_interest_id . "', level = '" . (int)$level . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_account` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		if (isset($data['source_interest_accounts'])) {
			foreach ($data['source_interest_accounts'] as $country_id => $accounts) {
				foreach ($accounts as $account) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_account` SET source_interest_id = '" . (int)$source_interest_id . "', country_id = '" . (int)$country_id . "', account = '" . $this->db->escape($account['account']) . "', quality = '" . $account['quality'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_tag` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		if (isset($data['source_interest_tags'])) {
			foreach ($data['source_interest_tags'] as $country_id => $tags) {
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "source_interest_tag` SET source_interest_id = '" . (int)$source_interest_id . "', country_id = '" . (int)$country_id . "', tag = '" . $this->db->escape($tag['tag']) . "', quality = '" . $tag['quality'] . "'");
				}
			}
		}

		$this->cache->delete('source_interest');
	}

	public function deleteSourceInterest($source_interest_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_path` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_path` WHERE path_id = '" . (int)$source_interest_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteSourceInterest($result['source_interest_id']);
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest` WHERE source_interest_id = '" . (int)$source_interest_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_description` WHERE source_interest_id = '" . (int)$source_interest_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_account` WHERE source_interest_id = '" . (int)$source_interest_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "source_interest_tag` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		$this->cache->delete('source_interest');
	}

	public function getSourceInterest($source_interest_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM `" . DB_PREFIX . "source_interest_path` cp LEFT JOIN `" . DB_PREFIX . "source_interest_description` cd1 ON (cp.path_id = cd1.source_interest_id AND cp.source_interest_id != cp.path_id) WHERE cp.source_interest_id = c.source_interest_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.source_interest_id) AS path FROM " . DB_PREFIX . "source_interest c LEFT JOIN " . DB_PREFIX . "source_interest_description cd2 ON (c.source_interest_id = cd2.source_interest_id) WHERE c.source_interest_id = '" . (int)$source_interest_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		if ($query->row) {
			$source_interest_data = $query->row;

			$store_timezone = new DateTimeZone($this->config->get('config_timezone'));

			$date_added = new DateTime($source_interest_data['date_added']);
			$date_added->setTimezone($store_timezone);

			$date_modified = new DateTime($source_interest_data['date_modified']);
			$date_modified->setTimezone($store_timezone);

			$source_interest_data['date_added']    = $date_added->format('Y-m-d H:i:s');
			$source_interest_data['date_modified'] = $date_modified->format('Y-m-d H:i:s');
		} else {
			$source_interest_data = array();
		}

		return $source_interest_data;
	}

	public function getSourceInterests($data = array()) {
		$sql = "SELECT cp.source_interest_id AS source_interest_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id FROM `" . DB_PREFIX . "source_interest_path` cp LEFT JOIN `" . DB_PREFIX . "source_interest` c1 ON (cp.source_interest_id = c1.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest` c2 ON (cp.path_id = c2.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest_description` cd1 ON (cp.path_id = cd1.source_interest_id) LEFT JOIN `" . DB_PREFIX . "source_interest_description` cd2 ON (cp.source_interest_id = cd2.source_interest_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY cp.source_interest_id";

		$sort_data = array(
			'name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cd2.name";
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

	public function getTotalSourceInterests() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "source_interest`");

		return $query->row['total'];
	}

	public function getSourceInterestDescriptions($source_interest_id) {
		$source_interest_description_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_description` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		foreach ($query->rows as $result) {
			$source_interest_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description']
			);
		}

		return $source_interest_description_data;
	}

	public function getSourceInterestAccounts($source_interest_id) {
		$account_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_account` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		foreach ($query->rows as $result) {
			if (!isset($account_data[$result['country_id']])) {
				$account_data[$result['country_id']] = array();
			}

			$account_data[$result['country_id']][] = array(
				'account' => $result['account'],
				'quality' => $result['quality']
			);
		}

		return $account_data;
	}

	public function getSourceInterestTags($source_interest_id) {
		$tag_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_tag` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		foreach ($query->rows as $result) {
			if (!isset($tag_data[$result['country_id']])) {
				$tag_data[$result['country_id']] = array();
			}

			$tag_data[$result['country_id']][] = array(
				'tag'     => $result['tag'],
				'quality' => $result['quality']
			);
		}

		return $tag_data;
	}

	public function getSourceInterestExcludedCountries($source_interest_id) {
		$excluded_country_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "source_interest_exclude` WHERE source_interest_id = '" . (int)$source_interest_id . "'");

		foreach ($query->rows as $result) {
			$excluded_country_data[] = $result['country_id'];
		}

		return $excluded_country_data;
	}

	public function getSourceHistory($data) {
		$sql = "SELECT sih.*, a.username, CONCAT(c.firstname,' ',c.lastname) as customer FROM `" . DB_PREFIX . "source_interest_history` sih LEFT JOIN `" . DB_PREFIX . "account` a ON a.account_id = sih.account_id LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = sih.customer_id WHERE sih.source_interest_id = '" . (int)$data['source_interest_id'] . "'";

		$sort_data = array(
			'a.username',
			'c.firstname'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sih.date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}

		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getSourceHistoryTotal($data) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "source_interest_history` sih LEFT JOIN `" . DB_PREFIX . "account` a ON a.account_id = sih.account_id LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = sih.customer_id WHERE sih.source_interest_id = '" . (int)$data['source_interest_id'] . "'");

		return $query->row['total'];
	}
}