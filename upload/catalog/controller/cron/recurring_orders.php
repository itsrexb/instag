<?php
class ControllerCronRecurringOrders extends Controller {
	public function index() {
		set_time_limit(0);

		if (isset($this->request->get['secret']) && $this->request->get['secret'] == $this->config->get('config_cron_secret')) {
			$this->load->model('catalog/product');
			$this->load->model('checkout/order');
			$this->load->model('cron/cron');
			$this->load->model('cron/recurring_order');
			$this->load->model('customer/customer');
			$this->load->model('extension/extension');
			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');

			$cron_data = array();

			$recurring_orders = array();

			$results = $this->model_cron_recurring_order->getRecurringOrders(array(
				'filter_date_next_recurring_end' => date('Y-m-d', strtotime('+' . (int)$this->config->get('config_recurring_order_days_before') . ' days')),
				'filter_active'                  => true // Active
			));

			foreach ($results as $result) {
				// if an order has already been attempted today for this recurring order, don't try it again
				$last_recurring_transaction_date = $this->model_cron_recurring_order->getLastRecurringTransactionDate($result['recurring_order_id']);

				if ($last_recurring_transaction_date && strtotime('today', strtotime($last_recurring_transaction_date)) == strtotime('today')) {
					continue;
				}

				$recurring_order = array(
					'store_id'            => $result['store_id'],
					'customer_id'         => $result['customer_id'],
					'payment_method'      => $result['payment_method'],
					'payment_code'        => $result['payment_code'],
					'language_id'         => $result['language_id'],
					'currency_id'         => $result['currency_id'],
					'currency_code'       => $result['currency_code'],
					'currency_value'      => $result['currency_value'],
					'date_next_recurring' => $result['date_next_recurring'],
					'metadata'            => json_decode($result['metadata'], true)
				);

				$product = array(
					'recurring_order_id'  => $result['recurring_order_id'],
					'account_id'          => $result['account_id'],
					'product_id'          => $result['product_id'],
					'name'                => $result['product_name'],
					'quantity'            => $result['product_quantity'],
					'recurring_price'     => $result['recurring_price'],
					'recurring_cycle'     => $result['recurring_cycle'],
					'recurring_frequency' => $result['recurring_frequency'],
					'recurring_duration'  => $result['recurring_duration'],
					'trial_price'         => $result['trial_price'],
					'trial_cycle'         => $result['trial_cycle'],
					'trial_frequency'     => $result['trial_frequency'],
					'trial_duration'      => $result['trial_duration'],
					'coupon_id'           => $result['coupon_id'],
					'coupon_remaining'    => $result['coupon_remaining'],
					'date_next_recurring' => $result['date_next_recurring']
				);

				if ($this->config->get('config_recurring_order_combine_transactions')) {
					$key = $result['store_id'] . '.' . $result['customer_id'] . '.' . $result['metadata'];

					if (!isset($recurring_orders[$key])) {
						$recurring_orders[$key] = $recurring_order;

						$recurring_orders[$key]['products'] = array();
					}

					$recurring_orders[$key]['products'][] = $product;
				} else {
					$recurring_orders[] = array_merge($recurring_order, array('products' => array($product)));
				}
			}

			foreach ($recurring_orders as $recurring_order) {
				$this->cart->clear();

				$this->session->data['recurring_coupons'] = array();

				$this->model_cron_recurring_order->useStoreConfig($recurring_order['store_id']);

				// set currency according to recurring order
				$this->currency->set($recurring_order['currency_code']);

				$order_data = array();

				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id']       = $this->config->get('config_store_id');
				$order_data['store_name']     = $this->config->get('config_name');

				if ($order_data['store_id']) {
					$order_data['store_url'] = $this->config->get('config_url');
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}

				$customer_info = $this->model_customer_customer->getCustomer($recurring_order['customer_id']);

				$this->customer->login($customer_info['email'], '', true);

				$order_data['customer_id']       = $recurring_order['customer_id'];
				$order_data['customer_group_id'] = $this->customer->getGroupId();
				$order_data['firstname']         = $this->customer->getFirstName();
				$order_data['lastname']          = $this->customer->getLastName();
				$order_data['email']             = $this->customer->getEmail();
				$order_data['telephone']         = $this->customer->getTelephone();
				$order_data['payment_method']    = $recurring_order['payment_method'];
				$order_data['payment_code']      = $recurring_order['payment_code'];

				// Products
				foreach ($recurring_order['products'] as $product) {
					if ($product['trial_duration']) {
						$price = $product['trial_price'];
					} else {
						$price = $product['recurring_price'];
					}

					$cart_id = $this->cart->add($product['product_id'], $product['account_id'], $product['quantity'], 0, $price);

					if ($product['coupon_id']) {
						$this->session->data['recurring_coupons'][] = array(
							'coupon_id' => $product['coupon_id'],
							'cart_id'   => $cart_id
						);
					}
				}

				if ($this->cart->hasProducts()) {
					// Totals
					$order_data['totals'] = array();
					$total = 0;
					$taxes = $this->cart->getTaxes();

					$sort_order = array();

					$results = $this->model_extension_extension->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get($result['code'] . '_status')) {
							$this->load->model('total/' . $result['code']);

							$this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);
						}
					}

					$order_data['products'] = array();

					foreach ($this->cart->getProducts() as $product) {
						$recurring_order_id = 0;

						// find the existing recurring_order_id for the cart product
						foreach ($recurring_order['products'] as $recurring_product) {
							if ($recurring_product['account_id'] == $product['account_id'] && $recurring_product['product_id'] == $product['product_id']) {
								$recurring_order_id = $recurring_product['recurring_order_id'];
								break;
							}
						}

						$order_data['products'][] = array(
							'recurring_order_id'       => $recurring_order_id,
							'product_id'               => $product['product_id'],
							'name'                     => $product['name'],
							'model'                    => $product['model'],
							'account_id'               => $product['account_id'],
							'account_type'             => $product['account_type'],
							'account_username'         => $product['account_username'],
							'time_extension'           => $product['time_extension'],
							'time_extension_frequency' => $product['time_extension_frequency'],
							'download'                 => array(),
							'quantity'                 => $product['quantity'],
							'price'                    => $product['price'],
							'total'                    => $product['total'],
							'discount'                 => $product['discount'],
							'coupon_id'                => $product['coupon_id'],
							'tax'                      => $this->tax->getTax($product['price'], $product['tax_class_id']),
							'reward'                   => $product['reward'],
							'recurring'                => $product['recurring']
						);
					}

					$order_data['vouchers']        = array();
					$order_data['comment']         = '';
					$order_data['total']           = $total;
					$order_data['language_id']     = $recurring_order['language_id'];
					$order_data['currency_id']     = $this->currency->getId();
					$order_data['currency_code']   = $recurring_order['currency_code'];
					$order_data['currency_value']  = $this->currency->getValue($this->currency->getCode());
					$order_data['ip']              = '';
					$order_data['forwarded_ip']    = '';
					$order_data['user_agent']      = '';
					$order_data['accept_language'] = '';
					$order_data['recurring_order'] = 1;

					$order_id = $this->model_checkout_order->addOrder($order_data);

					// load model for payment method and do the transaction there
					$transaction_result = false;

					if (is_file(DIR_APPLICATION . 'model/payment/' . $recurring_order['payment_code'] . '.php')) {
						$this->load->model('payment/' . $recurring_order['payment_code']);

						if (method_exists($this->{'model_payment_' . $recurring_order['payment_code']}, 'recurringOrder')) {
							$transaction_result = $this->{'model_payment_' . $recurring_order['payment_code']}->recurringOrder($order_id, $recurring_order);
						}
					}

					foreach ($recurring_order['products'] as $product) {
						$this->model_cron_recurring_order->updateRecurringOrder($product, $transaction_result, $order_data);
					}

					if ($transaction_result) {
						// update any enabled marketing extensions
						$this->load->model('extension/extension');
						$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

						foreach ($marketing_extensions as $marketing) {
							if ($this->config->get($marketing['code'] . '_status')) {
								$this->{$marketing['code']} = new $marketing['code']($this->registry);

								$this->load->model('extension/marketing/' . $marketing['code']);
								$this->{'model_extension_marketing_' . $marketing['code']}->addOrder($order_id);
							}
						}
					}

					$this->cart->clear();
				} else {
					// the recurring products have been deleted, cancel this recurring profile
					foreach ($recurring_order['products'] as $product) {
						$this->model_cron_recurring_order->cancelRecurringOrder($product['recurring_order_id']);
					}
				}

				$this->customer->logout();
				$this->instaghive->logout();
			}

			$this->cart->clear();

			if (isset($this->session->data['recurring_coupons'])) {
				unset($this->session->data['recurring_coupons']);
			}

			$this->model_cron_cron->updateCron('recurring_orders', $cron_data);
		}
	}
}