<?php
use WHMCS\Database\Capsule;


function domain_manager($vars) {
    if ($vars['error'] == '129') {

        $domain_id = $vars['params']['domainid'];
        $domain_name = $vars['params']['sld'] . '.' . $vars['params']['tld'];  
        $client_id = $vars['params']['clientid']; 

        $existingDomain = Capsule::table('sr_cf_domain_error_129')
            ->where('domain_id', $domain_id)
            ->where('status', 0)
            ->first();


        $config = Capsule::table('sr_cf_config')
            ->where('id', 1)
            ->first();

        $shouldOpenTicket = $config->openTicketAfterTrials;



        if ($existingDomain) {
            if ($existingDomain->trials >= $config->max_trials) {
                if ($shouldOpenTicket) {
                    openTicket($vars);
                }

                Capsule::table('sr_cf_domain_error_129')
                    ->where('domain_id', $domain_id)
                    ->delete();

            } else {
                Capsule::table('sr_cf_domain_error_129')
                    ->where('domain', $domain_name)
                    ->update([
                        'trials' => $existingDomain->trials + 1
                ]);
            }
        } else {
            Capsule::table('sr_cf_domain_error_129')->insert([
                'domain_id' => $domain_id,
                'trials' => 1,
                'status' => 0,
                'client_id' => $client_id,
                'domain' => $domain_name
            ]);
        }
    }
}




function openTicket($vars) {
    $client_id = $vars['params']['clientid'];
    $subject = "Saldo a ser convertido como crédito";
    $message = "O número de tentativas para o domínio {$vars['params']['sld']}.{$vars['params']['tld']} foi excedido. A equipe precisa converter o saldo como crédito.";
    
    // Parâmetros do ticket
    $ticketParams = [
        'userid' => $client_id,
        'subject' => $subject,
        'message' => $message,
        'priority' => 'High', 
        'status' => 'Open', 
        'department' => 1
    ];
    $result = localAPI('OpenTicket', $ticketParams);
    if ($result['result'] == 'success') {
        logModuleCall('domain_manager', 'openTicket', $ticketParams, $result);
    } else {
        logModuleCall('domain_manager', 'openTicket_error', $ticketParams, $result);
    }
}
