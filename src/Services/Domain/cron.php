<?php

include_once('../../../../../../init.php'); // Inclui a inicialização do WHMCS

use WHMCS\Database\Capsule;

function tryRegisterDomain($domain_id, $domain_name, $client_id) {
    // Verifica o status do domínio usando a API DomainWhois
    $whois_params = [
        'action' => 'DomainWhois',
        'domain' => $domain_name,
    ];

    $whois_result = localAPI('DomainWhois', $whois_params);
    
    // Se o domínio já estiver registrado, retorna sem tentar registrar
    if ($whois_result['result'] == 'success' && $whois_result['status'] == 'unavailable') {
        echo "O domínio $domain_name já está registrado. Não tentaremos registrar novamente.\n";
        
        // Atualiza o `updated_at` mesmo que não tenha sido registrado
        Capsule::table('sr_cf_domain_error_129')
            ->where('domain_id', $domain_id)
            ->update(['updated_at' =>  date('Y-m-d H:i:s')]); // Atualiza o horário da última tentativa

        return false;
    }

    // Caso contrário, tenta registrar o domínio
    $params = [
        'action' => 'DomainRegister',
        'domain' => $domain_name,
        'clientid' => $client_id,
    ];

    $result = localAPI('DomainRegister', $params);
    if ($result['result'] == 'success') {
        // Registro bem-sucedido
        Capsule::table('sr_cf_domain_error_129')
            ->where('domain_id', $domain_id)
            ->update([
                'status' => 1,
                'updated_at' =>  date('Y-m-d H:i:s')  // Atualiza o horário após o sucesso
            ]);
    } else {
        // Atualiza o status para 0 (falha)
        Capsule::table('sr_cf_domain_error_129')
            ->where('domain_id', $domain_id)
            ->update([
                'status' => 0,
                'updated_at' =>  date('Y-m-d H:i:s') // Atualiza o horário após a falha
            ]);
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
        echo 'dominio encontrado'; 

        // Verificar se já excedeu o intervalo de tentativas
        $last_try_time = strtotime($domain->updated_at); // Data da última tentativa
        $current_time = time(); // Hora atual
        $time_difference = ($current_time - $last_try_time) / 60; // Diferença em minutos

        // Se o intervalo entre tentativas for suficiente, tentar registrar
        if ($time_difference >= $interval_between_trials) {
            echo 'diferença suficiente'; 
            // Verifica o número de tentativas
            if ($domain->trials < $max_trials) {
                // Tentar registrar o domínio
                $result = tryRegisterDomain($domain->domain_id, $domain->domain, $domain->client_id);
                if (!$result) {
                    echo "O domínio $domain->domain já foi registrado, não tentando novamente.\n";
                }
            } 
        } else {
            echo 'diferença insuficiente'; 
        }
    }
}

// Executar a função de processamento de domínios
processDomains();
