<?php
/**
 * Created by PhpStorm.
 * User: dangd
 * Date: 1/24/2018
 * Time: 10:09 PM
 */

/**
 * @param int $length
 * @return string
 */

function lowerupper($str) {
    for($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] > 'Z') $str[$i] = strtoupper($str[$i]);
        else $str[$i] = strtolower($str[$i]);
    }
    return $str;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomNumber($length = 6) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

