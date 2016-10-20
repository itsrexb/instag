<?php
header('Access-Control-Allow-Origin: *');

// Tracking Code (30 days)
if (isset($_GET['tracking'])) {
	setcookie('tracking', $_GET['tracking'], time() + 3600 * 24 * 30, '/');
}

// External Affiliate ID (30 days)
if (isset($_GET['aff_id'])) {
	setcookie('ext_aff_id', $_GET['aff_id'], time() + 3600 * 24 * 30, '/');
}