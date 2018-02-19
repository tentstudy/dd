<?php
	if (!session_id()) {
		session_start();
	}

	$block_config = require_once __DIR__ . '/../config/block.php';

	$block_time = $block_config['block_time'];
	$block_count = $block_config['block_count'];

	if (time() - $_SESSION['call_api_last'] < $block_time && $_SESSION['call_api_count'] > $block_count) {
		$until_time = $_SESSION['call_api_last'] + $block_time;

		echo json_encode([
			'success' => false,
			'message' => "Blocked until {$until_time}"
		]);

		exit();
	}

	if (time() - $_SESSION['call_api_last'] > $block_time) {
		$_SESSION['call_api_count'] = 1;
	} else {
		$_SESSION['call_api_count'] += 1;
	}

	$_SESSION['call_api_last'] = time();