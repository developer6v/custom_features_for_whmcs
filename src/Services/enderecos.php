<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function trigger(el,type){if(!el)return;try{el.dispatchEvent(new Event(type,{bubbles:true}));}catch(e){}}
  
  // Escopos
  function getDomainScope(){ 
    var sel=document.getElementById('inputDomainContact');
    if (!sel) return null;
    var p=sel.closest('.panel-body');
    return p ? p.nextElementSibling : null; // Segundo bloco de formulário
  }
  function getClientScope(){
    var byPhone=document.getElementById('phonenumber');
    if(byPhone){var b=byPhone.closest('.panel-body');if(b)return b;}
    var byCountry=document.getElementById('inputCountry');
    if(byCountry){var c=byCountry.closest('.panel-body');if(c)return c;}
    var f=document.querySelector('form[data-gtm-form-interact-id]');
    return f ? f.closest('.panel-body') : null;
  }

  function q(s,sel){ return s ? s.querySelector(sel) : null; }
  function getValue(s,sel){ var el = q(s,sel); return el ? el.value : ""; }
  function setValue(s,sel,val){
    var el=q(s,sel); 
    if(!el)return;
    if(el.tagName === 'SELECT') {
      var prev = el.value; 
      el.value = val; 
      if(el.value !== val){
        var opts = el.options || [];
        for(var i = 0; i < opts.length; i++) {
          if (opts[i].text === val) { 
            el.value = opts[i].value; 
            break; 
          }
        }
      }
      if(el.value !== prev) trigger(el,'change');
    } else {
      if(el.value !== val){
        el.value = val;
        trigger(el,'input'); trigger(el,'change'); trigger(el,'blur');
      }
    }
  }

  // Mapa de campos (cliente -> domínio)
  var MAPPINGS = [
    ['#firstname',  '#firstname'],
    ['#lastname',   '#lastname'],
    ['#email',      '#email'],
    ['#phonenumber','#domaincontactphonenumber'],
    ['input[name="country-calling-code-phonenumber"]','input[name="country-calling-code-domaincontactphonenumber"]'],
    ['#address1',   '#address1'],
    ['#address2',   '#address2'],
    ['#city',       '#city'],
    ['#state',      '#state'],
    ['#postcode',   '#postcode'],
    ['#companyname','#companyname'],
    ['#inputCountry','#domaincontactcountry']
  ];

  // Copia agora (e aplica sua regra: address2 = address1 no cliente)
  function autofillDomainAddress(){
    var S = getClientScope(), D = getDomainScope();

    var address1 = getValue(S, '#address1');
    [S, D].forEach(function(scope){
      if(!scope) return;
      scope.querySelectorAll('#address2').forEach(function(el){
        var grp = el.closest('.form-group'); if (grp) grp.style.display = 'none';
        if (el.value !== address1) { el.value = address1; trigger(el,'input'); trigger(el,'change'); trigger(el,'blur'); }
      });
    });


    var address1 = getValue(S, '#address1');
    setValue(S, '#address2', address1);
    
    if(!S || !D) return;

    for(var i=0;i<MAPPINGS.length;i++){
      setValue(D, MAPPINGS[i][1], getValue(S, MAPPINGS[i][0]));
    }
  }

  // Espelhamento em tempo real digitando no 1º formulário
  function attachLiveMirrors(){
    var S = getClientScope();
    if(!S) return false;
    if(S.__mirrorBound__) return true; // evita duplicar

    S.__mirrorBound__ = true;
    var evts = ['input','change','blur'];
    for(var i=0;i<MAPPINGS.length;i++){
      (function(fromSel){
        var el = q(S, fromSel);
        if(!el) return;
        evts.forEach(function(evt){
          el.addEventListener(evt, autofillDomainAddress);
        });
      })(MAPPINGS[i][0]);
    }
    // cópia inicial
    autofillDomainAddress();
    return true;
  }

  // === NÃO REMOVIDO: continua espelhando ao mudar o select ===
  function handleSelectChange() {
    var secondForm = getDomainScope();
    if (secondForm) {
      autofillDomainAddress();
    }
  }

  // Espera o select existir e mantém o listener de change
  var checkFormExist = setInterval(function() {
    var inputField = document.getElementById('inputDomainContact'); 
    if (inputField) {
      inputField.addEventListener("change", handleSelectChange);
      clearInterval(checkFormExist);
      autofillDomainAddress();
    }
  }, 500);

  // Liga os listeners do 1º formulário assim que os escopos existirem
  var tries=0;
  var hookLive = setInterval(function(){
    tries++;
    var ok = attachLiveMirrors();
    if(ok && getDomainScope()){ clearInterval(hookLive); }
    if(tries>180){ clearInterval(hookLive); } // 3min fail-safe
  }, 1000);

  // Também tenta no DOM ready
  if(window.jQuery){
    jQuery(function(){
      attachLiveMirrors();
      autofillDomainAddress();
    });
  } else {
    if(document.readyState === 'complete' || document.readyState === 'interactive'){
      attachLiveMirrors(); autofillDomainAddress();
    } else {
      document.addEventListener('DOMContentLoaded', function(){
        attachLiveMirrors(); autofillDomainAddress();
      });
    }
  }

  
})();
</script>
HTML;
}
?>
