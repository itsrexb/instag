<?php
class ModelReportSale extends Model {
	public function getTotalSales($data = array()) {
		$sql = "SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0'";

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrdersByCountry() {
		$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, c.iso_code_2 FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "country` c ON (o.payment_country_id = c.country_id) WHERE o.order_status_id > '0' GROUP BY o.payment_country_id");

		return $query->rows;
	}

	public function getChartTotalRevenue($range_type) {
		$local_timezone = new \DateTimeZone($this->config->get('config_timezone'));

		$order_data = array(
			'total'      => array(),
			'unknown'    => array(),
			'affiliate'  => array(),
			'ext_aff_id' => array(),
		);

		switch($range_type)
		{
		case 'day':
			for ($i = 0; $i < 24; $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
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
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('-6 days', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'week':
			for ($i = 0; $i < 7; $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
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
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('-29 days', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'month':
			$datetime = new \DateTime(date('Y-m-01'), $local_timezone);

			for ($i = 1; $i <= $datetime->format('t'); $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('first day of this month', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'year':
			for ($i = 1; $i <= 12; $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('first day of January this year', $local_timezone);
			$date_added_start->modify('today');
			break;
		}

		// change date_added_start to UTC so we pull the correct orders from the database
		$date_added_start->setTimezone(new \DateTimeZone('UTC'));

		$sql = "SELECT o.order_id, o.total, o.date_added, c.affiliate_id, c.ext_aff_id FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = o.customer_id) WHERE o.order_status_id IN ('" . implode("','", $this->config->get('config_complete_status')) . "') AND o.date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'";

		$query = $this->db->query($sql);

		$tomorrow = new \DateTime('today', $local_timezone);
		$tomorrow->modify('+1 day');

		switch ($range_type)
		{
		case 'day':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('G')] += $result['total'];

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('G')] += $result['total'];
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('G')] += $result['total'];
				} else {
					$order_data['unknown'][$date_added->format('G')] += $result['total'];
				}
			}
			break;
		case '7days':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$date_added->modify('today');

				$date_diff = 7 - (int)$date_added->diff($tomorrow)->days;

				$order_data['total'][$date_diff] += $result['total'];

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_diff] += $result['total'];
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_diff] += $result['total'];
				} else {
					$order_data['unknown'][$date_diff] += $result['total'];
				}
			}
			break;
		case 'week':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('w')] += $result['total'];

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('w')] += $result['total'];
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('w')] += $result['total'];
				} else {
					$order_data['unknown'][$date_added->format('w')] += $result['total'];
				}
			}
			break;
		case '30days':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$date_added->modify('today');

				$date_diff = 30 - (int)$date_added->diff($tomorrow)->days;

				$order_data['total'][$date_diff] += $result['total'];

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_diff] += $result['total'];
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_diff] += $result['total'];
				} else {
					$order_data['unknown'][$date_diff] += $result['total'];
				}
			}
			break;
		case 'month':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('j')] += $result['total'];

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('j')] += $result['total'];
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('j')] += $result['total'];
				} else {
					$order_data['unknown'][$date_added->format('j')] += $result['total'];
				}
			}
			break;
		case 'year':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('n')] += $result['total'];

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('n')] += $result['total'];
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('n')] += $result['total'];
				} else {
					$order_data['unknown'][$date_added->format('n')] += $result['total'];
				}
			}
			break;
		}

		return $order_data;
	}

	public function getChartTotalOrders($range_type) {
		$local_timezone = new \DateTimeZone($this->config->get('config_timezone'));

		$order_data = array(
			'total'      => array(),
			'unknown'    => array(),
			'affiliate'  => array(),
			'ext_aff_id' => array(),
		);

		switch($range_type)
		{
		case 'day':
			for ($i = 0; $i < 24; $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
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
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('-6 days', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'week':
			for ($i = 0; $i < 7; $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
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
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('-29 days', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'month':
			$datetime = new \DateTime(date('Y-m-01'), $local_timezone);

			for ($i = 1; $i <= $datetime->format('t'); $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('first day of this month', $local_timezone);
			$date_added_start->modify('today');
			break;
		case 'year':
			for ($i = 1; $i <= 12; $i++) {
				$order_data['total'][$i]      = 0;
				$order_data['unknown'][$i]    = 0;
				$order_data['affiliate'][$i]  = 0;
				$order_data['ext_aff_id'][$i] = 0;
			}

			// set the proper day based on the local timezone
			$date_added_start = new \DateTime('first day of January this year', $local_timezone);
			$date_added_start->modify('today');
			break;
		}

		// change date_added_start to UTC so we pull the correct orders from the database
		$date_added_start->setTimezone(new \DateTimeZone('UTC'));

		$sql = "SELECT o.order_id, o.date_added, c.affiliate_id, c.ext_aff_id FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = o.customer_id) WHERE o.order_status_id IN ('" . implode("','", $this->config->get('config_complete_status')) . "') AND o.date_added >= '" . $this->db->escape($date_added_start->format('Y-m-d H:i:s')) . "'";

		$query = $this->db->query($sql);

		$tomorrow = new \DateTime('today', $local_timezone);
		$tomorrow->modify('+1 day');

		switch ($range_type)
		{
		case 'day':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('G')]++;

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('G')]++;
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('G')]++;
				} else {
					$order_data['unknown'][$date_added->format('G')]++;
				}
			}
			break;
		case '7days':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$date_added->modify('today');

				$date_diff = 7 - (int)$date_added->diff($tomorrow)->days;

				$order_data['total'][$date_diff]++;

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_diff]++;
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_diff]++;
				} else {
					$order_data['unknown'][$date_diff]++;
				}
			}
			break;
		case 'week':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('w')]++;

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('w')]++;
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('w')]++;
				} else {
					$order_data['unknown'][$date_added->format('w')]++;
				}
			}
			break;
		case '30days':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$date_added->modify('today');

				$date_diff = 30 - (int)$date_added->diff($tomorrow)->days;

				$order_data['total'][$date_diff]++;

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_diff]++;
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_diff]++;
				} else {
					$order_data['unknown'][$date_diff]++;
				}
			}
			break;
		case 'month':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('j')]++;

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('j')]++;
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('j')]++;
				} else {
					$order_data['unknown'][$date_added->format('j')]++;
				}
			}
			break;
		case 'year':
			foreach ($query->rows as $result) {
				$date_added = new \DateTime($result['date_added']);

				$order_data['total'][$date_added->format('n')]++;

				if ($result['ext_aff_id']) {
					$order_data['ext_aff_id'][$date_added->format('n')]++;
				} else if ($result['affiliate_id']) {
					$order_data['affiliate'][$date_added->format('n')]++;
				} else {
					$order_data['unknown'][$date_added->format('n')]++;
				}
			}
			break;
		}

		return $order_data;
	}

	public function getOrders($data = array()) {


	 	$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, COUNT(*) AS `orders`, SUM((SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS products, SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id)) AS tax, SUM(o.total) AS `total`, (SELECT SUM(op.discount) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id) AS `discount`,(SELECT SUM(op.total) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id) AS `gross`,SUM(o.total ) AS `net`  FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = o.customer_id";


		$implode_data = array();

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$filter_date_start = new \DateTime($data['filter_date_start'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_start->setTimezone(new \DateTimeZone('UTC'));

			$implode_data[] = "o.date_added >= '" . $this->db->escape($filter_date_start->format('Y-m-d H:i:s')) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$filter_date_end = new \DateTime($data['filter_date_end'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_end->setTimezone(new \DateTimeZone('UTC'));

			$implode_data[] = "o.date_added <= '" . $this->db->escape($filter_date_end->format('Y-m-d H:i:s')) . "'";
		}

        if(!empty($data['filter_affiliate_id']) ){
            $implode_data[] = " c.affiliate_id = ".(int)$data['filter_affiliate_id'];
        }

        if(!empty($data['filter_ext_aff_id']) ){
            $implode_data[] = " c.ext_aff_id = ".(int)$data['filter_ext_aff_id'];
        }

		if(!empty($implode_data)){
			$imploded_str = ''; 
			foreach ($implode_data as $implode) {
				$imploded_str .= ' AND '.$implode;
			}
			$sql .= $imploded_str;
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}

		$sql .= " ORDER BY o.date_added DESC";

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

	public function getTotalOrders($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added), DAY(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), WEEK(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}

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

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTaxes($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'";

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

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
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

	public function getTotalTaxes($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}

		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'tax'";

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

	public function getProfit($data = array()) {
	
		
		$implode_data = array();

		$affiliate_transaction ="";


		if (!empty($data['filter_year'])) {
			$implode_data[] = " DATE_FORMAT(o.date_added,'%Y') = '" . $this->db->escape($data['filter_year']) . "' ";
			$affiliate_transaction .= "DATE_FORMAT(at.date_added,'%Y') = '".trim($data['filter_year'])."' AND  DATE_FORMAT(at.date_added,'%M') = DATE_FORMAT(o.date_added,'%M')";
		}

		if (!empty($data['filter_month'])) {
			$implode_data[] = " DATE_FORMAT(o.date_added,'%M') = '" . $this->db->escape($data['filter_month']) . "' ";
		}

		if($affiliate_transaction<>""){
			$affiliate_transaction = " WHERE ".$affiliate_transaction;
		}

		$sql = "SELECT DATE_FORMAT(o.date_added,'%M') as month, COUNT(*) AS `orders`, SUM((SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS `products`,  
						SUM((SELECT SUM(op.total) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS `gross`, SUM((SELECT SUM(op.discount) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS `discount`, (select SUM(gross_amount) from `" . DB_PREFIX . "affiliate_transaction` at ".$affiliate_transaction.") as `acquisition_cost`,
						 SUM(o.total ) as net
						FROM `" . DB_PREFIX . "order` o";

		
		$sql .= " WHERE order_status_id > '0'";

		if(!empty($implode_data)){
			$imploded_str = ''; 
			foreach ($implode_data as $implode) {
				$imploded_str .= ' AND '.$implode;
			}
			$sql .= $imploded_str;
		}

		
		$sql .= " GROUP BY MONTH(o.date_added) ";
		

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

	public function getProfitTotal($data = array()) {
	
		$sql = "SELECT DATE_FORMAT(o.date_added,'%M') as month,
						COUNT(*) AS `orders`
						FROM `" . DB_PREFIX . "order` o";
		$implode_data = array();

		$sql .= " WHERE order_status_id > '0'";
		
		if (!empty($data['filter_month'])) {
			$implode_data[] = " DATE_FORMAT(o.date_added,'%M') = '" . $this->db->escape($data['filter_month']) . "' ";
		}

		if (!empty($data['filter_year'])) {
			$implode_data[] = " DATE_FORMAT(o.date_added,'%Y') = '" . $this->db->escape($data['filter_year']) . "' ";
		}

		if(!empty($implode_data)){
			$imploded_str = ''; 
			foreach ($implode_data as $implode) {
				$imploded_str .= ' AND '.$implode;
			}
			$sql .= $imploded_str;
		}

		
		$sql .= " GROUP BY MONTH(o.date_added) ";
		

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
		return $query->num_rows;
	}

}