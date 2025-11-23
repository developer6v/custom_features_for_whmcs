<?php
require_once __DIR__ . '/src/Services/frontend-manipulation/index.php';
require_once __DIR__ . '/src/Config/assets.php';


add_hook('ClientAreaFooterOutput', 1, function($vars) {

    return '<script>alert("teste");</script>';
});



?>
