<?php

function assets() {
    $assets = '<link rel="stylesheet" type="text/css" href="' . url('modules/addons/custom_features_for_whmcs/public/css/style.css') . '">';
    $assets .= '<script type="text/javascript" src="' . url('modules/addons/custom_features_for_whmcs/public/js/script.js') . '"></script>';
    return $assets;
}
