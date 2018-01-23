<?php
    if (!session_id()) {
        session_start();
    }

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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DD TentStudy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="images/logo-rec.png" alt="">
        </div>
        <hr style="max-width: 150px;">
        <div class="content">
            <?php if (!isset($_SESSION['access_token'])) : ?>
                <a href="<?php echo $loginUrl ?>" class="btn btn-primary">Log in with Facebook!</a>
            <?php else : ?>
                <div class="profile">
                    <div class="avatar-container">
                        <img 
                            src="https://graph.facebook.com/<?php echo $_SESSION['id'] ?>/picture?type=large&amp;redirect=true&amp;width=50&amp;height=50" width="50" height="50">
                    </div>
                    <div class="info-container">
                        <div class="name"><?php echo $_SESSION['name'] ?></div>
                        <div class="action">
                            [<a href="/logout.php">Logout</a>]
                        </div>
                    </div>
                </div>
                <div class="rollup">
                    
                </div>
            <?php endif ?>
        </div>
        <div class="week-report">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">28/1 - 3/2</th>
                        <th colspan="2">Mon</th>
                        <th colspan="2">Tue</th>
                        <th colspan="2">Wed</th>
                        <th colspan="2">Thu</th>
                        <th colspan="2">Fri</th>
                        <th colspan="2">Sat</th>
                        <th colspan="2">Sun</th>
                    </tr>
                    <tr>
                        <th>First</th>
                        <th>Last</th>
                        <th>First</th>
                        <th>Last</th>
                        <th>First</th>
                        <th>Last</th>
                        <th>First</th>
                        <th>Last</th>
                        <th>First</th>
                        <th>Last</th>
                        <th>First</th>
                        <th>Last</th>
                        <th>First</th>
                        <th>Last</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nguyen Dang Dung</td>
                        <td>6.00</td>
                        <td>23.00</td>
                        <td>6.00</td>
                        <td>23.00</td>
                        <td>6.00</td>
                        <td>23.00</td>
                        <td>6.00</td>
                        <td>23.00</td>
                        <td>6.00</td>
                        <td>23.00</td>
                        <td>6.00</td>
                        <td>23.00</td>
                        <td>6.00</td>
                        <td>23.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>