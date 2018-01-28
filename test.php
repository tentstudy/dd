<?php
    /**
     * Created by PhpStorm.
     * User: dangd
     * Date: 1/27/2018
     * Time: 10:55 AM
     */

    $today = date("Ymd", strtotime('01.01.2018'));

    echo date('d/m', strtotime($today - 1));