<?php
require_once __DIR__ . '/src/Services/cepvalidator.php';
require_once __DIR__ . '/src/Services/cpfcnpj.php';
require_once __DIR__ . '/src/Services/enderecos.php';
require_once __DIR__ . '/src/Services/Domain/domain.php';

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


function add_cep_mask_script($vars) {
    // Verifica se a página atual é a de checkout ou onde o campo CEP está presente
    if ($vars['filename'] == 'order' || $vars['filename'] == 'cart') {
        // Adiciona o script do jQuery Mask
        $maskScript = '
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
            <script>
            jQuery(document).ready(function($) {
                // Aplica a máscara de CEP
                $("input[name=\'postcode\']").mask("00000-000");
            });
            </script>
        ';
        // Retorna o script para ser inserido na página
        return $maskScript;
    }
}

// Registra o hook para adicionar o script no cabeçalho
add_hook('ClientAreaHeadOutput', 1, 'add_cep_mask_script');

// Registra o hook para adicionar o script no corpo
add_hook('ClientAreaHeaderOutput', 1, 'add_cep_mask_script');
?>
