<?php
    /**
     * Created by PhpStorm.
     * User: dangd
     * Date: 1/24/2018
     * Time: 10:13 PM
     */

    require_once __DIR__ . '/libs/connect.php';

    require_once __DIR__ . '/libs/functions.php';

    require_once __DIR__ . '/models/RollUp.php';

    if (!session_id()) {
        session_start();
    }

    if (!isset($_POST['btnRollUp']) || !isset($_SESSION['access_token'])) {
        $conn->close();
        header('Location: /');

        return;
    }

    $token = $_POST['token'];
    $captcha = $_POST['captcha'];

    if ($token !== $_SESSION['token'] ) {
        header("Location: /");

        return;
    }

    $query = "SELECT * FROM rules WHERE id = '{$_SESSION['rule']}'";

    $result = mysqli_query($conn, $query);

    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Rule not found';
        header('Location: /');

        return;
    }

    $ruleData = $result->fetch_assoc();
    $ruleFunctions = explode(',', $ruleData['functions']);
    $res = $_SESSION['captcha'];

    foreach ($ruleFunctions as $function) {
        $res = $function($res);
    }

    if ($res !== $captcha) {
        $_SESSION['error'] = 'Captcha does not match';
        header('Location: /');

        return;
    }

    $result = rollup($conn, $config['new_day'], $config['limit_time'], $_SESSION['id']);

    $_SESSION['warning'] = $result['warning'] ?? '';
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }

    header('Location: /');