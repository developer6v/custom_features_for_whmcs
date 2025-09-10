<?php
function cpfcnpj_script() {
    return <<<'HTML'
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
    function digits(s){ return (s||'').replace(/\D/g,''); }

  function toggleCompanyRequired(isCnpj){
    var $company = jQuery('input[name="companyname"]');
    if(!$company.length) return;

    // pega o "(opcional)" dentro do label correspondente
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

 function maskCpfCnpj($el){
  var v = digits($el.val());
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

  $el.val(v);

  // >>> NOVO: libere mais caracteres quando chegar em 11 dígitos (transição p/ CNPJ)
  var len = digits(v).length;
  $el.prop('maxLength', (len >= 11 ? 18 : 14)); // CPF=14 chars, CNPJ=18 chars

  // Flags globais (AND do checkout)
  window.__checkout.doc = (len === 11 || len === 14);
  window.__recomputeCheckout();

  toggleCompanyRequired(len > 11); // CNPJ exige empresa
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
