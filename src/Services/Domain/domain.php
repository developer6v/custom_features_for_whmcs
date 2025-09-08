<?php
use WHMCS\Database\Capsule;


function domain_manager($vars) {
    if ($vars['error'] == '129') {

        $domain_id = $vars['params']['domainid'];
        $domain_name = $vars['params']['sld'] . '.' . $vars['params']['tld'];  
        $client_id = $vars['params']['userid']; 

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
                        'trials' => $existingDomain->trials + 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } else {
            Capsule::table('sr_cf_domain_error_129')->insert([
                'domain_id' => $domain_id,
                'trials' => 1,
                'status' => 0,
                'client_id' => $client_id,
                'domain' => $domain_name,
                'created_at' => date('Y-m-d H:i:s'), 
                
            ]);
        }
    }
}




function openTicket($vars) {
    $client_id = $vars['params']['userid'];
    $subject = "-";
    $message = "-";
    
    $command = 'OpenTicket';
    $postData = array(
        'deptid' => '1',
        'subject' => $subject,
        'message' => $message,
        'clientid' => $client_id,
        'admin' => true,
        'name' => "Stay Cloud - Suporte Técnico",
        'priority' => 'High',
        'markdown' => true
    );
    
    $result = localAPI($command, $postData);

   
}


function markAsCancelled () {

}