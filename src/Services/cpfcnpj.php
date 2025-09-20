<?php
function cpfcnpj_script() {
    return <<<'HTML'
  <script>
    window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

    // Agregador já definido no outro script; se não, define aqui rapidamente
    (function ensureAggregator(){
      if (window.__initCompanyAggregator) return;
      window.__initCompanyAggregator = true;
      window.__docState = { reg:0, other:0 };

    function getCompanyInput(){
      return document.querySelector('input[name="companyname"]');
    }
    function setCompanyRequired(required){
      var company = getCompanyInput();
      if (!company) return;
      var formGroup = company.closest('.form-group');
      var elOpCompany = formGroup ? formGroup.querySelector('.control-label .control-label-info') : null;
      if (required) {
        company.setAttribute('required','required');
        company.setAttribute('aria-required','true');
        if (elOpCompany) elOpCompany.style.display = 'none';
      } else {
        company.removeAttribute('required');
        company.removeAttribute('aria-required');
        if (elOpCompany) elOpCompany.style.display = 'inline';
      }
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
      var anyCnpj = (window.__docState.reg === 14) || (window.__docState.other === 14);
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
      window.__setDocLen('other', len);
    }

    jQuery(function(){
      var checkExist = setInterval(function() {
        var $field = jQuery('#cl_custom_field_1');
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


function cpfcnpj_script_cart() {
    return <<<'HTML'
<script>
  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

  (function ensureAggregator(){
    if (window.__initCompanyAggregator) return;
    window.__initCompanyAggregator = true;
    window.__docState = { reg:0, other:0 };

   function getCompanyInput(){
    return document.querySelector('input[name="companyname"]');
  }

  function setCompanyRequired(required){
    var company = getCompanyInput();
    if (!company) return;

    var formGroup = company.closest('.form-group');
    var label = formGroup ? formGroup.querySelector('label.control-label') : null;

    if (required) {
      company.setAttribute('required','required');
      company.setAttribute('aria-required','true');
      if (label) label.textContent = 'Empresa';
    } else {
      company.removeAttribute('required');
      company.removeAttribute('aria-required');
      if (label) label.textContent = 'Empresa (opcional)';
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
      window.__setDocLen('other', len);
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




function cpfcnpj_script_admin() {
    return <<<'HTML'
  <script>
    window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

    (function ensureAggregator(){
      if (window.__initCompanyAggregator) return;
      window.__initCompanyAggregator = true;
      window.__docState = { reg:0, other:0 };
      
      function getCompanyInput(){
        return document.querySelector('input[name="companyname"]');
      }

      function setCompanyRequired(required){
        var company = getCompanyInput();
        if (!company) return;

        var formGroup = company.closest('td.fieldarea'); // Encontrando a célula que contém o input
        var label = formGroup ? formGroup.querySelector('small') : null;

        if (required) {
          company.setAttribute('required','required');
          company.setAttribute('aria-required','true');
          if (label) {
            label.textContent = 'Empresa'; // Atualiza o texto do label
            label.style.display = 'none'; // Esconde o "(Opcional)".
          }
        } else {
          company.removeAttribute('required');
          company.removeAttribute('aria-required');
          if (label) {
            label.textContent = 'Empresa (opcional)';
            label.style.display = 'inline'; // Exibe novamente o "(Opcional)".
          }
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
        var anyCnpj = (window.__docState.reg === 14) || (window.__docState.other === 14); // Verifica se o CNPJ tem 14 caracteres
        setCompanyRequired(anyCnpj);
        attachCompanyListenerOnce();
        var docValid = [window.__docState.reg, window.__docState.other].some(l => l === 11 || l === 14); // Verifica se o CPF tem 11 ou CNPJ tem 14 dígitos
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
      const submitButton = document.querySelector('input[type="submit"][value="Adicionar Cliente"]');
      if (submitButton) {
        submitButton.disabled = disabled;
      }
    };

    (function(){
      function digits(s){ return (s||'').replace(/\D/g,''); } // Remove qualquer coisa que não seja dígito

      function maskCpfCnpj($el){
        var v = digits($el.val()); // Extrai apenas os dígitos

        // Limite de caracteres para CPF e CNPJ
        if (v.length > 14) v = v.slice(0,14);

        // Máscara para CPF
        if (v.length <= 11){
          if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
          else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
          else if (v.length > 3) v = v.replace(/^(\d{3})((\d{0,3})).*$/, "$1.$2");
        } else {
          // Máscara para CNPJ
          if (v.length > 12)     v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
          else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/, "$1.$2.$3/$4");
          else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
          else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3}).*$/, "$1.$2");
        }

        $el.val(v); // Atualiza o valor do campo com a máscara
        var len = digits(v).length; // Comprimento final sem os caracteres não numéricos
        $el.prop('maxLength', (len >= 11 ? 18 : 14)); // Define o maxLength

        // Atualiza a contagem de caracteres no agregador
        window.__setDocLen('other', len);
      }

      jQuery(function(){
        var checkExist = setInterval(function() {
          var $field = jQuery('#customfield1');
          if ($field.length) {
            clearInterval(checkExist);
            maskCpfCnpj($field);
            $field.on('input change blur', function(){ 
              maskCpfCnpj($field); 
            });
          }
        }, 100);
      });
    })();
  </script>
  HTML;
}
