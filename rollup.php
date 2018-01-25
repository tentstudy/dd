<?php
/**
 * Created by PhpStorm.
 * User: dangd
 * Date: 1/24/2018
 * Time: 10:13 PM
 */

require_once __DIR__ . '/libs/connect.php';

require_once __DIR__ . '/libs/functions.php';

if (!session_id()) {
    session_start();
}

if (isset($_POST['btnRollUp']) && isset($_SESSION['access_token'])) {
    $rule = htmlspecialchars($_POST['rule']);
    $token = $_POST['token'];
    $captcha = $_POST['captcha'];

    if ($token !== $_SESSION['token']) {
        header("Location: /");

        return;
    }

    $query = "SELECT * FROM rules WHERE id = '{$rule}'";

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

    $today = date("Ymd");
    $time = date("H:i");

    if ($time < $config['new_day']) $today -= 1;

    $query = "SELECT * FROM rollup WHERE user_id = '{$_SESSION['id']}' AND roll_day = {$today}";

    $result = mysqli_query($conn, $query);

    //first time
    if ($result->num_rows === 0) {
        $query = "INSERT INTO rollup (user_id, roll_day, first) VALUES ('{$_SESSION['id']}', {$today}, '{$time}')";

        $check = mysqli_query($conn, $query);

        if (!$check) {
            $_SESSION['error'] = 'Cannot roll up now, try again later';
            header('Location: /');

            return;
        }

        $_SESSION['success'] = 'Roll up successfully';
        header('Location: /');

        return;
    }
    //last time
    $query = "UPDATE rollup SET last = '{$time}' WHERE user_id = '{$_SESSION['id']}' AND roll_day = {$today}";

    $check = mysqli_query($conn, $query);

    if (!$check) {
        $_SESSION['error'] = 'Cannot roll up now, try again later';
        header('Location: /');

        return;
    }

    $_SESSION['success'] = 'Roll up successfully';
    header('Location: /');

    return;
}

$conn->close();
header('Location: /');