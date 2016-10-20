<?php
namespace Moip;
abstract class MoipAPI {
	public function __construct($client) {
		$this->client = $client;
	}
}