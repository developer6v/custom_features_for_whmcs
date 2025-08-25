<?php 

use WHMCS\Database\Capsule;

function cf_config_database () {
    if (!Capsule::schema()->hasTable('sr_cf_config')) {
        Capsule::schema()->create('sr_cf_config', function ($table) {
            $table->increments('id'); 
            $table->integer('max_trials')->default(0); 
            $table->integer('interval_between_trials')->default(0); 
            $table->boolean('openTicketAfterTrials')->default(false);
        });
        Capsule::table("sr_cf_config")->insert([
            "max_trials" => 3,
            "interval_between_trials" => 5,
            "openTicketAfterTrials" => false
        ]);
    }


    
    if (!Capsule::schema()->hasTable('sr_cf_domain_error_129')) {
        Capsule::schema()->create('sr_cf_domain_error_129', function ($table) {
            $table->increments('id'); 
            $table->integer('domain_id'); 
            $table->integer('trials'); 
            $table->integer('status'); 
            $table->integer('client_id'); 
            $table->string('domain', 255);
            $table->timestamps(); 
        });
    }
}

?>