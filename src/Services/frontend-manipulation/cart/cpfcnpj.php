<?php

function cpfcnpj_script_cart() {
    return <<<'HTML'
    <script>
          console.log("script cpfcnpj")

    window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

    (function ensureAggregator(){
      if (window.__initCompanyAggregator) return;
      window.__initCompanyAggregator = true;
      window.__docState = { reg:0, other:0 };

    function getCompanyInput(){
      return document.querySelector('input[name="companyname"]');
    }

    function setCompanyRequired(required) {
      console.log("funcao sendo chamada")
      var company = document.querySelector('input[name="companyname"]'); // Seleciona o input pelo name
      if (!company) {
        console.log("Campo 'companyname' não encontrado");
        return;
      }

      var formGroup = company.closest('.form-group');
      if (!formGroup) {
        console.log("form-group não encontrado");
        return;
      }

      var label = formGroup.querySelector('label');
      if (label) {
        // Log para verificar o texto que será aplicado
        console.log('Alterando label para: ' + (required ? 'Empresa' : 'Empresa (opcional)'));
        label.textContent = required ? 'Empresa' : 'Empresa (opcional)';
      } else {
        console.log("Label não encontrada");
      }
  

      window.__checkout = window.__checkout || {};
      window.__checkout.company = !required || (company.value.trim().length > 0);
    }

      function attachCompanyListenerOnce(){
        var company = getCompanyInput();
        if (!company || company._companyListenerAttached) return;
        company._companyListenerAttached = true;
        var handler = function(){
          var required = company.hasAttribute('required');
          window.__checkout.company = !required || (company.value.trim().length > 0);
          window.__recomputeCheckout && window.__recomputeCheckout();
        };
        ['input','change','blur'].forEach(ev => company.addEventListener(ev, handler));
      }
      window.__recomputeCompany = function(){
          alert('[cart] __recomputeCompany da versão CART foi chamado');
        var anyCnpj = (window.__docState.reg > 11) || (window.__docState.other > 11);
        setCompanyRequired(anyCnpj);
        attachCompanyListenerOnce();
        var docValid = [window.__docState.reg, window.__docState.other].some(l => l === 11 || l === 14);
        window.__checkout.doc = docValid;
        window.__recomputeCheckout && window.__recomputeCheckout();
      };
      window.__setDocLen = function(source, len){
        if (source === 'reg') window.__docState.reg = len;
        else window.__docState.other = len;
        window.__recomputeCompany();
      };
    })();

    window.__recomputeCheckout = function() {
      
      const g = window.__checkout;
      const disabled = !(g.login) && !(g.cep && g.doc && g.company);
      document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
    };

    (function(){
      function digits(s){ return (s||'').replace(/\D/g,''); }

      function maskCpfCnpj($el){
        var v = digits($el.val());
        if (v.length > 14) v = v.slice(0,14);

        if (v.length <= 11){
          if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
          else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
          else if (v.length > 3) v = v.replace(/^(\d{3})((\d{0,3})).*$/, "$1.$2");
        } else {
          if (v.length > 12)     v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
          else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/, "$1.$2.$3/$4");
          else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
          else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3}).*$/, "$1.$2");
        }

        $el.val(v);
        var len = digits(v).length;
        $el.prop('maxLength', (len >= 11 ? 18 : 14));

        // >>> Atualiza o agregador como campo "other"
        window.__setDocLen('reg', len);

      }

      jQuery(function(){
        var checkExist = setInterval(function() {
          var $field = jQuery('#customfield1');
          if ($field.length) {
            clearInterval(checkExist);
            maskCpfCnpj($field);
            $field.on('input change blur', function(){ maskCpfCnpj($field); });
          }
        }, 100);
      });
    })();
  </script>

HTML;
}



