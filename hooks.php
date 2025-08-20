<?php
require_once __DIR__ . '/src/Services/cepvalidator.php';
require_once __DIR__ . '/src/Services/cpfcnpj.php';
require_once __DIR__ . '/src/Services/enderecos.php';

if (!defined('WHMCS')) { die('Access denied'); }


// CEP
add_hook('ClientAreaFooterOutput', 1, function($vars) {
    return cepvalidator_script();
});
add_hook('AdminAreaFooterOutput', 1, function($vars) {
    return cepvalidator_script();
});


// CNPJ/CPF
add_hook('ClientAreaFooterOutput', 1, function($vars) {
    return cpfcnpj_script();
});
add_hook('AdminAreaFooterOutput', 1, function($vars) {
    return cpfcnpj_script();
});




// Endereço
add_hook('ClientAreaFooterOutput', 1, function($vars) {
    return enderecos();
});