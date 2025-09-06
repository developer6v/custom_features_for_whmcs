<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function trigger(el,type){ if(!el)return; try{ el.dispatchEvent(new Event(type,{bubbles:true})); }catch(e){} }

  // Escopos
  function getDomainScope(){
    var sel=document.getElementById('inputDomainContact');
    if(!sel) return null;
    var p=sel.closest('.panel-body');
    return p ? p.nextElementSibling : null;
  }
  function getClientScope(){
    var byPhone=document.getElementById('phonenumber');
    if(byPhone){ var b=byPhone.closest('.panel-body'); if(b) return b; }
    var byCountry=document.getElementById('inputCountry');
    if(byCountry){ var c=byCountry.closest('.panel-body'); if(c) return c; }
    var f=document.querySelector('form[data-gtm-form-interact-id]');
    return f ? f.closest('.panel-body') : null;
  }

  function q(s,sel){ return s ? s.querySelector(sel) : null; }
  function getValue(s,sel){ var el=q(s,sel); return el ? el.value : ""; }
  function setValue(s,sel,val){
    var el=q(s,sel);
    if(!el) return;
    if(el.tagName === 'SELECT'){
      var prev=el.value;
      el.value = val;
      if(el.value !== val){
        var opts=el.options||[];
        for(var i=0;i<opts.length;i++){
          if(opts[i].text === val){ el.value=opts[i].value; break; }
        }
      }
      if(el.value !== prev) trigger(el,'change');
    }else{
      if(el.value !== val){
        el.value = val;
        trigger(el,'input'); trigger(el,'change'); trigger(el,'blur');
      }
    }
  }

  // Mapa de campos: [origem_no_cliente, destino_no_dominio]
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

  // Copia os valores atuais do escopo cliente -> domínio
  function mirrorNow(){
    var S=getClientScope(), D=getDomainScope();
    if(!S || !D) return;

    // sua regra: address2 do cliente recebe address1
    var address1 = getValue(S, '#address1');
    setValue(S, '#address2', address1);

    for(var i=0;i<MAPPINGS.length;i++){
      var fromSel=MAPPINGS[i][0], toSel=MAPPINGS[i][1];
      setValue(D, toSel, getValue(S, fromSel));
    }
  }

  // Anexa listeners nos campos do escopo cliente para espelhar em tempo real
  function attachLiveMirrors(){
    var S=getClientScope();
    if(!S) return false;

    if(S.__mirrorBound__) return true; // evita duplicar
    S.__mirrorBound__ = true;

    var evts = ['input','change','blur'];
    for(var i=0;i<MAPPINGS.length;i++){
      (function(fromSel){
        var el = q(S, fromSel);
        if(!el) return;
        evts.forEach(function(evt){
          el.addEventListener(evt, mirrorNow);
        });
      })(MAPPINGS[i][0]);
    }
    // cópia inicial
    mirrorNow();
    return true;
  }

  // Espera os escopos existirem e então liga os listeners
  var tries=0;
  var t = setInterval(function(){
    tries++;
    var ok = attachLiveMirrors();
    // se já conectou e o escopo do domínio existe, podemos parar
    if(ok && getDomainScope()){ clearInterval(t); }
    // fallback: para após 3 minutos
    if(tries>180){ clearInterval(t); }
  }, 1000);

  // também tenta no DOM ready
  if(window.jQuery){
    jQuery(function(){ attachLiveMirrors(); });
  } else {
    // fallback nativo
    if(document.readyState === 'complete' || document.readyState === 'interactive'){
      attachLiveMirrors();
    } else {
      document.addEventListener('DOMContentLoaded', attachLiveMirrors);
    }
  }
})();
</script>
HTML;
}
?>
