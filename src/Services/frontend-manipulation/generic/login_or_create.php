<?php
function loginOrCreate() {
return <<<'HTML'
    <script>
    window.__checkout = window.__checkout || {
        cep:false,
        doc:false,
        company:true,
        login:false
    };

    // Função para reavaliar o estado do checkout
    window.__recomputeCheckout = function() {
        const g = window.__checkout;
        // A lógica agora considera o login como maiorial
        const disabled = !(g.login) && !(g.cep && g.doc && g.company);  // Considera login como prioridade

        document.querySelectorAll('button#checkout, #place_order')
            .forEach(b => b.disabled = disabled);
    };
    jQuery(function() {
    // Verifica periodicamente se o campo de "Login de clientes atuais" foi selecionado
    var checkExist = setInterval(function() {
        // Radio buttons existentes
        var $loginRadio = jQuery('#loginUser input[type="radio"][value="loginOption"]'); // Radio button "Login de clientes atuais"
        var $accountID = jQuery('#account_id'); // Radio button "Login de clientes atuais"
        var $accountID2 = jQuery('input[name="account_id"]'); // Radio button "Login de clientes atuais"
        
        // Novo radio button de Login de clientes atuais
        var $existingAccountRadio = jQuery('input[type="radio"][name="custtype"][value="existing"]');

        // Verifica se algum dos campos foi encontrado
        if ($loginRadio.length || $accountID.length || $accountID2.length || $existingAccountRadio.length) {
            console.log("Algum dos account ids foi encontrado");

            // Verifica se o radio button "Login de clientes atuais" foi marcado de diferentes fontes
            if ($loginRadio.prop('checked') || $accountID.prop('checked') || $accountID2.prop('checked') || $existingAccountRadio.prop('checked')) {
                window.__checkout.login = true;  // Marca como login de cliente atual
            } else {
                window.__checkout.login = false;  // Caso contrário, marca como falso
            }

            // Recalcula o estado do checkout
            window.__recomputeCheckout();
        }
    }, 100);  // Intervalo de 100ms para verificar se o radio button foi encontrado
    });

    </script>
HTML;
}
