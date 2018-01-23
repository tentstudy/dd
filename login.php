<?php
    require_once __DIR__ . '/vendor/autoload.php';

    $config = require_once(__DIR__ . '/config/app.php');

    if (!isset($_SESSION['access_token'])) {
        $fb = new Facebook\Facebook([
            'app_id' => $config['app_id'],
            'app_secret' => $config['app_secret'],
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('https://dd.tentstudy.lien/callback.php', $permissions);
    }
    