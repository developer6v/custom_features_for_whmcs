<?php
function registerNumber() {
    return <<<HTML
<script>

  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };
  window.__recomputeCheckout = function() {
      const g = window.__checkout;
      // Se o login de clientes atuais estiver ativado, habilita o botão
      const disabled = !(g.login) && !(g.cep && g.doc && g.company);  // Considera login como prioridade

      // Atualiza o estado dos botões
      document.querySelectorAll('button#checkout, #place_order')
          .forEach(b => b.disabled = disabled);
  };



(function(){
    function toggleCompanyRequired(isCnpj){
    var $company = jQuery('input[name="companyname"]');
    if(!$company.length) return;

    var elOpCompany = $company.closest('.form-group').find('.control-label .control-label-info')[0];

    if(isCnpj){
      $company.attr({'required':'required','aria-required':'true'});
      if(elOpCompany) elOpCompany.style.display = 'none';
    } else {
      $company.removeAttr('required aria-required');
      if(elOpCompany) elOpCompany.style.display = 'inline';
    }

      window.__checkout.company = !isCnpj || ($company.val().trim().length > 0);
      window.__recomputeCheckout();

      $company.off('.companychk').on('input.companychk change.companychk blur.companychk', function(){
        window.__checkout.company = !isCnpj || (this.value.trim().length > 0);
        window.__recomputeCheckout();
      });
  }

  function trigger(el,t){ 
    if(!el) return; 
    try{ 
      el.dispatchEvent(new Event(t,{bubbles:true})); 
    } catch(e) {}
  }

  function copyOnce(){
    var from = document.getElementById('1');
    var to   = document.getElementById('0');
    if(!from || !to) return false;

    var val = (from.value != null) ? from.value : '';
    if(to.value !== val){
      to.value = val;
      ['input','change','blur'].forEach(function(ev){ 
        trigger(to, ev); 
      });

      // Aplique a máscara no campo de origem (from)
      maskCpfCnpjRegister(from);  // Aplica a máscara ao campo 'from'
    }
    return true;
  }

  
  jQuery(function() {
    // Verifica periodicamente se os campos estão disponíveis
    var checkExist = setInterval(function() {
      var $from = jQuery('#1');
      var $to = jQuery('#0');

      if ($from.length && $to.length) {
        alert("Encontrou o campo");
        clearInterval(checkExist);
        copyOnce();

        // Liga a função copyOnce ao evento 'input' e 'change'
        $from.on('input change', function() {
          copyOnce();
        });
      } else {
        alert("Não encontrou o campo de id");
      }
    }, 300);
  });


  // Função para aplicar a máscara de CPF/CNPJ
  function maskCpfCnpjRegister(el){
    alert("mensagem de mascara chamada")
    var v = digits(el.value);  // Usando el.value ao invés de jQuery
    if(v.length > 14) v = v.slice(0,14);

    if(v.length <= 11){
      if(v.length > 9){
        v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
      } else if(v.length > 6){
        v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
      } else if(v.length > 3){
        v = v.replace(/^(\d{3})((\d{0,3})).*$/, "$1.$2");
      }
    } else {
      if(v.length > 12){
        v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
      } else if(v.length > 8){
        v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/, "$1.$2.$3/$4");
      } else if(v.length > 5){
        v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
      } else if(v.length > 2){
        v = v.replace(/^(\d{2})(\d{0,3}).*$/, "$1.$2");
      }
    }

    el.value = v;  // Atualiza o campo com o valor formatado

    // >>> NOVO: libere mais caracteres quando chegar em 11 dígitos (transição p/ CNPJ)
    var len = digits(v).length;
    el.maxLength = (len >= 11 ? 18 : 14);  // CPF=14 chars, CNPJ=18 chars
  
    // Flags globais (AND do checkout)
    window.__checkout.doc = (len === 11 || len === 14);
    window.__recomputeCheckout();

    toggleCompanyRequired(len > 11);
  }

  function digits(s) {
    return (s || '').replace(/\D/g, '');  // Remove todos os caracteres não numéricos
  }

  jQuery(function(){
  // Verifica periodicamente se o campo está disponível
    var checkExist = setInterval(function() {
      var $field = jQuery('#cl_custom_field_1');
      if ($field.length) {
        clearInterval(checkExist);
        maskCpfCnpj($field);
        $field.on('input', function(){ maskCpfCnpj($field); });
      }
    }, 100);
  });


})();
</script>
HTML;
}
?>
