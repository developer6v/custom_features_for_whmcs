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

        var label = formGroup ? formGroup.querySelector('label') : null;
        if (label) label.textContent = required ? 'Empresa' : 'Empresa (opcional)';
  
        window.__checkout.company = !required || (company.value.trim().length > 0);
      }

      function setMessageCPFCNPJ(required){
        var spanMes =  document.querySelector('#cpf-cnpj-message');
        if (spanMes) {
          spanMes.innerHTML = required ? 'CNPJ detectado — Empresa obrigatória' : 'CPF — Empresa opcional';
        }
      }

window.__recomputeCompany = function(){
    function digits(s){ return String(s||'').replace(/\D/g,''); }
    function isLenValid(len){ return len === 11 || len === 14; }

    // -----------------------------
    //  VALIDADOR REAL DE CPF
    // -----------------------------
    function isValidCPF(v) {
        var d = digits(v);
        if (d.length !== 11) return false;

        // Recusa sequências tipo 00000000000
        if (/^(\d)\1{10}$/.test(d)) return false;

        var sum = 0;
        for (var i = 0; i < 9; i++) sum += parseInt(d.charAt(i)) * (10 - i);
        var dv1 = (sum * 10) % 11;
        if (dv1 === 10 || dv1 === 11) dv1 = 0;
        if (dv1 !== parseInt(d.charAt(9))) return false;

        sum = 0;
        for (var i = 0; i < 10; i++) sum += parseInt(d.charAt(i)) * (11 - i);
        var dv2 = (sum * 10) % 11;
        if (dv2 === 10 || dv2 === 11) dv2 = 0;

        return dv2 === parseInt(d.charAt(10));
    }

    var elCtrl  = document.getElementById('cpfcnpjregistercontroller');
    var elOther = document.getElementById('cl_custom_field_1');
    var hasCtrl = !!elCtrl;

    var regDigits   = elCtrl  ? digits(elCtrl.value)  : "";
    var otherDigits = elOther ? digits(elOther.value) : "";

    var regLen   = Math.max(regDigits.length,   window.__docState.reg   || 0);
    var otherLen = Math.max(otherDigits.length, window.__docState.other || 0);

    var anyCnpj = (regLen > 11) || (otherLen > 11);
    setCompanyRequired(anyCnpj);
    setMessageCPFCNPJ(anyCnpj);

    // -----------------------------
    //  VALIDAÇÃO FINAL DO DOCUMENTO
    // -----------------------------
    var docValid = false;

    if (hasCtrl) {
        // 1) Precisa ter tamanho valido
        var lenOk = isLenValid(regLen) && isLenValid(otherLen);

        // 2) Se for CPF, também validar matematicamente
        var cpfOk_reg   = (regDigits.length   === 11) ? isValidCPF(regDigits)   : true;
        var cpfOk_other = (otherDigits.length === 11) ? isValidCPF(otherDigits) : true;

        docValid = lenOk && cpfOk_reg && cpfOk_other;

    } else {
        // Sem o controller principal
        if (regDigits.length === 11) docValid = isValidCPF(regDigits);
        else if (otherDigits.length === 11) docValid = isValidCPF(otherDigits);
        else docValid = isLenValid(regLen) || isLenValid(otherLen);
    }

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
      }

      // helper: atualiza slot 'other' com base no cl_custom_field_1
      function __updDocLen_other(value){
        var len = digits(String(value||'')).length;
        window.__setDocLen && window.__setDocLen('other', len);
      }

      jQuery(function(){
        var checkExist = setInterval(function() {
          var $field = jQuery('#cl_custom_field_1');
          if ($field.length) {
            clearInterval(checkExist);

            if (!document.getElementById('cpf-cnpj-message')) {
              var messageElement = document.createElement('span');
              messageElement.id = 'cpf-cnpj-message';
              messageElement.style.fontSize = '12px';
              messageElement.style.marginTop = '5px';
              $field.parent().append(messageElement);
            }

            // Inicializa máscara e estado
            maskCpfCnpj($field);
            __updDocLen_other($field.val());

            // Observa mudanças
            $field.on('input change blur', function(){
              maskCpfCnpj($field);
              __updDocLen_other($field.val());
            });
          }
        }, 100);
      });
    })();
  </script>
HTML;
}