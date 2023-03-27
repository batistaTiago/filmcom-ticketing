<?php

if (!function_exists('ddj')) {
    function ddj($arg) {
        dd(json_encode($arg, JSON_PRETTY_PRINT));
    }
}
