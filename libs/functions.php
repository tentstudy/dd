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

function generateCaptcha($captcha, $ruleText) {
    $capImg = ImageCreateFromJpeg(__DIR__ . '/../images/captcha/base.jpg');

    $capColor= ImageColorAllocate($capImg, 0, 0, 0);

    $capFont = __DIR__ . '/../fonts/frankin.ttf';

    $capFontSize = "28";

    $capStartPos = getStartPoint($captcha, 23, 280);

    ImagettfText(
        $capImg, $capFontSize, 0, $capStartPos, 35, $capColor,
        $capFont, $captcha
    );

    $arrRuleText = splitStringToMultiLine($ruleText, 28);

    $textFont = __DIR__ . '/../fonts/arialItalic.ttf';

    $textColor = ImageColorAllocate($capImg, 0, 0, 255);

    $y = 60;

    foreach ($arrRuleText as $line) {
        ImagettfText(
            $capImg, "12", 0,
            getStartPoint($line, '8', 280),
            $y, $textColor, $textFont, $line);
        $y += 20;
    }

    ob_start();
    imagepng($capImg);
    $bin = ob_get_clean();

    $src =  base64_encode($bin);
    ImageDestroy($capImg);

    return "data:image/png;base64,{$src}";
}

function lowerupper($str)
{
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] > 'Z') $str[$i] = strtoupper($str[$i]);
        else $str[$i] = strtolower($str[$i]);
    }
    return $str;
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomNumber($length = 6)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getStartPoint($string, $lenPerChar, $maxWidth) {
    $len = strlen(stripUnicode($string));
    $mid = $len / 2 * $lenPerChar;
    return $maxWidth / 2 - $mid;
}

function splitStringToMultiLine($string, $lenOfLine) {
    $res = array();
    $converted = stripUnicode($string);
    $string = explode(' ', $string);
    $converted = explode(' ', $converted);
    $j = 0;
    $max = count($string);
    while ($j < $max) {
        $curLen = 0;
        $str = '';
        while ($j < $max && $curLen + strlen($converted[$j]) < $lenOfLine) {
            $curLen += strlen($converted[$j]);
            $str .= $string[$j] . ' ';
            $j++;
        }
        array_push($res, $str);
    }
    return $res;
}

function stripUnicode($str)
{
    if (!$str) {
        return false;
    }

//    $ascii = explode('|', 'A|B|C|D|E|G|H|I|K|L|M|N|O|U|M|N|P|Q|R|S|V|T|X|Y');
    $unicode = array(
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'd' => 'đ',
        'D' => 'Đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
    );

    foreach ($unicode as $khongdau => $codau) {
        $arr = explode("|", $codau);
        $str = str_replace($arr, $khongdau, $str);
    }
    return $str;
}

