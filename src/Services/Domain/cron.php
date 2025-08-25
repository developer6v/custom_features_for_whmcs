<?php

include_once('../../../../../../init.php'); // Inclui a inicialização do WHMCS

use WHMCS\Database\Capsule;

// Função para tentar registrar o domínio
function tryRegisterDomain($domain_id, $domain_name, $client_id) {
    $params = [
        'action' => 'DomainRegister',
        'domain' => $domain_name,
        'clientid' => $client_id,
    ];

    // Enviar comando para o WHMCS Registrar Domínio
    $result = localAPI('DomainRegister', $params);

    // Verificar o retorno
    if ($result['result'] == 'success') {
        // Registro bem-sucedido
        Capsule::table('sr_cf_domain_error_129')
            ->where('domain_id', $domain_id)
            ->update(['status' => 1]);
    } else {
        // Falha no registro
        Capsule::table('sr_cf_domain_error_129')
            ->where('domain_id', $domain_id)
            ->update(['status' => 0]); // Marca como falha
    }

    return $result;
}

// Função para processar todos os domínios
function processDomains() {
    // Obter configuração da tabela sr_cf_config
    $config = Capsule::table('sr_cf_config')->where('id', 1)->first();
    $max_trials = $config->max_trials;
    $interval_between_trials = $config->interval_between_trials;

    // Buscar domínios com status '0' (falha ou aguardando)
    $domains = Capsule::table('sr_cf_domain_error_129')
        ->where('status', 0) // 0 significa que falhou ou está aguardando
        ->get();

    // Processar cada domínio
    foreach ($domains as $domain) {
                    echo 'dominio encontra'; 

        // Verificar se já excedeu o intervalo de tentativas
        $last_try_time = strtotime($domain->updated_at); // Data da última tentativa
        $current_time = time(); // Hora atual
        $time_difference = ($current_time - $last_try_time) / 60; // Diferença em minutos

        // Se o intervalo entre tentativas for suficiente, tentar registrar
        if ($time_difference >= $interval_between_trials) {
            echo 'diferença suficientre'; 
            // Verifica o número de tentativas
            if ($domain->trials < $max_trials) {
                // Tentar registrar o domínio
                $result = tryRegisterDomain($domain->domain_id, $domain->domain, $domain->client_id);
            } 
        } else {
            echo 'diferença insuficiente'; 
        }
    }
}

// Executar a função de processamento de domínios
processDomains();

