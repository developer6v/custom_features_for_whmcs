<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function trigger(el,type){if(!el)return;try{el.dispatchEvent(new Event(type,{bubbles:true}));}catch(e){}}
  
  // Funções de escopo e de captura de valores
  function getDomainScope(){ 
    var sel=document.getElementById('inputDomainContact');
    console.log("Procurando inputDomainContact:", sel); // Log para verificar o estado de 'inputDomainContact'
    if (!sel) return null;
    var p=sel.closest('.panel-body');
    console.log("Procurando próximo elemento irmão do inputDomainContact:", p); // Log para verificar se o 'closest' retorna o elemento esperado
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
    console.log("autofilled domain called");
    var S = getClientScope(), D = getDomainScope();
    if(!S || !D){
      console.log("retornando");
      console.log("s", S);
      console.log("d", D);
      return;
    }

    var firstName = getValue(S, '#firstname');
    var lastName = getValue(S, '#lastname');
    var email = getValue(S, '#email');
    var phone = getValue(S, '#phonenumber');
    var phoneCC = getValue(S, 'input[name="country-calling-code-phonenumber"]');
    var address1 = getValue(S, '#address1');
    var city = getValue(S, '#city');
    var state = getValue(S, '#state');
    var postcode = getValue(S, '#postcode');
    var company = getValue(S, '#companyname');
    var country = getValue(S, '#inputCountry');

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
  }

  // Função para detectar mudanças no select
  function handleSelectChange() {
    console.log("Mudança detectada no select!"); // Log para verificar se o evento 'change' foi disparado
    var secondForm = getDomainScope();
    if (secondForm) {
      console.log("Formulario encontrado! Preenchendo...");
      autofillDomainAddress(); // Chama a função de preenchimento quando o formulário é revelado
    } else {
      console.log("Formulario não encontrado.");
    }
  }

  // Verifica periodicamente se o campo 'inputDomainContact' está disponível
  var checkFormExist = setInterval(function() {
    var inputField = document.getElementById('inputDomainContact'); 
    console.log("Procurando campo 'inputDomainContact':", inputField); // Log para verificar se o campo foi encontrado
    if (inputField) {
      // Adiciona o listener de mudança no select com addEventListener
      if (inputField) {
        inputField.addEventListener("change", function() {
          console.log("select alterado"); // Log para verificar se o evento 'change' foi disparado
          handleSelectChange(); // Chama a função de preenchimento quando houver mudança no select
        });
      } else {
        console.log("inputDomainContact não encontrado na inicialização!");
      }


      clearInterval(checkFormExist); // Quando o campo for encontrado, pare de verificar
      console.log("Campo inputDomainContact encontrado!");
      autofillDomainAddress(); // Preenche os dados no segundo formulário automaticamente
    } else {
      console.log("Campo inputDomainContact ainda não encontrado...");
    }
  }, 500); // Verifica a cada 500 milissegundos


  // Inicializa a lógica no carregamento da página
  jQuery(function(){
    console.log("Iniciando autofillDomainAddress no carregamento da página...");
    autofillDomainAddress();  // Preenche os dados automaticamente assim que o documento estiver pronto
  });
})();
</script>
HTML;
}
?>
