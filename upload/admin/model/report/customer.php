<?php
class ModelReportCustomer extends Model {
	public function getChartTotalCustomers($range_type) {
		$local_timezone = new \DateTimeZone($this->config->get('config_timezone'));

		$customer_data = array(
			'total'      => array(),
			'unknown'    => array(),
			'affiliate'  => array(),
			'ext_aff_id' => array(),
		);

		switch($range_type)
		{
		case 'day':
			for ($i = 0; $i < 24; $i++) {
				$customer_data['total'][$i]      = 0;
				$customer_data['unknown'][$i]    = 0;
				$customer_data['affiliate'][$i]  = 0;
				$customer_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('today', $local_timezone);
			break;
		case '7days':
			$datetime = new \DateTime('-6 days', $local_timezone);
			$datetime->modify('today');

			$datetime_end = new \DateTime('+1 day', $local_timezone);
			$datetime_end->modify('today');

			for ($i = 0; $i < (int)$datetime->diff($datetime_end)->days; $i++) {
				$customer_data['total'][$i]      = 0;
				$customer_data['unknown'][$i]    = 0;
				$customer_data['affiliate'][$i]  = 0;
				$customer_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('-6 days', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'week':
			for ($i = 0; $i < 7; $i++) {
				$customer_data['total'][$i]      = 0;
				$customer_data['unknown'][$i]    = 0;
				$customer_data['affiliate'][$i]  = 0;
				$customer_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('now', $local_timezone);
			$date_added_start->modify('-' . $date_added_start->format('w') . ' days');
			$date_added_start->modify('today');
			break;
		case '30days':
			$datetime = new \DateTime('-29 days', $local_timezone);
			$datetime->modify('today');

			$datetime_end = new \DateTime('+1 day', $local_timezone);
			$datetime_end->modify('today');

			for ($i = 0; $i < (int)$datetime->diff($datetime_end)->days; $i++) {
				$customer_data['total'][$i]      = 0;
				$customer_data['unknown'][$i]    = 0;
				$customer_data['affiliate'][$i]  = 0;
				$customer_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('-29 days', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'month':
			$datetime = new \DateTime(date('Y-m-01'), $local_timezone);

			for ($i = 1; $i <= $datetime->format('t'); $i++) {
				$customer_data['total'][$i]      = 0;
				$customer_data['unknown'][$i]    = 0;
				$customer_data['affiliate'][$i]  = 0;
				$customer_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('first day of this month', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'year':
			for ($i = 1; $i <= 12; $i++) {
				$customer_data['total'][$i]      = 0;
				$customer_data['unknown'][$i]    = 0;
				$customer_data['affiliate'][$i]  = 0;
				$customer_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('first day of January this year', $local_timezone);
			$date_added_start->modify('today');
			break;
		}

		// change date_added_start to UTC so we pull the correct orders from the database
		$date_added_start->setTimezone(new \DateTimeZone('UTC'));

		$sql = "SELECT customer_id, date_added, affiliate_id, ext_aff_id FROM `" . DB_PREFIX . "customer` WHERE date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'";

		$query = $this->db->query($sql);

		$tomorrow = new \DateTime('today', $local_timezone);
		$tomorrow->modify('+1 day');

		switch ($range_type)
		{
		case 'day':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$customer_data['total'][$date_added->format('G')]++;

				if ($result['ext_aff_id']) {
					$customer_data['ext_aff_id'][$date_added->format('G')]++;
				} else if ($result['affiliate_id']) {
					$customer_data['affiliate'][$date_added->format('G')]++;
				} else {
					$customer_data['unknown'][$date_added->format('G')]++;
				}
			}
			break;
		case '7days':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$date_added->modify('today');

				$date_diff = 7 - (int)$date_added->diff($tomorrow)->days;

				$customer_data['total'][$date_diff]++;

				if ($result['ext_aff_id']) {
					$customer_data['ext_aff_id'][$date_diff]++;
				} else if ($result['affiliate_id']) {
					$customer_data['affiliate'][$date_diff]++;
				} else {
					$customer_data['unknown'][$date_diff]++;
				}
			}
			break;
		case 'week':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$customer_data['total'][$date_added->format('w')]++;

				if ($result['ext_aff_id']) {
					$customer_data['ext_aff_id'][$date_added->format('w')]++;
				} else if ($result['affiliate_id']) {
					$customer_data['affiliate'][$date_added->format('w')]++;
				} else {
					$customer_data['unknown'][$date_added->format('w')]++;
				}
			}
			break;
		case '30days':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$date_added->modify('today');

				$date_diff = 30 - (int)$date_added->diff($tomorrow)->days;

				$customer_data['total'][$date_diff]++;

				if ($result['ext_aff_id']) {
					$customer_data['ext_aff_id'][$date_diff]++;
				} else if ($result['affiliate_id']) {
					$customer_data['affiliate'][$date_diff]++;
				} else {
					$customer_data['unknown'][$date_diff]++;
				}
			}
			break;
		case 'month':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$customer_data['total'][$date_added->format('j')]++;

				if ($result['ext_aff_id']) {
					$customer_data['ext_aff_id'][$date_added->format('j')]++;
				} else if ($result['affiliate_id']) {
					$customer_data['affiliate'][$date_added->format('j')]++;
				} else {
					$customer_data['unknown'][$date_added->format('j')]++;
				}
			}
			break;
		case 'year':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$customer_data['total'][$date_added->format('n')]++;

				if ($result['ext_aff_id']) {
					$customer_data['ext_aff_id'][$date_added->format('n')]++;
				} else if ($result['affiliate_id']) {
					$customer_data['affiliate'][$date_added->format('n')]++;
				} else {
					$customer_data['unknown'][$date_added->format('n')]++;
				}
			}
			break;
		}

		return $customer_data;
	}
	
	public function getTotalCustomersByDay($type = '') {
		$customer_data = array();

		for ($i = 0; $i < 24; $i++) {
			$customer_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}

		$date_added_start = new \DateTime('today', new \DateTimeZone($this->config->get('config_timezone')));
		$date_added_start->setTimezone(new \DateTimeZone('UTC'));

		if($type == ''){
			$query = $this->db->query("SELECT customer_id, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "customer` WHERE date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'Unknown'){
			$query = $this->db->query("SELECT customer_id, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id = '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'Affiliate'){
			$query = $this->db->query("SELECT customer_id, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "customer` WHERE affiliate_id != 0 AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'ext_aff'){
			$query = $this->db->query("SELECT customer_id, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id != '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		foreach ($query->rows as $result) {
			$customer_data[$result['hour']]['total']++;
		}

		return $customer_data;
	}

	public function getTotalCustomersByWeek($type = '') {
		$customer_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$customer_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}

		$date_added_start = new \DateTime('-' . date('w') . ' days', new \DateTimeZone($this->config->get('config_timezone')));
		$date_added_start->setTimezone(new \DateTimeZone('UTC'));

		if($type == ''){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'Unknown'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id = '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'Affiliate'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE affiliate_id != 0 AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'ext_aff'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id != '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		foreach ($query->rows as $result) {
			$customer_data[date('w', strtotime($result['date_added']))]['total']++;
		}

		return $customer_data;
	}

	public function getTotalCustomersByMonth($type = '') {
		$customer_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$customer_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$date_added_start = new \DateTime(date('Y') . '-' . date('m') . '-01', new \DateTimeZone($this->config->get('config_timezone')));
		$date_added_start->setTimezone(new \DateTimeZone('UTC'));

		if($type == ''){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'Unknown'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id = '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'Affiliate'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE affiliate_id != 0 AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'ext_aff'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id != '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		foreach ($query->rows as $result) {
			$customer_data[date('j', strtotime($result['date_added']))]['total']++;
		}

		return $customer_data;
	}

	public function getTotalCustomersByYear($type = '') {
		$customer_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$customer_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$date_added_start = new \DateTime(date('Y') . '-01-01', new \DateTimeZone($this->config->get('config_timezone')));
		$date_added_start->setTimezone(new \DateTimeZone('UTC'));

		if($type == ''){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}
		
		if($type == 'Unknown'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id = '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}

		if($type == 'Affiliate'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE affiliate_id != 0 AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}
		
		if($type == 'ext_aff'){
			$query = $this->db->query("SELECT customer_id, date_added FROM `" . DB_PREFIX . "customer` WHERE (affiliate_id = 0 AND ext_aff_id != '') AND date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'");
		}


		foreach ($query->rows as $result) {
			$customer_data[date('n', strtotime($result['date_added']))]['total']++;
		}

		return $customer_data;
	}

	public function getOrders($data = array()) {
		$sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, o.order_id, SUM(op.quantity) as products, SUM(DISTINCT o.total) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_product` op ON (o.order_id = op.order_id)LEFT JOIN `" . DB_PREFIX . "customer` c ON (o.customer_id = c.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE o.customer_id > 0 AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " GROUP BY o.order_id";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$sql = "SELECT t.customer_id, t.customer, t.email, t.customer_group, t.status, COUNT(t.order_id) AS orders, SUM(t.products) AS products, SUM(t.total) AS total FROM (" . $sql . ") AS t GROUP BY t.customer_id ORDER BY total DESC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(DISTINCT o.customer_id) AS total FROM `" . DB_PREFIX . "order` o WHERE o.customer_id > '0'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "o.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getRewardPoints($data = array()) {
		$sql = "SELECT cr.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, SUM(cr.points) AS points, COUNT(o.order_id) AS orders, SUM(o.total) AS total FROM " . DB_PREFIX . "customer_reward cr LEFT JOIN `" . DB_PREFIX . "customer` c ON (cr.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (cr.order_id = o.order_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "cr.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "cr.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " GROUP BY cr.customer_id ORDER BY points DESC";

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

	public function getTotalRewardPoints($data = array()) {
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_reward`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCredit($data = array()) {
		$sql = "SELECT ct.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, SUM(ct.amount) AS total FROM `" . DB_PREFIX . "customer_transaction` ct LEFT JOIN `" . DB_PREFIX . "customer` c ON (ct.customer_id = c.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "ct.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "ct.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$sql .= " GROUP BY ct.customer_id ORDER BY total DESC";

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

	public function getTotalCredit($data = array()) {
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_transaction`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProfit($data = array()) {
		$sql = "SELECT MIN(t1.date_added) AS date_start, MAX(t1.date_added) AS date_end, COUNT(DISTINCT t1.customer_id) AS total_customers, SUM(t1.paid_accounts) AS total_paid_accounts, SUM(t1.revenue) AS total_revenue FROM (SELECT c.customer_id, c.date_added, (SELECT COUNT(DISTINCT ro.recurring_order_id) FROM `" . DB_PREFIX . "recurring_order` ro WHERE ro.customer_id = c.customer_id AND ro.active = '1') AS paid_accounts, (SELECT SUM(o.total) FROM `" . DB_PREFIX . "order` o WHERE o.customer_id = c.customer_id AND o.order_status_id IN ('5')) AS revenue FROM `" . DB_PREFIX . "customer` c";

		$where_data = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "c.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if (isset($data['filter_affiliate_id'])) {
			$where_data[] = "c.affiliate_id = '" . (int)$data['filter_affiliate_id'] . "'";
		}

		if (isset($data['filter_ext_aff_id'])) {
			$where_data[] = "c.ext_aff_id = '" . $this->db->escape($data['filter_ext_aff_id']) . "'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$sql .= " ORDER BY c.date_added DESC) t1";

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = '';
		}

		switch($group)
		{
		case 'day';
			$sql .= " GROUP BY YEAR(t1.date_added), MONTH(t1.date_added), DAY(t1.date_added)";
			break;
		case 'week':
			$sql .= " GROUP BY YEAR(t1.date_added), WEEK(t1.date_added)";
			break;
		case 'month':
			$sql .= " GROUP BY YEAR(t1.date_added), MONTH(t1.date_added)";
			break;
		case 'year':
			$sql .= " GROUP BY YEAR(t1.date_added)";
			break;
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

	public function getTotalProfit($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = '';
		}

		switch($group)
		{
		case 'day';
			$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added), DAY(date_added)) AS total FROM `" . DB_PREFIX . "customer`";
			break;
		case 'week':
			$sql = "SELECT COUNT(DISTINCT YEAR(date_added), WEEK(date_added)) AS total FROM `" . DB_PREFIX . "customer`";
			break;
		case 'month':
			$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added)) AS total FROM `" . DB_PREFIX . "customer`";
			break;
		case 'year':
			$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM `" . DB_PREFIX . "customer`";
			break;
		default:
			return 1;
		}

		$where_data = array();

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$where_data[] = "date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if (isset($data['filter_affiliate_id'])) {
			$where_data[] = "affiliate_id = '" . (int)$data['filter_affiliate_id'] . "'";
		}

		if (isset($data['filter_ext_aff_id'])) {
			$where_data[] = "ext_aff_id = '" . $this->db->escape($data['filter_ext_aff_id']) . "'";
		}

		if ($where_data) {
			$sql .= " WHERE " . implode(" AND ", $where_data);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalRevenue($start_date,$end_date, $data = array()) {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}	
		
		$sql = "SELECT SUM(o.total) AS total  FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "customer` c ON o.customer_id = c.customer_id ";

		$sql .= " WHERE o.order_status_id IN(" . implode(",", $implode) . ") ";

		if (isset($data['filter_affiliate_id'])) {
			$sql .= " AND c.affiliate_id = '" . (int)$data['filter_affiliate_id'] . "'";
		}

		if (isset($data['filter_ext_aff_id'])) {
			$sql .= " AND c.ext_aff_id = '" . $this->db->escape($data['filter_ext_aff_id']) . "'";
		}

		if (!empty($start_date)) {
			$filter_date_start = new \DateTime($start_date . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND c.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($end_date)) {
			$filter_date_end = new \DateTime($end_date . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND c.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		$query = $this->db->query($sql);
		return $query->row['total'];		
	}

	public function getTotalPaidAccounts($start_date,$end_date, $data = array()) {	
		$sql = "SELECT COUNT(*) AS total_paid_accounts FROM `" . DB_PREFIX . "recurring_order` ro
		LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = ro.customer_id WHERE ro.active = '1' ";

		if (!empty($data['filter_affiliate_id'])) {
			$sql .= " AND c.affiliate_id = '" . (int)$data['filter_affiliate_id'] . "'";
		}

		if (!empty($data['filter_ext_aff_id'])) {
			$sql .= " AND c.ext_aff_id = '" . $this->db->escape($data['filter_ext_aff_id']) . "'";
		}
		
		if (!empty($start_date)) {
			$filter_date_start = new \DateTime($start_date . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND c.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($end_date)) {
			$filter_date_end = new \DateTime($end_date . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$sql .= " AND c.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}
		
		$query = $this->db->query($sql);
		return $query->row['total_paid_accounts'];		
	}

	public function getCustomersOnline($data = array()) {
		$sql = "SELECT co.ip, co.customer_id, co.url, co.referer, co.date_added FROM " . DB_PREFIX . "customer_online co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY co.date_added DESC";

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

	public function getTotalCustomersOnline($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_online` co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCustomerActivities($data = array()) {
		$sql = "SELECT ca.customer_activity_id, ca.customer_id, ca.key, ca.data, ca.ip, ca.date_added FROM " . DB_PREFIX . "customer_activity ca LEFT JOIN " . DB_PREFIX . "customer c ON (ca.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "ca.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "ca.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "ca.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY ca.date_added DESC";

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

	public function getTotalCustomerActivities($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_activity` ca LEFT JOIN " . DB_PREFIX . "customer c ON (ca.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "ca.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "ca.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "ca.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
