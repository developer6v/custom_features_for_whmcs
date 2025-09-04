<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function trigger(el,type){if(!el)return;try{el.dispatchEvent(new Event(type,{bubbles:true}));}catch(e){}}
  
  // Funções de escopo e de captura de valores
  function getDomainScope(){ 
    var sel=document.getElementById('inputDomainContact');
    if (!sel) return null;
    var p=sel.closest('.panel-body');
    return p ? p.nextElementSibling : null; // Retorna o próximo elemento irmão
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
    }
    else {
      if(el.value !== val){
        el.value = val;
        trigger(el, 'input');
        trigger(el, 'change');
        trigger(el, 'blur');
      }
    }
  }

  // Função para preencher os campos
  function autofillDomainAddress(){
    var S = getClientScope(), D = getDomainScope();
    if(!S || !D){
      return;
    }

    var firstName = getValue(S, '#firstname');
    var lastName = getValue(S, '#lastname');
    var email = getValue(S, '#email');
    var phone = getValue(S, '#phonenumber');
    var phoneCC = getValue(S, 'input[name="country-calling-code-phonenumber"]');
    var address1 = getValue(S, '#address1');
    var address2 = getValue(S, '#address2');
    var city = getValue(S, '#city');
    var state = getValue(S, '#state');
    var postcode = getValue(S, '#postcode');
    var company = getValue(S, '#companyname');
    var country = getValue(S, '#inputCountry');

    setValue(S, '#address2', address1);
    setValue(D, '#firstname', firstName);
    setValue(D, '#lastname', lastName);
    setValue(D, '#email', email);
    setValue(D, '#domaincontactphonenumber', phone);
    setValue(D, 'input[name="country-calling-code-domaincontactphonenumber"]', phoneCC);
    setValue(D, '#address1', address1);
    setValue(D, '#city', city);
    setValue(D, '#state', state);
    setValue(D, '#postcode', postcode);
    setValue(D, '#companyname', company);
    setValue(D, '#domaincontactcountry', country);
    setValue(D, '#address2', address2);
  }

  // Função para detectar mudanças no select
  function handleSelectChange() {
    var secondForm = getDomainScope();
    if (secondForm) {
      autofillDomainAddress(); // Chama a função de preenchimento quando o formulário é revelado
    }
  }

  // Verifica periodicamente se o campo 'inputDomainContact' está disponível
  var checkFormExist = setInterval(function() {
    var inputField = document.getElementById('inputDomainContact'); 
    var inputs = ["#firstname", "#lastname", "#email", "#phonenumber", "#address1", "#address2", "#city", "#state", "#postcode", "#companyname", "#inputCountry"];
    
    if (inputField) {
      // Adiciona o listener de mudança para os campos do formulário
      inputs.forEach(function(inputSelector) {
        var inputElement = document.querySelector(inputSelector);
        if (inputElement) {
          inputElement.addEventListener("input", function() {
            autofillDomainAddress(); // Chama a função de preenchimento sempre que qualquer campo for alterado
          });
        }
      });

      // Listener adicional para select
      var selectElement = document.querySelector("#inputDomainContact");
      if (selectElement) {
        selectElement.addEventListener("change", handleSelectChange); // Detecta mudanças no select
      }

      clearInterval(checkFormExist); // Quando o campo for encontrado, pare de verificar
      autofillDomainAddress(); // Preenche os dados no segundo formulário automaticamente
    } 
  }, 500); // Verifica a cada 500 milissegundos

  // Inicializa a lógica no carregamento da página
  jQuery(function(){
    autofillDomainAddress();  // Preenche os dados automaticamente assim que o documento estiver pronto
  });
})();
</script>
HTML;
}
?>
