<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function trigger(el, type){
    if (!el) return;
    try { el.dispatchEvent(new Event(type,{bubbles:true})); } catch(e){}
  }
  function getDomainScope(){
    var sel = document.getElementById('inputDomainContact');
    return sel ? sel.closest('.panel-body') : null;
  }
  function getClientScope(){
    var domainScope = getDomainScope();
    if (!domainScope) return null;
    var p = domainScope;
    while (p) {
      p = p.previousElementSibling;
      if (!p) break;
      if (p.classList && p.classList.contains('panel-body')) {
        if (p.querySelector('#phonenumber') || p.querySelector('form[data-gtm-form-interact-id]')) {
          return p;
        }
      }
    }
    var alt = document.querySelector('form[data-gtm-form-interact-id]');
    return alt ? (alt.closest('.panel-body') || document) : null;
  }
  function q(scope, sel){ return scope ? scope.querySelector(sel) : null; }
  function getValue(scope, sel){
    var el = q(scope, sel);
    return el ? el.value : "";
  }
  function setValue(scope, sel, value){
    var el = q(scope, sel);
    if (!el) return;
    if (el.tagName === 'SELECT'){
      var prev = el.value;
      el.value = value;
      if (el.value !== value){
        var opts = el.options || [];
        for (var i=0;i<opts.length;i++){
          if (opts[i].text === value){ el.value = opts[i].value; break; }
        }
      }
      if (el.value !== prev) trigger(el,'change');
    } else {
      if (el.value !== value){
        el.value = value;
        trigger(el,'input'); trigger(el,'change'); trigger(el,'blur');
      }
    }
  }
  function autofillDomainAddress(){
    var S = getClientScope(), D = getDomainScope();
    if (!S || !D) return;

    var firstName = getValue(S,'#firstname');
    var lastName  = getValue(S,'#lastname');
    var email     = getValue(S,'#email');
    var phone     = getValue(S,'#phonenumber');
    var phoneCC   = getValue(S,'input[name="country-calling-code-phonenumber"]');
    var address1  = getValue(S,'#address1');
    var city      = getValue(S,'#city');
    var state     = getValue(S,'#state');
    var postcode  = getValue(S,'#postcode');
    var company   = getValue(S,'#companyname');
    var country   = getValue(S,'#inputCountry');

    setValue(D,'#firstname', firstName);
    setValue(D,'#lastname',  lastName);
    setValue(D,'#email',     email);
    setValue(D,'#domaincontactphonenumber', phone);
    setValue(D,'input[name="country-calling-code-domaincontactphonenumber"]', phoneCC);
    setValue(D,'#address1',  address1);
    setValue(D,'#city',      city);
    setValue(D,'#state',     state);
    setValue(D,'#postcode',  postcode);
    setValue(D,'#companyname', company);
    setValue(D,'#domaincontactcountry', country);
  }
  function bindListeners(){
    var S = getClientScope(), D = getDomainScope();
    if (S){
      var sel = [
        '#firstname','#lastname','#email','#phonenumber',
        '#address1','#city','#state','#postcode','#companyname','#inputCountry',
        'input[name="country-calling-code-phonenumber"]'
      ].join(', ');
      S.addEventListener('input', function(e){ if (e.target && e.target.matches(sel)) autofillDomainAddress(); });
      S.addEventListener('change', function(e){ if (e.target && e.target.matches(sel)) autofillDomainAddress(); });
      S.addEventListener('blur', function(e){ if (e.target && e.target.matches(sel)) autofillDomainAddress(); });
    }
    if (D){
      var dc = D.querySelector('#inputDomainContact');
      if (dc){
        dc.addEventListener('change', autofillDomainAddress);
      }
    }
  }
  jQuery(function(){
    autofillDomainAddress();
    bindListeners();
    setTimeout(autofillDomainAddress,300);
    setTimeout(autofillDomainAddress,1000);
    setTimeout(autofillDomainAddress,2000);
  });
})();
</script>
HTML;
}
?>
