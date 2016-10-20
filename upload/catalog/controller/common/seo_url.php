<?php
class ControllerCommonSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			foreach ($parts as $part) {
				if (isset($this->request->get['route']) && strpos($this->request->get['route'], 'account/') === 0) {
					$route_parts = explode('/', $this->request->get['route']);

					if (isset($route_parts[1])) {
						$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND username = '" . $this->db->escape($part) . "' AND type = '" . $this->db->escape($route_parts[1]) . "'");

						if ($query->row) {
							$this->request->get['account_id'] = $query->row['account_id'];
						}

						continue;
					}
				}

				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "url_alias` WHERE keyword = '" . $this->db->escape($part) . "'");

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					} else if ($query->row['query']) {
						$this->request->get['route'] = $query->row['query'];
					}
				} else {
					$this->request->get['route'] = 'error/not_found';

					break;
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				}
			}

			if (isset($this->request->get['route'])) {
				return new Action($this->request->get['route']);
			}
		}
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		if (isset($data['route'])) {
			foreach ($data as $key => $value) {
				if ($key == 'route') {
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $this->db->escape($value) . "'");

					if ($query->row) {
						$url .= '/' . $query->row['keyword'];
					}
				} else if ($data['route'] == 'information/information' && $key == 'information_id') {
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				}
			}
		}

		if ($url) {
			if (strpos($data['route'], 'account/') === 0  && isset($data['account_id'])) {
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($data['account_id']) . "'");

				if ($query->row) {
					$url .= '/' . $query->row['username'];

					unset($data['account_id']);
				}
			}

			$url = '/' . trim($url, '/');

			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					if ($value) {
						$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
					} else {
						$query .= '&' . rawurlencode((string)$key);
					}
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}
}