<?php
require_once __DIR__ . '/src/Services/frontend-manipulation/index.php';
require_once __DIR__ . '/src/Config/assets.php';

if (!defined('WHMCS')) {
    logActivity("whmcs n definido");
    die('Access denied');
}
    logActivity("whmcs definido");

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



add_hook('AdminAreaFooterOutput', 1, function($vars) {
    $out = cpfcnpj_script_admin();
    $out .=  cepvalidator_script();
});




// Erro 129
add_hook('AfterRegistrarRegistrationFailed', 1, function($vars) {
    domain_manager($vars);
});

// Sucesso
add_hook('AfterRegistrarRegistration', 1, function($vars) {
    domain_successful($vars);
});

add_hook('ClientAreaFooterOutput', 1, function($vars) {

    $out = "";

    // CEP
    if (isCheckoutCartPage()) {
        $out .= cepvalidator_script_cart();
    } elseif (isCheckoutOrderPage()) {
        $out .= cepvalidator_script();
    } else {
        $out .= cepvalidator_script();
    }

    // CPF/CNPJ
    if (isCheckoutCartPageConfig()) {
        $out .= cpfcnpj_domain_script();
    } elseif (isCheckoutCartPage()) {
        $out .= cpfcnpj_script_cart();
    } elseif (isCheckoutOrderPage()) {
        $out .= cpfcnpj_script();
    } else {
        $out .= cpfcnpj_script();
    }

    // Endereço
    if (isCheckoutCartPage()) {
        $out .= enderecos_cart();
    } elseif (isCheckoutOrderPage()) {
        $out .= enderecos();
    } else {
        $out .= enderecos();
    }

    // Login/Create
    $out .= loginOrCreate();

    // CSS
    $out .= assetsClient();

    // Registrar Número
    $out .= cpfcnpj_script();

    // HideFields (se necessário)
    if (strpos($_SERVER['REQUEST_URI'], 'hospedam-dedicada/teste-otavioi') !== false) {
        $out .= hidefields();
    }

    return $out;
});



?>
