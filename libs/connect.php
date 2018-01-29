<?php
/**
 * Created by PhpStorm.
 * User: dangd
 * Date: 1/25/2018
 * Time: 7:16 AM
 */

date_default_timezone_set("Asia/Ho_Chi_Minh");

$config = require_once __DIR__ . '/../config/app.php';

$conn = new mysqli($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);

if ($conn->errno) {
//    header('500 Internal Server Error');
    die('500 Internal Server Error');
}

mysqli_set_charset($conn, 'utf8');