<?php

/*
|--------------------------------------------------------------------------
| Generate without 4
|--------------------------------------------------------------------------
|
*/

if (!function_exists('generateNumeric')) {
    function generateNumeric($length = 8)
    {
        $codeAlphabet = "012356789";
        $max = strlen($codeAlphabet);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $code;
    }
}

/*
|--------------------------------------------------------------------------
| Generate numeric all
|--------------------------------------------------------------------------
|
*/
if (!function_exists('generateNumericAll')) {
    function generateNumericAll($length = 8)
    {
        $codeAlphabet = "0123456789";
        $max = strlen($codeAlphabet);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $code;
    }
}

/*
|--------------------------------------------------------------------------
| Generate Char
|--------------------------------------------------------------------------
|
*/
if (!function_exists('generateChar')) {
    function generateChar($length = 8)
    {
        $codeAlphabet = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $max = strlen($codeAlphabet);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $code;
    }
}

/*
|--------------------------------------------------------------------------
| Generate Char
|--------------------------------------------------------------------------
|
*/
if (!function_exists('generateAllChar')) {
    function generateAllChar($length = 8)
    {
        $codeAlphabet = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $max = strlen($codeAlphabet);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $code;
    }
}

/*
|--------------------------------------------------------------------------
| Get Backend Asset Base URL
|--------------------------------------------------------------------------
|
*/
if (!function_exists('getAsset')) {
    function getAsset($path)
    {
        $URL = asset('public/assets/' . $path);
        return $URL;
    }
}

/*
|--------------------------------------------------------------------------
| Get Frontend Asset Base URL
|--------------------------------------------------------------------------
|
*/
if (!function_exists('getAssetBaseFrontend')) {
    function getAssetBaseFrontend($path)
    {
        $URL = asset('public/' . $path);
        return $URL;
    }
}

/*
|--------------------------------------------------------------------------
| Time from Second mysql
|--------------------------------------------------------------------------
|
| Return time H:i:s from second mysql
|
*/
if (!function_exists('convertSecondToTime')) {
    function convertSecondToTime($second)
    {
        if ($second < 0) $second = $second * -1;
        $hours = floor($second / 3600);
        $minutes = floor(($second / 60) % 60);

        return $hours . ' Jam ' . $minutes . ' Menit';
    }
}

if(!function_exists('isActiveRoute')) {
    function isActiveRoute($route, $output = "active")
    {
        if (Route::currentRouteName() == $route) return $output;
    }
}

if(!function_exists('areActiveRoutes')) {
    function areActiveRoutes(Array $routes, $output = "selected")
    {
        foreach ($routes as $route) {
            if (strpos(Request::url(), $route['url'])) return $output;
        }
    }
}