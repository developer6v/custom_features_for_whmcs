<?php
function loginOrCreate() {
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

  document.querySelector('#user_user').addEventListener('change', function() {
    // Chama a função que verifica se o botão de checkout deve ser habilitado ou desabilitado
    window.__recomputeCheckout();
});

(function() {
  jQuery(function() {
    // Verifica periodicamente se o campo de "Login de clientes atuais" foi selecionado
    var checkExist = setInterval(function() {
      var $loginRadio = jQuery('#loginUser input[type="radio"][value="loginOption"]'); // Radio button "Login de clientes atuais"
      
      if ($loginRadio.length) {
        clearInterval(checkExist);  // Para a verificação periódica quando o elemento for encontrado

        // Verifica se o radio button "Login de clientes atuais" está marcado
        if ($loginRadio.prop('checked')) {
            alert("checked")
          window.__checkout.login = true;  // Marca como login de cliente atual
        } else {
            alert("nn checked")
          window.__checkout.login = false;  // Caso contrário, marca como falso
        }

        window.__recomputeCheckout();  // Atualiza o estado do checkout
      }
    }, 100);  // Intervalo de 100ms para verificar se o radio button foi encontrado
  });
})();

</script>
HTML;
}
