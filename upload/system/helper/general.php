<?php
function token($length = 32) {
	// Create random token
	$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

	$max = strlen($string) - 1;

	$token = '';

	for ($i = 0; $i < $length; $i++) {
		$token .= $string[mt_rand(0, $max)];
	}

	return $token;
}


function pr($array,$label="", $vardump=false){
	$backtrace = debug_backtrace();
	echo'<pre>';
	if($label<>""){ echo $label.' on '.$backtrace[0]['file']."  line ".$backtrace[0]['line'].'<br>'; }
	if($vardump){ 
		var_dump($array);
	}else{
		print_r($array);
	}
	echo'</pre>';
}

function checkRequestOrigin($host) {
	return ($host == parse_url(HTTP_SERVER, PHP_URL_HOST));
}

function ORDER_STATUS($order_status_key){
	$statuses =  array('canceled' => 7,'canceled_reversal' => 9,'chargeback' => 13,'complete' => 5,'denied' => 8 ,'expired' =>14,'failed' => 10,'pending' => 1,'processed' => 15,'processing' => 2,'refunded' => 11,'reversed' => 12,'voided' =>16 );
	return $statuses[$order_status_key];
}