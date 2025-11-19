<?php
function registerNumber() {
   return <<<'HTML'
<script>
  // Estado global padrão
  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

  // Agregador global (define uma única vez)
  (function initAggregator(){
    if (window.__initCompanyAggregator) return; // evita redefinir
    window.__initCompanyAggregator = true;

    window.__docState = { reg:0, other:0 }; // comprimentos "só dígitos" dos 2 campos

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

      // Atualiza flag company com base no valor digitado e no required atual
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
      function digits(s){ return String(s||'').replace(/\D/g,''); }
      function isLenValid(len){ return len === 11 || len === 14; }

      // lê valores reais se existirem, e concilia com o agregador
      var elCtrl  = document.getElementById('cpfcnpjregistercontroller'); // controlador (reg)
      var elOther = document.getElementById('cl_custom_field_1');         // outro campo (other)
      var hasCtrl = !!elCtrl;

      var regLenReal   = elCtrl  ? digits(elCtrl.value).length  : 0;
      var otherLenReal = elOther ? digits(elOther.value).length : 0;

      var regLen   = Math.max(regLenReal,   window.__docState.reg   || 0);
      var otherLen = Math.max(otherLenReal, window.__docState.other || 0);

      // Empresa obrigatória se QUALQUER estiver "máscara CNPJ"
      var anyCnpj = (regLen > 11) || (otherLen > 11); // troque por === 14 se quiser estrito
      setCompanyRequired(anyCnpj);
      attachCompanyListenerOnce();

      // Regra: se existe controller, os DOIS devem ser válidos; senão, apenas o outro (ou o que houver)
      var docValid = hasCtrl
        ? (isLenValid(regLen) && isLenValid(otherLen))
        : (isLenValid(otherLen) || isLenValid(regLen));

      window.__checkout.doc = docValid;

      window.__recomputeCheckout && window.__recomputeCheckout();
    };

    window.__setDocLen = function(source, len){
      if (source === 'reg') window.__docState.reg = len;
      else window.__docState.other = len;
      window.__recomputeCompany();
    };
  })();

  // Habilitador de botões
window.__recomputeCheckout = function () {
  const g = window.__checkout || {};
  const hasDocTargets = !!(document.getElementById('1') || document.getElementById('0'));
  const disabled = (g.login && hasDocTargets) ? !g.doc : !(g.cep && g.doc && g.company);
  document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
};

  (function(){
    function trigger(el,t){ if(!el) return; try{ el.dispatchEvent(new Event(t,{bubbles:true})); }catch(e){} }
    function digits(s){ return (s||'').replace(/\D/g,''); }

    // helper local: atualiza o slot 'reg' a partir do controlador
    function __updDocLen_reg(value){
      var len = digits(String(value||'')).length;
      window.__setDocLen && window.__setDocLen('reg', len);
    }

    function maskCpfCnpjRegister($el){
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

      // >>> Atualiza o agregador como campo "reg" (controlador)
      __updDocLen_reg(v);
    }

    // Se houver dois inputs (id="1" e id="0"), copia e mascara
    function copyOnce(){
      var from = document.getElementById('cpfcnpjregistercontroller');
      var to1  = document.getElementById('1');
      var to2  = document.getElementById('0');
      if(!from || (!to1 && !to2)) return false;

      var val = (from.value != null) ? from.value : '';

      [to1, to2].forEach(function(to){
         if(to && to.value !== val){
            to.value = val;
            ['input','change','blur'].forEach(ev => trigger(to, ev));
         }
      });

      return true;
    }

    var watcher = setInterval(function(){
      var $from = jQuery('#1');
      var $to   = jQuery('#0');

      if($from.length && $to.length){
        clearInterval(watcher);
        var formGroup = $from.closest('.form-group__wrapper');
        if (formGroup && formGroup.prepend) {
          formGroup.prepend('<input type="text" class="form-control" id="cpfcnpjregistercontroller" name="Cpf/CNPJ">');
        } else {
          // fallback: insere antes do #1
          jQuery('#1').before('<input type="text" class="form-control" id="cpfcnpjregistercontroller" name="Cpf/CNPJ">');
        }
        var $newFrom = jQuery('#cpfcnpjregistercontroller');

        $newFrom.on('input change blur', function(){
          maskCpfCnpjRegister($newFrom);
          copyOnce();
        });

        // Dispara uma vez para inicializar estado e cópia
        maskCpfCnpjRegister($newFrom);
        copyOnce();
      }
    }, 300);

  })();
</script>
HTML;
}
