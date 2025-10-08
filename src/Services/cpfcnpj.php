<?php
function cpfcnpj_script() {
    return <<<'HTML'
  <script>
    window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

    // Agregador já definido no outro script; se não, define aqui rapidamente
    (function ensureAggregator(){
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

         var formGroup = company.closest('.form-group');
    if (!formGroup) {
      console.log("form-group não encontrado");
      return;
    }

      var label = formGroup.querySelector('label');
      if (label) {
        // Log para verificar o texto que será aplicado
        console.log('Alterando label para: ' + (required ? 'Empresa' : 'Empresa (opcional)'));
        label.textContent = required ? 'Empresa' : 'Empresa (opcional)';
      } else {
        console.log("Label não encontrada");
      }
  
        window.__checkout.company = !required || (company.value.trim().length > 0);
      }

      function setMessageCPFCNPJ(required){
        var spanMes =  document.querySelector('#cpf-cnpj-message');
        if (spanMes) {
          spanMes.innerHTML  = "encontrou";  
        } else {
          console.log("nao encontrou o campo")
        }
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
        var anyCnpj = (window.__docState.reg === 14) || (window.__docState.other === 14); // Verifica se o CNPJ tem 14 caracteres
        setCompanyRequired(anyCnpj);
        setMessageCPFCNPJ(anyCnpj);
        attachCompanyListenerOnce();
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

    window.__recomputeCheckout = function() {
      const g = window.__checkout;
      const disabled = !(g.login) && !(g.cep && g.doc && g.company);
      document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
    };

    (function(){
      function digits(s){ return (s||'').replace(/\D/g,''); }

      function maskCpfCnpj($el){
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

      jQuery(function(){
        var checkExist = setInterval(function() {
          var $field = jQuery('#cl_custom_field_1');
          if ($field.length) {
            clearInterval(checkExist);

            // Criação do elemento de erro logo após encontrar o campo
            var messageElement = document.createElement('span');
            messageElement.id = 'cpf-cnpj-message';
            messageElement.style.fontSize = '12px';
            messageElement.style.marginTop = '5px';
            $field.parent().append(messageElement);  // Adiciona o elemento abaixo do campo

            maskCpfCnpj($field);
            $field.on('input change blur', function(){ maskCpfCnpj($field); });
          }
        }, 100);
      });
    })();
  </script>
  HTML;
}


function cpfcnpj_script_cart() {
    return <<<'HTML'
  <script>
        console.log("script cpfcnpj")

  window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

  (function ensureAggregator(){
    if (window.__initCompanyAggregator) return;
    window.__initCompanyAggregator = true;
    window.__docState = { reg:0, other:0 };

   function getCompanyInput(){
    return document.querySelector('input[name="companyname"]');
  }

  function setCompanyRequired(required) {
    console.log("funcao sendo chamada")
    var company = document.querySelector('input[name="companyname"]'); // Seleciona o input pelo name
    if (!company) {
      console.log("Campo 'companyname' não encontrado");
      return;
    }

    var formGroup = company.closest('.form-group');
    if (!formGroup) {
      console.log("form-group não encontrado");
      return;
    }

    var label = formGroup.querySelector('label');
    if (label) {
      // Log para verificar o texto que será aplicado
      console.log('Alterando label para: ' + (required ? 'Empresa' : 'Empresa (opcional)'));
      label.textContent = required ? 'Empresa' : 'Empresa (opcional)';
    } else {
      console.log("Label não encontrada");
    }
 

    window.__checkout = window.__checkout || {};
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
      var anyCnpj = (window.__docState.reg > 11) || (window.__docState.other > 11);
      setCompanyRequired(anyCnpj);
      attachCompanyListenerOnce();
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

  window.__recomputeCheckout = function() {
    const g = window.__checkout;
    const disabled = !(g.login) && !(g.cep && g.doc && g.company);
    document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
  };

  (function(){
    function digits(s){ return (s||'').replace(/\D/g,''); }

    function maskCpfCnpj($el){
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
      window.__setDocLen('reg', len);

    }

    jQuery(function(){
      var checkExist = setInterval(function() {
        var $field = jQuery('#customfield1');
        if ($field.length) {
          clearInterval(checkExist);
          maskCpfCnpj($field);
          $field.on('input change blur', function(){ maskCpfCnpj($field); });
        }
      }, 100);
    });
  })();
</script>

HTML;
}




function cpfcnpj_script_admin() {
    return <<<'HTML'
  <script>
    window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

    (function ensureAggregator(){
      if (window.__initCompanyAggregator) return;
      window.__initCompanyAggregator = true;
      window.__docState = { reg:0, other:0 };
      
      function getCompanyInput(){
        return document.querySelector('input[name="companyname"]');
      }

      function setCompanyRequired(required){
        var company = getCompanyInput();
        if (!company) return;

        var formGroup = company.closest('td.fieldarea'); // Encontrando a célula que contém o input
        var label = formGroup ? formGroup.querySelector('small') : null;

        if (required) {
          company.setAttribute('required','required');
          company.setAttribute('aria-required','true');
          if (label) {
            label.textContent = 'Empresa'; // Atualiza o texto do label
            label.style.display = 'none'; // Esconde o "(Opcional)".
          }
        } else {
          company.removeAttribute('required');
          company.removeAttribute('aria-required');
          if (label) {
            label.textContent = 'Empresa (opcional)';
            label.style.display = 'inline'; // Exibe novamente o "(Opcional)".
          }
        }

        window.__checkout = window.__checkout || {};
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
        var anyCnpj = (window.__docState.reg === 14) || (window.__docState.other === 14); // Verifica se o CNPJ tem 14 caracteres
        setCompanyRequired(anyCnpj);
        attachCompanyListenerOnce();
        var docValid = [window.__docState.reg, window.__docState.other].some(l => l === 11 || l === 14); // Verifica se o CPF tem 11 ou CNPJ tem 14 dígitos
        window.__checkout.doc = docValid;
        window.__recomputeCheckout && window.__recomputeCheckout();
      };

      window.__setDocLen = function(source, len){
        if (source === 'reg') window.__docState.reg = len;
        else window.__docState.other = len;
        window.__recomputeCompany();
      };
    })();

    window.__recomputeCheckout = function() {
      const g = window.__checkout;
      const disabled = !(g.login) && !(g.cep && g.doc && g.company);
      const submitButton = document.querySelector('input[type="submit"][value="Adicionar Cliente"]');
      if (submitButton) {
        submitButton.disabled = disabled;
      }
    };

    (function(){
      function digits(s){ return (s||'').replace(/\D/g,''); } // Remove qualquer coisa que não seja dígito

      function maskCpfCnpj($el){
        var v = digits($el.val()); // Extrai apenas os dígitos

        // Limite de caracteres para CPF e CNPJ
        if (v.length > 14) v = v.slice(0,14);

        // Máscara para CPF
        if (v.length <= 11){
          if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
          else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
          else if (v.length > 3) v = v.replace(/^(\d{3})((\d{0,3})).*$/, "$1.$2");
        } else {
          // Máscara para CNPJ
          if (v.length > 12)     v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
          else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/, "$1.$2.$3/$4");
          else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
          else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3}).*$/, "$1.$2");
        }

        $el.val(v); // Atualiza o valor do campo com a máscara
        var len = digits(v).length; // Comprimento final sem os caracteres não numéricos
        $el.prop('maxLength', (len >= 11 ? 18 : 14)); // Define o maxLength

        // Atualiza a contagem de caracteres no agregador
        window.__setDocLen('other', len);
      }

      jQuery(function(){
        var checkExist = setInterval(function() {
          var $field = jQuery('#customfield1');
          if ($field.length) {
            clearInterval(checkExist);
            maskCpfCnpj($field);
            $field.on('input change blur', function(){ 
              maskCpfCnpj($field); 
            });
          }
        }, 100);
      });
    })();
  </script>
  HTML;
}


function cpfcnpj_domain_script() {
    return <<<'HTML'
<style>
/* Esconde os campos oficiais */
.domain-info-additional .form-group.row:has(#inputDomainfield\[0\]\[0\]),
.domain-info-additional .form-group.row:has(#inputDomainfield\[0\]\[1\]) {
    display: none !important;
}
</style>

<script>
window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

// Agregador global
(function ensureAggregator(){
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

    var label = formGroup ? formGroup.querySelector('label') : null;
    if (label) {
      label.textContent = required ? 'Empresa' : 'Empresa (opcional)';
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

  window.__recomputeCompany = function(){
    var anyCnpj = (window.__docState.reg === 14) || (window.__docState.other === 14);
    setCompanyRequired(anyCnpj);
    attachCompanyListenerOnce();
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

window.__recomputeCheckout = function() {
  const g = window.__checkout;
  const disabled = !(g.login) && !(g.cep && g.doc && g.company);
  document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = disabled);
};

(function(){
  function digits(s){ return (s||'').replace(/\D/g,''); }
  function trigger(el,t){ 
    if(!el) return; 
    try{ el.dispatchEvent(new Event(t,{bubbles:true})); }catch(e){} 
  }

  function maskCpfCnpj(value){
    var v = digits(value);
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

    return v;
  }

  function syncFields(controllerValue){
    var digitsOnly = digits(controllerValue);
    var masked = maskCpfCnpj(controllerValue);
    
    // Atualiza campo Register Number (oculto) - apenas dígitos
    var field0 = document.querySelector('input[name="domainfield[0][0]"]');
    if (field0 && field0.value !== digitsOnly) {
      field0.value = digitsOnly;
      trigger(field0, 'input');
      trigger(field0, 'change');
    }
    
    // Atualiza campo CPF/CNPJ (oculto) - com máscara
    var field1 = document.querySelector('input[name="domainfield[0][1]"]');
    if (field1 && field1.value !== masked) {
      field1.value = masked;
      trigger(field1, 'input');
      trigger(field1, 'change');
    }

    // Atualiza agregador
    window.__setDocLen('reg', digitsOnly.length);
    
    return masked;
  }

  jQuery(function($){
    var checkExist = setInterval(function() {
      var field0 = $('input[name="domainfield[0][0]"]');
      var field1 = $('input[name="domainfield[0][1]"]');
      
      if (field0.length && field1.length) {
        clearInterval(checkExist);
        
        // Esconde os campos oficiais via jQuery também
        field0.closest('.form-group.row').hide();
        field1.closest('.form-group.row').hide();
        
        // Cria o novo campo controller
        var newFieldHtml = `
          <div class="form-group row" id="cpf-cnpj-controller-wrapper">
            <label class="col-form-label col-md-4 col-lg-2" for="cpf-cnpj-controller">
              CPF ou CNPJ:
            </label>
            <div class="col-form-label col-checkbox col-md-8 col-lg-10">
              <input type="text" 
                     id="cpf-cnpj-controller" 
                     name="cpf-cnpj-controller"
                     class="form-control input-250 input-inline" 
                     maxlength="18"
                     placeholder="Digite CPF ou CNPJ">
              <span id="cpf-cnpj-message" style="display:block; font-size:12px; margin-top:5px; color:#666;">
                Formato do CPF (11 dígitos): NNN.NNN.NNN-NN<br>
                Formato do CNPJ (14 dígitos): NN.NNN.NNN/NNNN-NN
              </span>
            </div>
          </div>
        `;
        
        // Insere o novo campo antes do primeiro campo oculto
        field0.closest('.form-group.row').before(newFieldHtml);
        
        var $controller = $('#cpf-cnpj-controller');
        
        // Preenche o controller com valor existente (se houver)
        var existingValue = field1.val() || field0.val() || '';
        if (existingValue) {
          $controller.val(maskCpfCnpj(existingValue));
          syncFields(existingValue);
        }
        
        // Listener no campo controller
        $controller.on('input change blur', function(){
          var masked = syncFields($controller.val());
          $controller.val(masked);
          
          var len = digits(masked).length;
          $controller.prop('maxLength', (len >= 11 ? 18 : 14));
          
          // Atualiza mensagem de validação
          var $message = $('#cpf-cnpj-message');
          if (len === 11) {
            $message.html('<span style="color:green;">✓ CPF válido (11 dígitos)</span>');
          } else if (len === 14) {
            $message.html('<span style="color:green;">✓ CNPJ válido (14 dígitos)</span>');
          } else if (len > 0 && len < 11) {
            $message.html('<span style="color:orange;">CPF incompleto (' + len + '/11 dígitos)</span>');
          } else if (len > 11 && len < 14) {
            $message.html('<span style="color:orange;">CNPJ incompleto (' + len + '/14 dígitos)</span>');
          } else {
            $message.html('Formato do CPF (11 dígitos): NNN.NNN.NNN-NN<br>Formato do CNPJ (14 dígitos): NN.NNN.NNN/NNNN-NN');
          }
        });
        
        console.log('Campo CPF/CNPJ controller criado e sincronizado com sucesso!');
      }
    }, 100);
  });
})();
</script>
HTML;
}