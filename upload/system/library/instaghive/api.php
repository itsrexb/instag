<?php
namespace instagHive;
abstract class instagHiveAPI {
	public function __construct($client) {
		$this->client = $client;
	}
}