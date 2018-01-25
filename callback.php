<?php
if (!session_id()) {
    session_start();
}

require_once __DIR__ . '/libs/connect.php'; //have $config variable

require_once __DIR__ . '/vendor/autoload.php';

try {
    $fb = new Facebook\Facebook([
        'app_id' => $config['app_id'],
        'app_secret' => $config['app_secret'],
        'default_graph_version' => 'v2.10',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    header('400 Bad Request');
    echo 'Bad request';
    exit;
}

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    header('400 Bad Request');
    echo 'Bad request';
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    header('400 Bad Request');
    echo 'Bad request';
    exit;
}

if (!isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
}

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

$shortToken = (string)$accessToken;
$_SESSION['access_token'] = $shortToken;

$fb->setDefaultAccessToken($shortToken);

try {
    $response = $fb->get('/me');
    $userNode = $response->getGraphUser();
    $_SESSION['name'] = $userNode->getName();
    $_SESSION['id'] = $userNode->getId();

    $query = "SELECT * FROM users WHERE id = '{$_SESSION['id']}'";
    $result = mysqli_query($conn, $query);

    if ($result->num_rows === 0) { //new user
        $query = "INSERT INTO users (id, name) VALUES ('{$_SESSION['id']}', '{$_SESSION['name']}')";
        $check = mysqli_query($conn, $query);
    } else {
        $query = "UPDATE users SET name = '{$_SESSION['name']}' WHERE id = '{$_SESSION['id']}}'";
        $check = mysqli_query($conn, $query);
    }
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    header('400 Bad Request');
    echo 'Bad request';
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    header('400 Bad Request');
    echo 'Bad request';
    exit;
}

if (!$accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        $_SESSION['access_token'] = (string)$accessToken;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
    }
}

header('Location: /');