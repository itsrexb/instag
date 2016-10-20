<?php
class ControllerCronAffiliateCommissions extends Controller {
	public function index() {
		set_time_limit(0);

		if (isset($this->request->get['secret']) && $this->request->get['secret'] == $this->config->get('config_cron_secret')) {
			// function used to sort customer account commissions from highest to lowest
			function customer_account_sort($a, $b) {
				if (($a['commissionable_total'] - $a['coupon_affiliate_fee']) == ($b['commissionable_total'] - $b['coupon_affiliate_fee'])) {
					return 0;
				}

				return (($a['commissionable_total'] - $a['coupon_affiliate_fee']) < ($b['commissionable_total'] - $b['coupon_affiliate_fee'])) ? -1 : 1;
			}

			$this->load->language('cron/affiliate_commissions');

			$this->load->model('affiliate/affiliate');
			$this->load->model('affiliate/affiliate_group');
			$this->load->model('cron/affiliate_commissions');
			$this->load->model('cron/cron');
			$this->load->model('marketing/coupon');

			$cron_data = array();

			// get last run cron information
			$cron = $this->model_cron_cron->getCron('affiliate_commissions');

			// figure out commission cycle by getting last run date for this cron job
			if ($cron && $cron['date_last_run']) {
				$date_last_run = $cron['date_last_run'];
			} else {
				$date_last_run = false;
			}

			// if script has already been run today, don't run it again
			if ($date_last_run && date('Y-m-d', strtotime($date_last_run)) == date('Y-m-d')) {
				return;
			}

			$affiliate_commission_data = array();

			$affiliate_group_commissions = array();				// used for caching information to reduce db queries

			$active_affiliates = $this->model_cron_affiliate_commissions->getActiveAffiliates();

			// loop through all active affiliates
			foreach ($active_affiliates as $direct_affiliate) {
				$affiliate_commissions = array();

				// determine this affiliates upline
				$affiliate_path = array_reverse($this->model_affiliate_affiliate->getAffiliatePath($direct_affiliate['affiliate_id']));

				// go through affiliate path and cache commission percentages
				foreach ($affiliate_path as $level => $affiliate_id) {
					$path_affiliate_info = $this->model_affiliate_affiliate->getAffiliate($affiliate_id);

					// if the affiliate group commissions haven't been cached, cache them
					if (!isset($affiliate_group_commissions[$path_affiliate_info['affiliate_group_id']])) {
						$affiliate_group_commissions[$path_affiliate_info['affiliate_group_id']] = $this->model_affiliate_affiliate_group->getAffiliateGroupCommissions($path_affiliate_info['affiliate_group_id']);
					}

					// use affiliate group override for current level, use highest level from default commission levels, otherwise 0
					if (isset($affiliate_group_commissions[$path_affiliate_info['affiliate_group_id']][$direct_affiliate['affiliate_group_id']][$level])) {
						$affiliate_commissions[] = array(
							'affiliate_id' => $affiliate_id,
							'commission'   => $affiliate_group_commissions[$path_affiliate_info['affiliate_group_id']][$direct_affiliate['affiliate_group_id']][$level],
							'account_fee'  => $path_affiliate_info['account_fee']
						);
					} else if (isset($affiliate_group_commissions[$path_affiliate_info['affiliate_group_id']][0])) {
						foreach ($affiliate_group_commissions[$path_affiliate_info['affiliate_group_id']][0] as $commission_level => $commission) {
							if ($level >= $commission_level) {
								$affiliate_commissions[] = array(
									'affiliate_id'   => $affiliate_id,
									'commission_pct' => $commission,
									'account_fee'    => $path_affiliate_info['account_fee']
								);

								break;
							}
						}
					} else {
						$affiliate_commissions[] = array(
							'affiliate_id'   => $affiliate_id,
							'commission_pct' => 0,
							'account_fee'    => $path_affiliate_info['account_fee']
						);
					}
				}

				// get list of customers linked to affiliate that have an active recurring order
				$active_customers = $this->model_cron_affiliate_commissions->getAffiliateActiveCustomers($direct_affiliate['affiliate_id']);

				// loop through all active customers linked to this affiliate
				foreach ($active_customers as $customer) {
					$affiliate_commission_count = ($customer['affiliate_commission_count'] >= 0 ? $customer['affiliate_commission_count'] : $this->config->get('config_affiliate_commission_count'));

					$account_commission_data = array();

					// get list of active recurring orders for this customer
					$active_recurring_orders = $this->model_cron_affiliate_commissions->getCustomerActiveRecurringOrders($customer['customer_id']);

					// find latest order since the last time this script was run and give commissions based on how much was paid for the product
					foreach ($active_recurring_orders as $recurring_order) {
						$last_order = $this->model_cron_affiliate_commissions->getLastOrderForRecurringOrder($recurring_order['recurring_order_id']);

						if ($last_order) {
							$order_date            = $last_order['date_added'];
							$commissionable_amount = $last_order['total'] + $last_order['discount'];

							// if a coupon affected this product in the order, get the coupon affiliate fee from it
							if ($last_order['coupon_id']) {
								$coupon_info = $this->model_marketing_coupon->getCoupon($last_order['coupon_id']);

								if ($coupon_info) {
									$coupon_affiliate_fee = $coupon_info['affiliate_fee'];
								} else {
									$coupon_affiliate_fee = 0;
								}
							} else {
								$coupon_affiliate_fee = 0;
							}
						} else {
							$order_date            = false;
							$commissionable_amount = 0.00;
							$coupon_affiliate_fee  = 0;
						}

						if (isset($account_commission_data[$recurring_order['account_id']])) {
							$account_commission_data[$recurring_order['account_id']]['commissionable_total'] += $commissionable_amount;
							$account_commission_data[$recurring_order['account_id']]['coupon_affiliate_fee'] += $coupon_affiliate_fee;
						} else {
							$account_commission_data[$recurring_order['account_id']] = array(
								'order_date'           => $order_date,
								'commissionable_total' => $commissionable_amount,
								'coupon_affiliate_fee' => $coupon_affiliate_fee
							);
						}
					}

					// sort highest -> lowest
					uasort($account_commission_data, 'customer_account_sort');

					// loop through account totals and give commissions to all eligible affiliates for as many accounts as this customer allows
					$account_count = 0;

					foreach ($account_commission_data as $account_id => $account_commission) {
						if (!$account_commission['order_date'] || !$date_last_run || strtotime($account_commission['order_date']) >= strtotime($date_last_run)) {
							foreach ($affiliate_commissions as $level => $affiliate_commission) {
								if ($affiliate_commission['commission_pct'] > 0) {
									if (isset($affiliate_commission_data[$affiliate_commission['affiliate_id']])) {
										if (isset($affiliate_commission_data[$affiliate_commission['affiliate_id']]['commissions'][$customer['customer_id']])) {
											$affiliate_commission_data[$affiliate_commission['affiliate_id']]['commissions'][$customer['customer_id']]['commission']  += $account_commission['commissionable_total'] * ($affiliate_commission['commission_pct'] / 100);
											$affiliate_commission_data[$affiliate_commission['affiliate_id']]['commissions'][$customer['customer_id']]['account_fee'] += $affiliate_commission['account_fee'];
											$affiliate_commission_data[$affiliate_commission['affiliate_id']]['commissions'][$customer['customer_id']]['coupon_fee']  += $account_commission['coupon_affiliate_fee'];
										} else {
											$affiliate_commission_data[$affiliate_commission['affiliate_id']]['commissions'][$customer['customer_id']] = array(
												'customer_name' => $customer['name'],
												'commission'    => $account_commission['commissionable_total'] * ($affiliate_commission['commission_pct'] / 100),
												'account_fee'   => $affiliate_commission['account_fee'] + $account_commission['coupon_affiliate_fee'],
												'coupon_fee'    => $account_commission['coupon_affiliate_fee']
											);
										}

										if ($level) {
											$affiliate_commission_data[$affiliate_commission['affiliate_id']]['downline_count']++;
										} else {
											$affiliate_commission_data[$affiliate_commission['affiliate_id']]['direct_count']++;
										}
									} else {
										$affiliate_commission_data[$affiliate_commission['affiliate_id']] = array(
											'direct_count'   => (!$level ? 1 : 0),
											'downline_count' => ($level ? 1 : 0),
											'commissions'    => array($customer['customer_id'] => array(
												'customer_name' => $customer['name'],
												'commission'    => $account_commission['commissionable_total'] * ($affiliate_commission['commission_pct'] / 100),
												'account_fee'   => $affiliate_commission['account_fee'],
												'coupon_fee'    => $account_commission['coupon_affiliate_fee']
											))
										);
									}
								}
							}
						}

						$account_count++;

						if ($affiliate_commission_count > 0 && $account_count >= $affiliate_commission_count) {
							break;
						}
					}
				}
			}

			// loop through affiliate commission data and give the affiliate commission
			if ($affiliate_commission_data) {
				$this->load->model('affiliate/transaction');

				foreach ($affiliate_commission_data as $affiliate_id => $affiliate_commission) {
					foreach ($affiliate_commission['commissions'] as $customer_id => $commission) {
						if (($commission['commission'] - $commission['account_fee']) != 0) {
							$this->model_affiliate_transaction->addTransaction(
								$affiliate_id,
								$customer_id,
								($commission['commission'] - $commission['account_fee']),
								sprintf($this->language->get('text_transaction_description'),
									$commission['customer_name']
								),
								$commission['commission'],
								$commission['account_fee']
							);
						}
					}
				}
			}

			$this->model_cron_cron->updateCron('affiliate_commissions', $cron_data);
		}
	}
}