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
        var $loginRadio = jQuery('#loginUser input[type="radio"][value="loginOption"]'); // Radio button "Login de clientes atuais"
        var $accountID = jQuery('#account_id'); // Radio button "Login de clientes atuais"
        var $accountID2 = jQuery('input[name="account_id"]'); // Radio button "Login de clientes atuais"

        if ($loginRadio.length || $accountID.length || $accountID2.length) {
            console.log("algum dos accoutn id foram encontrados");
            // Verifica se o radio button "Login de clientes atuais" está marcado
            if ($loginRadio.prop('checked') || $accountID.prop('checked') || $accountID2.prop('checked')) {
                window.__checkout.login = true;  // Marca como login de cliente atual
            } else if (!$loginRadio.prop('checked') && !$accountID.prop('checked') && !$accountID2.prop('checked')) {
                window.__checkout.login = false;  // Caso contrário, marca como falso
            }

            window.__recomputeCheckout();  // Atualiza o estado do checkout
        }
    }, 100);  // Intervalo de 100ms para verificar se o radio button foi encontrado
});
</script>
HTML;
}

function loginOrCreate_cart() {
    return <<<'HTML'
<script>
window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

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
        // Radio button "Criar uma nova conta"
        var $newAccountRadio = jQuery('input[type="radio"][name="custtype"][value="new"]');
        // Radio button "Login de clientes atuais"
        var $existingAccountRadio = jQuery('input[type="radio"][name="custtype"][value="existing"]');

        if ($newAccountRadio.length || $existingAccountRadio.length) {
            // Verifica se o radio button "Login de clientes atuais" está marcado
            if ($existingAccountRadio.prop('checked')) {
                window.__checkout.login = true;  // Marca como login de cliente atual
            } else if (!$existingAccountRadio.prop('checked')) {
                window.__checkout.login = false;  // Caso contrário, marca como falso
            }

            window.__recomputeCheckout();  // Atualiza o estado do checkout
        }
    }, 100);  // Intervalo de 100ms para verificar se o radio button foi encontrado
});
</script>
HTML;
}
