<?php
require_once __DIR__ . '/src/Services/cepvalidator.php';
require_once __DIR__ . '/src/Services/cpfcnpj.php';
require_once __DIR__ . '/src/Services/enderecos.php';
require_once __DIR__ . '/src/Services/Domain/domain.php';
require_once __DIR__ . '/src/Services/hideFieldsCheckout.php';
require_once __DIR__ . '/src/Services/registerNumber.php';
require_once __DIR__ . '/src/Config/assets.php';

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

// Erro 129
add_hook('AfterRegistrarRegistrationFailed', 1, function($vars) {
    domain_manager($vars);
});



add_hook('ClientAreaFooterOutput', 1, function($vars) {
    if (strpos($_SERVER['REQUEST_URI'], 'hospedam-dedicada/teste-otavioi') ) {
        return hidefields();
    }
});

?>