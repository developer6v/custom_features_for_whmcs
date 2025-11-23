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
function cpfcnpj_domain_script() {
    return <<<'HTML'
  <style>
  /* Fallback CSS: esconde oficiais por NAME (independente do índice) */
  .domain-info-additional .form-group.row:has(input[name^="domainfield["][name$="[0]"]),
  .domain-info-additional .form-group.row:has(input[name^="domainfield["][name$="[1]"]) {
    display: none !important;
  }

  /* Controller */
  #cpf-cnpj-controller-wrapper { margin-bottom: 15px; }
  #cpf-cnpj-controller { font-size: 14px; }
  #cpf-cnpj-message { display:none; font-size:12px; margin-top:8px; line-height:1.5; }
  </style>

  <script>
  (function(){
    'use strict';

    console.log('[CPF/CNPJ] Script iniciado');
    window.__checkout = window.__checkout || { doc:false };

    // Utils
    function digits(s){ return (s||'').replace(/\D/g,''); }
    function trigger(el, type){ if(!el) return; try{ el.dispatchEvent(new Event(type,{bubbles:true,cancelable:true})); }catch(e){} }

    function maskCpfCnpj(value){
      var v = digits(value||'');
      if (v.length > 14) v = v.slice(0,14);
      if (v.length <= 11){
        if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/,"$1.$2.$3-$4");
        else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/,"$1.$2.$3");
        else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3}).*$/,"$1.$2");
      } else {
        if (v.length > 12)     v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/,"$1.$2.$3/$4-$5");
        else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/,"$1.$2.$3/$4");
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/,"$1.$2.$3");
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3}).*$/,"$1.$2");
      }
      return v;
    }

  

    function isValidCNPJ(cnpj){ return digits(cnpj).length === 14; }

    function recomputeCheckout(){
      var disabled = !window.__checkout.doc;
      document.querySelectorAll('button#checkout, #place_order, button[type="submit"][data-btn-loader], button.btn-primary[type="submit"]').forEach(function(b){
        b.disabled = disabled;
        console.log('[CPF/CNPJ] Botão', b.className || b.id || '(sem class/id)', '- Disabled:', disabled);
      });
    }
    window.__recomputeCheckout = recomputeCheckout;

    // Sincroniza controller -> oficiais
    function syncFields(controllerValue){
      var masked = maskCpfCnpj(controllerValue);
      var d = digits(masked);

      // [*][0] = Register Number
      document.querySelectorAll('input[name^="domainfield["][name$="[0]"]').forEach(function(el){
        if (el.value !== masked){ el.value = masked; trigger(el,'input'); }
      });
      // [*][1] = CPF/CNPJ
      document.querySelectorAll('input[name^="domainfield["][name$="[1]"]').forEach(function(el){
        if (el.value !== masked){ el.value = masked; trigger(el,'input'); }
      });

      var ok = (d.length === 11 && isValidCPF(masked)) || (d.length === 14 && isValidCNPJ(masked));
      window.__checkout.doc = ok;
      recomputeCheckout();
      return masked;
    }

    // Aguarda jQuery (mantém seu padrão)
    function waitForJQuery(cb){
      if (typeof jQuery !== 'undefined') cb(jQuery);
      else setTimeout(function(){ waitForJQuery(cb); }, 100);
    }

    waitForJQuery(function($){
      console.log('[CPF/CNPJ] jQuery detectado, iniciando busca por campos');

      var checkExist = setInterval(function(){
        // Oficiais por NAME (genéricos)
        var field0 = $('input[name^="domainfield["][name$="[0]"]');
        var field1 = $('input[name^="domainfield["][name$="[1]"]');

        // Ids com colchetes — escape correto
        if (!field0.length) field0 = $('#inputDomainfield\\[0\\]\\[0\\], #inputDomainfield\\[1\\]\\[0\\], [id^="inputDomainfield"][id$="\\[0\\]"]');
        if (!field1.length) field1 = $('#inputDomainfield\\[0\\]\\[1\\], #inputDomainfield\\[1\\]\\[1\\], [id^="inputDomainfield"][id$="\\[1\\]"]');

        if ( (field0.length && field1.length) || $('input[name^="domainfield["]').length ){
          clearInterval(checkExist);
          console.log('[CPF/CNPJ] Campos encontrados!');

          var $f0 = field0.first();
          var $f1 = field1.first();

          // Esconde oficiais (com guarda de closest)
          [$f0, $f1].forEach(function($f){
            if (!$f || !$f.length) return;
            var $grp = $f.closest('.form-group, .form-group.row');
            if ($grp && $grp.length) $grp.hide();
          });
          console.log('[CPF/CNPJ] Campos oficiais escondidos');

          // Se já existe controller, não duplica
          if ($('#cpf-cnpj-controller').length){
            console.log('[CPF/CNPJ] Controller já existe, abortando inserção');
          } else {
            // Monta controller
            var newFieldHtml =
              '<div class="form-group row" id="cpf-cnpj-controller-wrapper">'
            + '  <label class="col-form-label col-md-4 col-lg-2" for="cpf-cnpj-controller">CPF ou CNPJ:</label>'
            + '  <div class="col-form-label col-checkbox col-md-8 col-lg-10">'
            + '    <input type="text" id="cpf-cnpj-controller" name="cpf-cnpj-controller" class="form-control input-250 input-inline" maxlength="18" placeholder="Digite CPF ou CNPJ" autocomplete="off">'
            + '    <span id="cpf-cnpj-message" style="display:block; font-size:12px; margin-top:5px; color:#666;">'
            + '      Formato do CPF (11 dígitos): NNN.NNN.NNN-NN<br>'
            + '      Formato do CNPJ (14 dígitos): NN.NNN.NNN/NNNN-NN'
            + '    </span>'
            + '  </div>'
            + '</div>';

            // Insere antes do primeiro oficial escondido, ou no container
            var $anchor = ($f0.length ? $f0 : $f1).closest('.form-group, .form-group.row');
            if ($anchor && $anchor.length){
              $anchor.before(newFieldHtml);
              console.log('[CPF/CNPJ] Campo controller inserido');
            } else {
              $('.domain-info-additional').prepend(newFieldHtml);
              console.log('[CPF/CNPJ] Campo controller inserido (fallback)');
            }
          }

          var $controller = $('#cpf-cnpj-controller');
          // Carrega valor existente se houver
          var existing = ($f1.val() || $f0.val() || '');
          if (existing){
            var masked = maskCpfCnpj(existing);
            $controller.val(masked);
            syncFields(masked);
            console.log('[CPF/CNPJ] Valor existente carregado:', masked);
          }

          // Listener do controller (espelha nos oficiais)
          $controller.on('input change blur', function(){
            var masked = syncFields($controller.val());
            $controller.val(masked);

            var len = digits(masked).length;
            $controller.prop('maxLength', 18); // 18 cobre CPF e CNPJ

            var $msg = $('#cpf-cnpj-message');
            if (len === 11){
              $msg.html(isValidCPF(masked) ? '<span style="color:green; font-weight:bold;">✓ CPF válido</span>' : '<span style="color:red; font-weight:bold;">✗ CPF inválido</span>');
            } else if (len === 14){
              $msg.html(isValidCNPJ(masked) ? '<span style="color:green; font-weight:bold;">✓ CNPJ válido</span>' : '<span style="color:red; font-weight:bold;">✗ CNPJ inválido</span>');
            } else if (len>0 && len<11){
              $msg.html('<span style="color:orange;">CPF incompleto ('+len+'/11)</span>');
            } else if (len>11 && len<14){
              $msg.html('<span style="color:orange;">CNPJ incompleto ('+len+'/14)</span>');
            } else {
              $msg.html('Formato do CPF (11 dígitos): NNN.NNN.NNN-NN<br>Formato do CNPJ (14 dígitos): NN.NNN.NNN/NNNN-NN');
            }
          });

          // Estado inicial dos botões
          window.__checkout.doc = !!(digits($controller.val()).length === 11 || digits($controller.val()).length === 14);
          window.__recomputeCheckout && window.__recomputeCheckout();

          console.log('[CPF/CNPJ] Sistema completamente inicializado!');
        }
      }, 100);

      // Timeout de segurança
      setTimeout(function(){
        clearInterval(checkExist);
        console.log('[CPF/CNPJ] Timeout atingido, busca encerrada');
      }, 10000);
    });
  })();
  </script>
  HTML;
}
