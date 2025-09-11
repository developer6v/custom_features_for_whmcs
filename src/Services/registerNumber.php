<?php
function registerNumber() {
    return <<<HTML
<script>

  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };
  window.__recomputeCheckout = function() {
      const g = window.__checkout;
      const disabled = !(g.login) && !(g.cep && g.doc && g.company);  
      document.querySelectorAll('button#checkout, #place_order')
          .forEach(b => b.disabled = disabled);
  };

(function(){
  function toggleCompanyRequiredRegister(isCnpj){
      // Seleciona o input pelo name
      var company = document.querySelector('input[name="companyname"]');
      if (!company) return;

      // Seleciona o label que contém "(opcional)" dentro da mesma form-group
      var formGroup = company.closest('.form-group');
      var elOpCompany = formGroup ? formGroup.querySelector('.control-label .control-label-info') : null;

      if (isCnpj) {
          company.setAttribute('required', 'required');
          company.setAttribute('aria-required', 'true');
          if (elOpCompany) elOpCompany.style.display = 'none';
      } else {
          company.removeAttribute('required');
          company.removeAttribute('aria-required');
          if (elOpCompany) elOpCompany.style.display = 'inline';
      }

      // Atualiza o estado global do checkout
      window.__checkout.company = !isCnpj || (company.value.trim().length > 0);
      window.__recomputeCheckout();

      // Remove event listeners antigos antes de adicionar novos
      company.removeEventListener('input', company._companyListener);
      company.removeEventListener('change', company._companyListener);
      company.removeEventListener('blur', company._companyListener);

      // Adiciona os eventos para atualizar o estado ao digitar
      company._companyListener = function() {
          window.__checkout.company = !isCnpj || (this.value.trim().length > 0);
          window.__recomputeCheckout();
      };

      company.addEventListener('input', company._companyListener);
      company.addEventListener('change', company._companyListener);
      company.addEventListener('blur', company._companyListener);
  }

  function trigger(el,t){ 
    if(!el) return; 
    try{ 
      el.dispatchEvent(new Event(t,{bubbles:true})); 
    } catch(e) {}
  }

  function copyOnce(){
    var from = document.getElementById('1');
    var to   = document.getElementById('0');
    if(!from || !to) return false;

    var val = (from.value != null) ? from.value : '';
    if(to.value !== val){
      to.value = val;
      ['input','change','blur'].forEach(function(ev){ 
        trigger(to, ev); 
      });

      maskCpfCnpj(from);  
    }
    return true;
  }

  var watcher = setInterval(function(){
    var from = document.getElementById('1');
    var to   = document.getElementById('0');
    if(from && to){
      clearInterval(watcher);
      copyOnce();
      // Liga a função copyOnce ao evento de input e change
      ['input','change'].forEach(function(ev){
        from.addEventListener(ev, copyOnce);
      });
    }
  }, 300);

  function maskCpfCnpj(el){
    var v = digits(el.value);  // Usando el.value ao invés de jQuery
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

    el.value = v; 
    var len = digits(v).length;
    el.maxLength = (len >= 11 ? 18 : 14);  
    window.__checkout.doc = (len === 11 || len === 14);
    window.__recomputeCheckout();

    toggleCompanyRequiredRegister(len > 11);
  }

  function digits(s) {
    return (s || '').replace(/\D/g, '');  
  }


})();
</script>
HTML;
}
?>
