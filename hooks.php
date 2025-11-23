<?php
require_once __DIR__ . '/src/Services/frontend-manipulation/index.php';
require_once __DIR__ . '/src/Config/assets.php';

if (!defined('WHMCS')) { die('Access denied'); }

// Função auxiliar para checar se está no checkout
function isCheckoutCartPage() {
    return (strpos($_SERVER['REQUEST_URI'], 'cart.php') !== false);
}

function isCheckoutCartPageConfig() {
    return (isset($_GET['a']) && $_GET['a'] === 'confdomains') || 
           (strpos($_SERVER['REQUEST_URI'], 'cart.php?a=confdomains') !== false);
}

function isCheckoutOrderPage() {
    return (strpos($_SERVER['REQUEST_URI'], "/order/") !== false);
}

// CEP
add_hook('ClientAreaFooterOutput', 1, function($vars) {
    if (isCheckoutCartPage()) {
        return cepvalidator_script_cart();
    }
    if (isCheckoutOrderPage()) {
        return cepvalidator_script();
    }
    return cepvalidator_script();
});


add_hook('AdminAreaFooterOutput', 1, function($vars) {
    if (isCheckoutCartPage()) {
        return cepvalidator_script_cart();
    }
    if (isCheckoutOrderPage()) {
        return cepvalidator_script();
    }
    return cepvalidator_script();
});

// CNPJ/CPF
add_hook('ClientAreaFooterOutput', 1, function($vars) {

    $out = '<script>alert("teste");</script>';
    return $out;
});
add_hook('AdminAreaFooterOutput', 1, function($vars) {
    return cpfcnpj_script_admin();
});

// Endereço
add_hook('ClientAreaFooterOutput', 1, function($vars) {
    if (isCheckoutCartPage()) {
        return enderecos_cart();
    }
    if (isCheckoutOrderPage()) {
        return enderecos();
    }
    return enderecos();
});

// CSS - Client
add_hook('ClientAreaFooterOutput', 1, function($vars) {
    
    return assetsClient();
});

// Login ou Criar Conta
add_hook('ClientAreaFooterOutput', 1, function($vars) {

    return loginOrCreate();
});

// Erro 129
add_hook('AfterRegistrarRegistrationFailed', 1, function($vars) {
    domain_manager($vars);
});

// Sucesso
add_hook('AfterRegistrarRegistration', 1, function($vars) {
    domain_successful($vars);
});

// Exemplo para outra URL específica
add_hook('ClientAreaFooterOutput', 1, function($vars) {
    if (strpos($_SERVER['REQUEST_URI'], 'hospedam-dedicada/teste-otavioi') !== false) {
        return hidefields();
    }
});

// RegisterNumber
add_hook('ClientAreaFooterOutput', 1, function($vars) {

    return registerNumber();
});


?>
