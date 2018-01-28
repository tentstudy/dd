<?php
    /**
     * Created by PhpStorm.
     * User: dangd
     * Date: 1/24/2018
     * Time: 10:13 PM
     */

    if (!session_id()) {
        session_start();
    }

    require_once __DIR__ . '/libs/connect.php'; //have $config variable

    require_once __DIR__ . '/vendor/autoload.php';

    require_once __DIR__ . '/libs/functions.php';

    if (!isset($_SESSION['access_token'])) {
        try {
            $fb = new Facebook\Facebook([
                'app_id' => $config['app_id'],
                'app_secret' => $config['app_secret'],
                'default_graph_version' => 'v2.10',
            ]);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {

            header('400 Bad Request');
            echo 'Bad request';
        }

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl($config['login_call_back'], $permissions);
    }


    $_SESSION['token'] = generateRandomString(12);
    $_SESSION['captcha'] = generateRandomString(10);

    $query = "SELECT * FROM rules ORDER BY RAND() LIMIT 0, 1";

    $result = mysqli_query($conn, $query);

    $rule = $result->fetch_assoc();

    $_SESSION['rule'] = $rule['id'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Điểm danh - TentStudy</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="https://dangdung.xyz/images/favicon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar7">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><img src="images/logo-rec.png" alt="Tent Study">
            </a>
        </div>


        <div id="navbar7" class="navbar-collapse collapse">
            <!--            <ul class="nav navbar-nav navbar-left">
            <li class="active"><a href="/">Home</a></li>
            <li><a href="#">About</a></li>
            </ul>-->
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($_SESSION['access_token'])) : ?>
                    <li class="dropdown user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <img
                                    src="https://graph.facebook.com/<?php echo $_SESSION['id'] ?>/picture?type=large&amp;redirect=true&amp;width=50&amp;height=50"
                                    width="30" height="30"><?php echo $_SESSION['name'] ?> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <!--                        <li><a href="#">Action</a></li>-->
                            <!--                        <li><a href="#">Another action</a></li>-->
                            <!--                        <li><a href="#">Something else here</a></li>-->
                            <!--                        <li class="divider"></li>-->
                            <!--                        <li class="dropdown-header">Nav header</li>-->
                            <!--                        <li><a href="#">Separated link</a></li>-->
                            <li><a href="/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else : ?>
                    <li><a href="<?php echo $loginUrl ?>">Login with Facebook</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
    <!--/.container-fluid -->
</nav>
<div class="container">
    <div class="content">
        <?php if (isset($_SESSION['access_token'])) : ?>
            <div class="rollup">
                <div class="captcha-view"><?php echo $_SESSION['captcha'] ?></div>
                <div class="captcha-rule"><?php echo $rule['text'] ?></div>
                <form method="POST" action="rollup.php">
                    <div class="form-group">
                        <input hidden name="rule" value="<?php echo $rule['id'] ?>" title="rule"/>
                        <input hidden name="token" value="<?php echo $_SESSION['token'] ?>" title="token"/>
                        <input class="form-control" name="captcha" type="text" title="captcha" required>
                    </div>
                    <?php
                        if (isset($_SESSION['success'])) {
                            echo "<div class=\"alert alert-success\">{$_SESSION['success']}</div>";
                            unset($_SESSION['success']);
                        }

                        if (isset($_SESSION['warning'])) {
                            echo "<div class=\"alert alert-warning\">{$_SESSION['warning']}</div>";
                            unset($_SESSION['warning']);
                        }

                        if (isset($_SESSION['error'])) {
                            echo "<div class=\"alert alert-danger\">{$_SESSION['error']}</div>";
                            unset($_SESSION['error']);
                        }
                    ?>
                    <div class="form-group">
                        <button class="btn btn-success" name="btnRollUp">Roll Up</button>
                    </div
                </form>
            </div>
        <?php endif ?>
    </div>
    <?php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $today = date("Ymd");
        $time = date("H:i");

        if ($time < $config['new_day']) $today -= 1;

        $lastSunday = date('Ymd', strtotime('last Sunday', strtotime($today)));

        $nextMonday = date('Ymd', strtotime('next Monday', strtotime($today)));

        $week = [];

        foreach ($days as $day) {
            $id = date('Ymd', strtotime("next {$day}", strtotime($lastSunday)));
            $week[$id] = [];
        }

        $query = "SELECT rollup.*, users.name 
              FROM rollup JOIN users 
              ON id = rollup.user_id 
              WHERE roll_day > {$lastSunday} AND roll_day < {$nextMonday} 
              ORDER BY updated DESC";

        $result = mysqli_query($conn, $query);

        if ($result->num_rows > 0) :
            $data = [];

            while ($row = $result->fetch_assoc()) $data[] = $row;

            $mapUser = [];

            foreach ($data as $row) {
                $user_id = $row['user_id'];
                $name = $row['name'];
                $roll_day = $row['roll_day'];
                $first = $row['first'];
                $last = $row['last'];

                if (!isset($mapUser[$user_id])) {
                    $mapUser[$user_id] = [
                        'name' => $name
                    ];
                }

                if (!isset($week[$roll_day][$user_id])) {
                    $week[$roll_day][$user_id] = [
                        'first' => $first,
                        'last' => $last
                    ];
                } else {
                    $week[$roll_day][$user_id][] = [
                        'first' => $first,
                        'last' => $last
                    ];
                }

            }
            ?>
            <div class="week-report">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th rowspan="2">
                            <?php echo date('d/m', strtotime($lastSunday + 1)) . ' - ' . date('d/m', strtotime($nextMonday - 1)); ?>
                        </th>
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
                    <?php
                        foreach ($mapUser as $user_id => $user) {
                            echo "<tr><td>{$user['name']}</td>";
                            foreach ($week as $day) {
                                if (isset($day[$user_id])) {
                                    $f = $day[$user_id]['first'] ? date("H:i", strtotime($day[$user_id]['first'])) : '';

                                    if ($day[$user_id]['first'] > $config['limit_time']) {
                                        $late = strtotime($day[$user_id]['first']) - strtotime($config['limit_time']);
                                        $late = gmdate('H:i', $late);
                                        $f = "<span title=\"{$late} late\" class=\"cross\">❌</span>" . $f;
                                    }

                                    $l = $day[$user_id]['last'] ? date("H:i", strtotime($day[$user_id]['last'])) : '';

                                    echo "<td>{$f}</td><td>{$l}</td>";
                                } else {
                                    echo "<td></td><td></td>";
                                }

                            }
                            echo "</tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>