<?php
function assets() {
    $base_url = 'https://' . $_SERVER['HTTP_HOST'] . '/modules/addons/custom_features_for_whmcs/public';
    
    $assets = '<link rel="stylesheet" type="text/css" href="' . $base_url . '/css/style.css' . '">';
    $assets .= '<script type="text/javascript" src="' . $base_url . '/js/script.js' . '"></script>';
    return $assets;
}