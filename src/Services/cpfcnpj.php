<?php
function cpfcnpj_script() {
    return <<<'HTML'
<script>
  // [COMPARTILHADO] controle único do botão (idempotente)
  window.__checkout = window.__checkout || { cep:false, doc:false, company:true };
  window.__recomputeCheckout = window.__recomputeCheckout || function(){
    const g = window.__checkout;
    const disabled = !(g.cep && g.doc && g.company);
    document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
  };

  (function(){
    function digits(s){ return (s||'').replace(/\D/g,''); }

    function toggleCompanyRequired(isCnpj){
      var $company = jQuery('input[name="companyname"]');
      if(!$company.length) return;

      if(isCnpj){
        $company.attr('required','required').attr('aria-required','true');
      }else{
        $company.removeAttr('required').removeAttr('aria-required');
      }

      // Flag "company": se for CNPJ, exige valor; se CPF, libera
      window.__checkout.company = !isCnpj || ($company.val().trim().length > 0);
      window.__recomputeCheckout();

      // Revalida ao digitar/alterar
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
