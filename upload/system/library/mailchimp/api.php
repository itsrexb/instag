<?php
namespace MailChimp;
abstract class MailChimpAPI {
	public function __construct($client) {
		$this->client = $client;
	}
}