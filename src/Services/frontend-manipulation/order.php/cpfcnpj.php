<?php
function cpfcnpj_script() {
    return <<<'HTML'
  <script>
    window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

    // =========================
    //  VALIDADORES CPF / CNPJ
    // =========================
    (function validators(){
      function onlyDigits(s){ return String(s||'').replace(/\D/g,''); }

      // Evita sequências do tipo 000... / 111... etc
      function isSequence(d){ return /^(\d)\1{10,13}$/.test(d); }

      // CPF: 11 dígitos
      function isValidCPF(v){
        alert("ta chegando aqui")
        var d = onlyDigits(v);
        if (d.length !== 11 || isSequence(d)) return false;
        var sum = 0, rest;

        // 1º DV
        for (var i=1; i<=9; i++) sum += parseInt(d.substring(i-1,i),10) * (11 - i);
        rest = (sum * 10) % 11; if (rest === 10 || rest === 11) rest = 0;
        if (rest !== parseInt(d.substring(9,10),10)) return false;

        // 2º DV
        sum = 0;
        for (i=1; i<=10; i++) sum += parseInt(d.substring(i-1,i),10) * (12 - i);
        rest = (sum * 10) % 11; if (rest === 10 || rest === 11) rest = 0;
        return rest === parseInt(d.substring(10,11),10);
      }

      // CNPJ: 14 dígitos
      function isValidCNPJ(v){
        var d = onlyDigits(v);
        if (d.length !== 14 || isSequence(d)) return false;

        var len = 12, numbers = d.substring(0,len), digits = d.substring(len);
        var sum = 0, pos = len - 7;

        for (var i=len; i>=1; i--){
          sum += parseInt(numbers.charAt(len - i),10) * pos--;
          if (pos < 2) pos = 9;
        }
        var result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
        if (result !== parseInt(digits.charAt(0),10)) return false;

        len = 13; numbers = d.substring(0,len); sum = 0; pos = len - 7;
        for (i=len; i>=1; i--){
          sum += parseInt(numbers.charAt(len - i),10) * pos--;
          if (pos < 2) pos = 9;
        }
        result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
        return result === parseInt(digits.charAt(1),10);
      }

      function docType(d){ return d.length === 11 ? 'CPF' : (d.length === 14 ? 'CNPJ' : null); }

      // expõe globalmente
  window.__doc = {
    digits: onlyDigits,
    isValidCPF,
    isValidCNPJ,
    isValid: function(v){
      var d = onlyDigits(v);
      alert('[__doc.isValid] v=' + v + ' | digits=' + d + ' | len=' + d.length);

      if (d.length === 11) {
        var rCpf = isValidCPF(d);
        alert('[__doc.isValid] CPF detectado. Resultado: ' + rCpf);
        return rCpf;
      }

      if (d.length === 14) {
        var rCnpj = isValidCNPJ(d);
        alert('[__doc.isValid] CNPJ detectado. Resultado: ' + rCnpj);
        return rCnpj;
      }

      alert('[__doc.isValid] Tamanho inválido, retornando false');
      return false;
    },
    type: docType
  };


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

      function setMessageCPFCNPJ(anyCnpj){
        var spanMes =  document.querySelector('#cpf-cnpj-message');
        if (spanMes) {
          spanMes.innerHTML = anyCnpj ? 'CNPJ detectado — Empresa obrigatória' : 'CPF — Empresa opcional';
        }
      }

      // Sinaliza erro visual no campo
      function setFieldValidity($field, isValid, messageWhenInvalid){
        var el = $field && $field.length ? $field[0] : null;
        if (!el) return;
        el.setCustomValidity(isValid ? '' : (messageWhenInvalid || 'Documento inválido'));
        // estilo simples sem depender de CSS global
        el.style.borderColor = isValid ? '' : '#e55353';
      }

      window.__recomputeCompany = function(){
        var digits = window.__doc.digits;

        var elCtrl  = document.getElementById('cpfcnpjregistercontroller');
        var elOther = document.getElementById('cl_custom_field_1');
        var hasCtrl = !!elCtrl;

        var regVal   = elCtrl  ? String(elCtrl.value || '')  : '';
        var otherVal = elOther ? String(elOther.value || '') : '';

        var regD   = digits(regVal);
        var otherD = digits(otherVal);

        var regLen   = Math.max(regD.length,   window.__docState.reg   || 0);
        var otherLen = Math.max(otherD.length, window.__docState.other || 0);

        var anyCnpj = (regLen > 11) || (otherLen > 11);
        setCompanyRequired(anyCnpj);
        setMessageCPFCNPJ(anyCnpj);

         var regOk   = regVal   ? window.__doc.isValid(regVal)   : false;
        var otherOk = otherVal ? window.__doc.isValid(otherVal) : false;

        // Se existir campo controlador, exigimos AMBOS válidos.
        // Caso não exista, basta um válido.
        var docValid = hasCtrl ? (regOk && otherOk) : (regOk || otherOk);

        // Se nenhum dos campos tem algo digitado, força inválido.
        if (!regVal && !otherVal) docValid = false;

        window.__checkout.doc = docValid;

        // ---- Logs úteis
        console.log('[doc] ctrl?', hasCtrl, '| anyCnpj?', anyCnpj);
        console.log('[doc] regVal:', regVal, '=>', regOk ? 'VÁLIDO' : 'inválido');
        console.log('[doc] otherVal:', otherVal, '=>', otherOk ? 'VÁLIDO' : 'inválido');
        console.log('[doc] RESULTADO docValid =', docValid ? 'VÁLIDO' : 'INVÁLIDO');

        // feedback visual no campo "other"
        if (elOther) {
          var $other = jQuery(elOther);
          var dType = window.__doc.type(window.__doc.digits(otherVal));
          var msg = dType === 'CPF' ? 'CPF inválido' : (dType === 'CNPJ' ? 'CNPJ inválido' : 'Documento incompleto');
          // válido se vazio (sem bloquear digitação) OU se passou na validação
          var ok = (!otherVal) || otherOk;
          // borda/veredito
          $other[0].setCustomValidity(ok ? '' : msg);
          $other[0].style.borderColor = ok ? '' : '#e55353';
        }

        window.__recomputeCheckout && window.__recomputeCheckout();
      };


      window.__setDocLen = function(source, len){
        if (source === 'reg') window.__docState.reg = len;
        else window.__docState.other = len;
        window.__recomputeCompany();
      };
    })();
    window.__recomputeCheckout = function() {
      const g = window.__checkout || {};
      // Habilita se: login OK OU (CEP OK E DOC OK E COMPANY OK)
      const canProceed = !!(g.login || (g.cep && g.doc && g.company));
      const disabled = !canProceed;

      console.log('[checkout] estados =>', JSON.stringify(g));
      console.log('[checkout] pode prosseguir?', canProceed ? 'SIM' : 'NÃO');

      document.querySelectorAll('button#checkout, #place_order')
        .forEach(b => b.disabled = disabled);
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
              messageElement.style.display = 'block';
              messageElement.style.fontSize = '12px';
              messageElement.style.marginTop = '5px';
              $field.parent().append(messageElement);
            }

            // Inicializa máscara e estado
            maskCpfCnpj($field);
            __updDocLen_other($field.val());

            // Observa mudanças
            $field.on('input change blur', function(){
                alert('[cl_custom_field_1] handler disparou. Valor atual: ' + $field.val());
              maskCpfCnpj($field);
              __updDocLen_other($field.val());
              window.__recomputeCompany && window.__recomputeCompany(); // <— garante revalidação imediata
            });


            // força um recompute ao carregar
            window.__recomputeCompany && window.__recomputeCompany();
          }
        }, 100);
      });
    })();
  </script>
  HTML;
}

