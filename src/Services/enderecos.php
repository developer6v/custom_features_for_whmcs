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

function enderecos_cart() {
    return <<<HTML
  <script>
  (function(){
  // Utilidades
  function trigger(el,type){ if(!el) return; try{ el.dispatchEvent(new Event(type,{bubbles:true})); }catch(e){} }
  function q(s,sel){ return s ? s.querySelector(sel) : null; }
  function qa(s,sel){ return s ? s.querySelectorAll(sel) : []; }

  // Escopos dos 2 blocos
  function getClientScope(){
    // Formulário principal (cliente)
    return document.querySelector('.panel-body.social-wide') || document;
  }
  function getDomainScope(){
    // Bloco do contato de domínio
    return document.getElementById('domainRegistrantInputFields') || null;
  }

  // Pega valor de um seletor dentro de um escopo
  function getValue(scope, selector){
    var el = q(scope, selector);
    if (!el) return '';
    return el.value || '';
  }

  // Seta valor com suporte a SELECT por value e por texto visível
  function setValue(scope, selector, val){
    var el = q(scope, selector);
    if (!el) return;

    if (el.tagName === 'SELECT') {
      var prev = el.value;
      el.value = val; // tenta por value

      if (el.value !== String(val)) {
        // tenta casar por texto visível (case-insensitive, trim)
        var opts = el.options || [];
        var needle = String(val).trim().toLowerCase();
        for (var i=0;i<opts.length;i++){
          var txt = (opts[i].text || '').trim().toLowerCase();
          if (txt === needle) { el.value = opts[i].value; break; }
        }
      }
      if (el.value !== prev) trigger(el,'change');
    } else {
      if (el.value !== String(val)) {
        el.value = String(val);
        trigger(el,'input'); trigger(el,'change'); trigger(el,'blur');
      }
    }
  }

  // Mapa de campos (Form 1 → Form 2) conforme seus HTMLs
  var MAPPINGS = [
    ['#inputFirstName',    '#inputDCFirstName'],
    ['#inputLastName',     '#inputDCLastName'],
    ['#inputEmail',        '#inputDCEmail'],
    ['#inputPhone',        '#inputDCPhone'],
    ['input[name="country-calling-code-phonenumber"]', 'input[name="country-calling-code-domaincontactphonenumber"]'],
    ['#inputCompanyName',  '#inputDCCompanyName'],
    ['#inputAddress1',     '#inputDCAddress1'], // <- especial: também atualiza #inputDCAddress2
    ['#inputCity',         '#inputDCCity'],
    ['#stateselect',       '#inputDCState'],     // select → input text
    ['#inputPostcode',     '#inputDCPostcode'],
    ['#inputCountry',      '#inputDCCountry']    // select → select
  ];

  // Copia valores do formulário 1 para o 2
  function autofillDomainAddress(){
    var S = getClientScope();
    var D = getDomainScope();
    if (!S || !D) return;

    for (var i=0; i<MAPPINGS.length; i++){
      var fromSel = MAPPINGS[i][0];
      var toSel   = MAPPINGS[i][1];

      var fromEl = q(S, fromSel);
      var toEl   = q(D, toSel);

      if (!fromEl || !toEl) continue;

      var val = '';
      if (fromEl.tagName === 'SELECT') {
        val = fromEl.value || (fromEl.options[fromEl.selectedIndex] ? fromEl.options[fromEl.selectedIndex].text : '');
      } else {
        val = fromEl.value || '';
      }

      setValue(D, toSel, val);

      // 🔁 NOVO: se for o Address1 de domínio, espelha para Address2 de domínio também
      if (toSel === '#inputDCAddress1') {
        setValue(D, '#inputDCAddress2', val);
      }
    }
  }

  // 🔁 NOVO: espelhamento dentro do bloco de domínio (digitar no DCAddress1 atualiza DCAddress2)
  function mirrorDomainAddress2(){
    var D = getDomainScope();
    if (!D) return;

    var a1 = q(D, '#inputDCAddress1');
    var a2 = q(D, '#inputDCAddress2');
    if (!a1 || !a2) return;

    if (a1.__mirrorBound__) return;
    a1.__mirrorBound__ = true;

    ['input','change','blur'].forEach(function(evt){
      a1.addEventListener(evt, function(){
        if (a2.value !== a1.value){
          a2.value = a1.value;
          trigger(a2,'input'); trigger(a2,'change'); trigger(a2,'blur');
        }
      });
    });
  }

  // Liga espelhamento ao vivo no form 1
  function attachLiveMirrors(){
    var S = getClientScope();
    if (!S) return false;
    if (S.__mirrorBound__) return true; // evita duplicar
    S.__mirrorBound__ = true;

    var evts = ['input','change','blur'];
    for (var i=0; i<MAPPINGS.length; i++){
      (function(fromSel){
        var el = q(S, fromSel);
        if (!el) return;
        evts.forEach(function(evt){ el.addEventListener(evt, autofillDomainAddress); });
      })(MAPPINGS[i][0]);
    }

    // cópia inicial
    autofillDomainAddress();
    // garante o espelho interno no bloco do domínio
    mirrorDomainAddress2();
    return true;
  }

  // Reage quando o usuário troca o select "Usar contato padrão / Adicionar novo..."
  function onDomainContactChange(){
    autofillDomainAddress();
    mirrorDomainAddress2();
  }

  // Inicialização: DOM pronto + elementos dinâmicos
  function init(){
    attachLiveMirrors();
    var sel = document.getElementById('inputDomainContact');
    if (sel && !sel.__boundChange){
      sel.addEventListener('change', onDomainContactChange);
      sel.__boundChange = true;
    }
    autofillDomainAddress();
    mirrorDomainAddress2();
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive'){
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
  }

  // Fallback: tenta por alguns segundos até achar os escopos (checkout pode hidratar depois)
  var tries = 0, maxTries = 20;
  var poll = setInterval(function(){
    tries++;
    var ok = getClientScope() && getDomainScope();
    if (ok){
      init();
      clearInterval(poll);
    }
    if (tries >= maxTries) clearInterval(poll);
  }, 500);
})();
</script>
HTML;
}


?>
