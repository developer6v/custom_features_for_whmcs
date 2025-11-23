<?php

add_hook('ClientAreaFooterOutput', 1, function($vars) {

    return '<script>alert("teste");</script>';
});


?>
