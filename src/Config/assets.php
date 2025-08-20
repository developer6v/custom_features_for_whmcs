<?php
function assets() {
    $base_url = 'https://' . $_SERVER['HTTP_HOST'] . '/modules/addons/custom_features_for_whmcs/public';
    
    $version = time(); // versão baseada na hora atual, muda a cada acesso

    $assets = '<link rel="stylesheet" type="text/css" href="' . $base_url . '/css/style.css?v=' . $version . '">';
    $assets .= '<script type="text/javascript" src="' . $base_url . '/js/script.js?v=' . $version . '"></script>';
    
    return $assets;
}
?>
