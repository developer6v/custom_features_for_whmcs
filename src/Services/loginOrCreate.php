<?php
function loginOrCreate() {
    return <<<'HTML'
<script>
  console.log('[checkout] script carregado');

  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };
  console.log('[checkout] estado inicial:', JSON.stringify(window.__checkout));

  // Função para reavaliar o estado do checkout
  window.__recomputeCheckout = function() {
      const g = window.__checkout;
      const disabled = !(g.login) && !(g.cep && g.doc && g.company);
      console.log('[recompute] g=', g, '=> disabled=', disabled);

      const btns = document.querySelectorAll('button#checkout, #place_order');
      console.log('[recompute] botoes encontrados:', btns.length);

      btns.forEach((b, i) => {
        b.disabled = disabled;
        console.log(`[recompute] botao[${i}] id=${b.id || '(sem id)'} disabled=`, b.disabled);
      });
  };

  jQuery(function() {
    console.log('[jquery] DOM pronto');

    var tick = 0;
    var checkExist = setInterval(function() {
      tick++;
      console.log('[interval] tick=', tick);

      var $loginRadio = jQuery('#loginUser input[type="radio"][value="loginOption"]'); // Radio "Login de clientes atuais"
      var $accountID  = jQuery('#account_id');

      console.log('[interval] $loginRadio length=', $loginRadio.length);
      console.log('[interval] $accountID  length=', $accountID.length);

      if ($loginRadio.length && $accountID.length) {
        console.log('[interval] ambos elementos existem');

        var loginRadioChecked = $loginRadio.prop('checked');
        var accountIDChecked  = $accountID.prop('checked');

        console.log('[interval] loginRadioChecked=', loginRadioChecked, 'accountIDChecked=', accountIDChecked);

        if (loginRadioChecked || accountIDChecked) {
          window.__checkout.login = true;
          console.log('[interval] set login=true');
        } else {
          window.__checkout.login = false;
          console.log('[interval] set login=false');
        }

        console.log('[interval] chamando __recomputeCheckout()');
        window.__recomputeCheckout();
      } else {
        if (!$loginRadio.length) console.log('[interval] #loginUser input[value="loginOption"] NÃO encontrado');
        if (!$accountID.length)  console.log('[interval] #account_id NÃO encontrado');
      }
    }, 100); // 100ms
  });
</script>
HTML;
}
