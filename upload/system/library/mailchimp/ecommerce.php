<?php
namespace MailChimp;
class Ecommerce extends MailChimpAPI {
	private $route = 'ecommerce/stores/';

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/#create-post_ecommerce_stores
	public function add_store($data = array()) {
		$result = $this->client->request($this->route, $data);

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/#edit-patch_ecommerce_stores_store_id
	public function edit_store($store_id, $data = array()) {
		$result = $this->client->request($this->route . $store_id, $data, 'PATCH');

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/customers/#read-get_ecommerce_stores_store_id_customers_customer_id
	public function get_customer($store_id, $customer_id) {
		$result = $this->client->request($this->route . $store_id . '/customers/' . $customer_id);

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/customers/#edit-put_ecommerce_stores_store_id_customers_customer_id
	public function update_customer($store_id, $customer_id, $data) {
		$result = $this->client->request($this->route . $store_id . '/customers/' . $customer_id, $data, 'PUT');

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/customers/#delete-delete_ecommerce_stores_store_id_customers_customer_id
	public function delete_customer($store_id, $customer_id) {
		$result = $this->client->request($this->route . $store_id . '/customers/' . $customer_id, array(), 'DELETE');

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/customers/#read-get_ecommerce_stores_store_id_customers_customer_id
	public function get_product($store_id, $product_id) {
		$result = $this->client->request($this->route . $store_id . '/products/' . $product_id);

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/products/#create-post_ecommerce_stores_store_id_products
	public function add_product($store_id, $data) {
		$result = $this->client->request($this->route . $store_id . '/products', $data);

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/customers/#delete-delete_ecommerce_stores_store_id_customers_customer_id
	public function delete_product($store_id, $product_id) {
		$result = $this->client->request($this->route . $store_id . '/products/' . $product_id, array(), 'DELETE');

		return $result;
	}

	// http://developer.mailchimp.com/documentation/mailchimp/reference/ecommerce/stores/orders/#create-post_ecommerce_stores_store_id_orders
	public function add_order($store_id, $data) {
		$result = $this->client->request($this->route . $store_id . '/orders', $data);

		return $result;
	}
}