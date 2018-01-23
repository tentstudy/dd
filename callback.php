<?php
    if (!session_id()) {
        session_start();
    }
    require_once __DIR__ . '/vendor/autoload.php';

    $config = require_once(__DIR__ . '/config/app.php');

    $fb = new Facebook\Facebook([
        'app_id' => $config['app_id'],
        'app_secret' => $config['app_secret'],
        'default_graph_version' => 'v2.10',
    ]);
    $helper = $fb->getRedirectLoginHelper();

    try {
        $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        // echo 'Graph returned an error: ' . $e->getMessage();
            header('400 Bad Request');
            echo 'Bad request';
        exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        // echo 'Facebook SDK returned an error: ' . $e->getMessage();
            header('400 Bad Request');
            echo 'Bad request';
        exit;
    }

    if (! isset($accessToken)) {
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

    $shortToken = (string) $accessToken;
    $_SESSION['access_token'] = $shortToken;

    $fb->setDefaultAccessToken($shortToken);

    try {
        $response = $fb->get('/me');
        $userNode = $response->getGraphUser();
        $_SESSION['name'] = $userNode->getName();
        $_SESSION['id'] = $userNode->getId();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        // echo 'Graph returned an error: ' . $e->getMessage();
        header('400 Bad Request');
        echo 'Bad request';
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        // echo 'Facebook SDK returned an error: ' . $e->getMessage();
        header('400 Bad Request');
        echo 'Bad request';
        exit;
    }

    if (! $accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            $_SESSION['access_token'] = (string) $accessToken;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
        }
    }

    header('Location: /');

    // debug token
    // // Get the access token metadata from /debug_token
    // $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    // echo '<h3>Metadata</h3>';
    // var_dump($tokenMetadata);

    // // Validation (these will throw FacebookSDKException's when they fail)
    // $tokenMetadata->validateAppId($config['app_id']);
    // // If you know the user ID this access token belongs to, you can validate it here
    // //$tokenMetadata->validateUserId('123');
    // $tokenMetadata->validateExpiration();