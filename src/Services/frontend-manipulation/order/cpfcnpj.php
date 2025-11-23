<?php
function cpfcnpj_script() {
   return <<<'HTML'
  <script>
  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

  (function initAggregator(){
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


    // =====================================================
    // 🚀 VALIDADOR REAL DE CPF (com debug)
    // =====================================================
    function isValidCPF(v) {
      function digits(s){ return String(s||'').replace(/\D/g,''); }
      var d = digits(v);
      
      if (d.length !== 11) {
          alert("CPF (" + d + ") inválido — tamanho incorreto");
          return false;
      }
      if (/^(\d)\1{10}$/.test(d)) {
          alert("CPF (" + d + ") inválido — sequência inválida");
          return false;
      }

      var sum = 0;
      for (var i = 0; i < 9; i++) sum += parseInt(d.charAt(i)) * (10 - i);
      var dv1 = (sum * 10) % 11;
      if (dv1 >= 10) dv1 = 0;
      if (dv1 !== parseInt(d.charAt(9))) {
          alert("CPF (" + d + ") inválido — DV1 incorreto");
          return false;
      }

      sum = 0;
      for (var i = 0; i < 10; i++) sum += parseInt(d.charAt(i)) * (11 - i);
      var dv2 = (sum * 10) % 11;
      if (dv2 >= 10) dv2 = 0;

      var isValid = dv2 === parseInt(d.charAt(10));
      alert("CPF (" + d + ") é " + (isValid ? "VÁLIDO" : "INVÁLIDO"));

      return isValid;
    }
    // =====================================================



    window.__recomputeCompany = function(){
      function digits(s){ return String(s||'').replace(/\D/g,''); }
      function isLenValid(len){ return len === 11 || len === 14; }

      var elCtrl  = document.getElementById('cpfcnpjregistercontroller');
      var elOther = document.getElementById('cl_custom_field_1');
      var hasCtrl = !!elCtrl;

      var regDigits   = elCtrl  ? digits(elCtrl.value)  : "";
      var otherDigits = elOther ? digits(elOther.value) : "";

      var regLenReal   = regDigits.length;
      var otherLenReal = otherDigits.length;

      var regLen   = Math.max(regLenReal,   window.__docState.reg   || 0);
      var otherLen = Math.max(otherLenReal, window.__docState.other || 0);

      var anyCnpj = (regLen > 11) || (otherLen > 11);
      setCompanyRequired(anyCnpj);
      attachCompanyListenerOnce();


      // =====================================================
      // 🚀 NOVA LÓGICA: validação real de CPF
      // =====================================================
      var cpfValid_reg   = (regDigits.length   === 11) ? isValidCPF(regDigits)   : true;
      var cpfValid_other = (otherDigits.length === 11) ? isValidCPF(otherDigits) : true;

      // Documentos válidos somente se:  
      //  - comprimento válido E CPF válido, ou  
      //  - campo CNPJ (14 dígitos)
      var docValid = (
        (regDigits.length   === 11 && cpfValid_reg)   ||
        (otherDigits.length === 11 && cpfValid_other) ||
        regDigits.length   === 14 ||
        otherDigits.length === 14
      );
      // =====================================================


      window.__checkout.doc = docValid;

      window.__recomputeCheckout && window.__recomputeCheckout();
    };


    window.__setDocLen = function(source, len){
      if (source === 'reg') window.__docState.reg = len;
      else window.__docState.other = len;

      window.__recomputeCompany();
    };

  })();



  window.__recomputeCheckout = function () {
    const g = window.__checkout || {};
    const hasDocTargets = !!(document.getElementById('1') || document.getElementById('0'));
    const disabled = (g.login && hasDocTargets) ? !g.doc : !(g.cep && g.doc && g.company);
    document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
  };



  (function(){
    function trigger(el,t){ if(!el) return; try{ el.dispatchEvent(new Event(t,{bubbles:true})); }catch(e){} }
    function digits(s){ return (s||'').replace(/\D/g,''); }

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

      __updDocLen_reg(v);
    }

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
          jQuery('#1').before('<input type="text" class="form-control" id="cpfcnpjregistercontroller" name="Cpf/CNPJ">');
        }

        var $newFrom = jQuery('#cpfcnpjregistercontroller');

        $newFrom.on('input change blur', function(){
          maskCpfCnpjRegister($newFrom);
          copyOnce();
        });

        maskCpfCnpjRegister($newFrom);
        copyOnce();
      }
    }, 300);

  })();
</script>
HTML;
}
?>
