<?php

	/**
	 * /api/rollup.php
	 * Roll up API
	 */
	
	session_start();

	header('Content-Type: application/json');

	require_once __DIR__ . '/block.php';

	
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		header("Status: 405 Method Not Allowed");
		exit();
	}

	require_once __DIR__ . '/../libs/connect.php';

    require_once __DIR__ . '/../models/RollUp.php';

	if (empty($_POST['token']))  {
		echo json_encode([
			'success' => false,
			'message' => 'missing token'
		]);
		exit();
	}

	$token = htmlspecialchars($_POST['token']);

	$query = "SELECT * FROM api_key WHERE token = '{$token}'";

	$result = mysqli_query($conn, $query);

	if ($result->num_rows === 0) {
		header("Status: 403 Forbidden");
		exit();
	}

	$user = $result->fetch_assoc();

	echo json_encode(rollup($conn, $config['new_day'], $config['limit_time'], $user['user_id']));

	
