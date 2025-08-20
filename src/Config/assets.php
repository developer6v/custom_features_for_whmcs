<?php


function assets() {
    $assets = '<link rel="stylesheet" type="text/css" href="' . __DIR__ . '/../../public/css/style.css' . '">';
    $assets .= '<script type="text/javascript" src="' . __DIR__ . '/../../public/js/script.js' . '"></script>';
    return $assets;
}