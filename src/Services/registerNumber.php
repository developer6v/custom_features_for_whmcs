<?php

function registerNumber() {
    return <<<HTML
  <script>

  // Estado global padrão
  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

  jQuery(function(){
    console.log("teste")
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
      // Empresa obrigatória se QUALQUER um dos campos estiver como CNPJ (len >= 12)
      var anyCnpj = (window.__docState.reg > 11) || (window.__docState.other > 11);
      setCompanyRequired(anyCnpj);
      attachCompanyListenerOnce();

      // Documento válido se ALGUM campo estiver completo: CPF(11) ou CNPJ(14)
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

  // Habilitador de botões
  window.__recomputeCheckout = function() {
    const g = window.__checkout;
    const disabled = !(g.login) && !(g.cep && g.doc && g.company);
    document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
  };

  (function(){
    function trigger(el,t){ if(!el) return; try{ el.dispatchEvent(new Event(t,{bubbles:true})); }catch(e){} }
    function digits(s){ return (s||'').replace(/\D/g,''); }

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

      // >>> Atualiza o agregador como campo "other"
      window.__setDocLen('other', len);
    }
    // Se houver dois inputs (id="1" e id="0"), copia e mascara
    function copyOnce(){
      var from = document.getElementById('1');
      var to   = document.getElementById('0');
      if(!from || !to) return false;

      var val = (from.value != null) ? from.value : '';
      if(to.value !== val){
        to.value = val;
      }
      return true;
    }

      var watcher = setInterval(function(){
        var $from = jQuery('#1');
        var $to   = jQuery('#0');
        if($from.length && $to.length){
          clearInterval(watcher);
            $field.on('input change blur', function(){ maskCpfCnpj($field); });
        }
      }, 300);
    });

  })();
  </script>

  HTML;
}


?>
