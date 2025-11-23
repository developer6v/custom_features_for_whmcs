<?php



function cpfcnpj_domain_script() {
    return <<<'HTML'
  <style>
  /* Fallback CSS: esconde oficiais por NAME (independente do índice) */
  .domain-info-additional .form-group.row:has(input[name^="domainfield["][name$="[0]"]),
  .domain-info-additional .form-group.row:has(input[name^="domainfield["][name$="[1]"]) {
    display: none !important;
  }

  /* Controller */
  #cpf-cnpj-controller-wrapper { margin-bottom: 15px; }
  #cpf-cnpj-controller { font-size: 14px; }
  #cpf-cnpj-message { display:none; font-size:12px; margin-top:8px; line-height:1.5; }
  </style>

  <script>
  (function(){
    'use strict';

    console.log('[CPF/CNPJ] Script iniciado');
    window.__checkout = window.__checkout || { doc:false };

    // Utils
    function digits(s){ return (s||'').replace(/\D/g,''); }
    function trigger(el, type){ if(!el) return; try{ el.dispatchEvent(new Event(type,{bubbles:true,cancelable:true})); }catch(e){} }

    function maskCpfCnpj(value){
      var v = digits(value||'');
      if (v.length > 14) v = v.slice(0,14);
      if (v.length <= 11){
        if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/,"$1.$2.$3-$4");
        else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/,"$1.$2.$3");
        else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3}).*$/,"$1.$2");
      } else {
        if (v.length > 12)     v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/,"$1.$2.$3/$4-$5");
        else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/,"$1.$2.$3/$4");
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/,"$1.$2.$3");
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3}).*$/,"$1.$2");
      }
      return v;
    }

  

    function isValidCNPJ(cnpj){ return digits(cnpj).length === 14; }

    function recomputeCheckout(){
      var disabled = !window.__checkout.doc;
      document.querySelectorAll('button#checkout, #place_order, button[type="submit"][data-btn-loader], button.btn-primary[type="submit"]').forEach(function(b){
        b.disabled = disabled;
        console.log('[CPF/CNPJ] Botão', b.className || b.id || '(sem class/id)', '- Disabled:', disabled);
      });
    }
    window.__recomputeCheckout = recomputeCheckout;

    // Sincroniza controller -> oficiais
    function syncFields(controllerValue){
      var masked = maskCpfCnpj(controllerValue);
      var d = digits(masked);

      // [*][0] = Register Number
      document.querySelectorAll('input[name^="domainfield["][name$="[0]"]').forEach(function(el){
        if (el.value !== masked){ el.value = masked; trigger(el,'input'); }
      });
      // [*][1] = CPF/CNPJ
      document.querySelectorAll('input[name^="domainfield["][name$="[1]"]').forEach(function(el){
        if (el.value !== masked){ el.value = masked; trigger(el,'input'); }
      });

      var ok = (d.length === 11 && isValidCPF(masked)) || (d.length === 14 && isValidCNPJ(masked));
      window.__checkout.doc = ok;
      recomputeCheckout();
      return masked;
    }

    // Aguarda jQuery (mantém seu padrão)
    function waitForJQuery(cb){
      if (typeof jQuery !== 'undefined') cb(jQuery);
      else setTimeout(function(){ waitForJQuery(cb); }, 100);
    }

    waitForJQuery(function($){
      console.log('[CPF/CNPJ] jQuery detectado, iniciando busca por campos');

      var checkExist = setInterval(function(){
        // Oficiais por NAME (genéricos)
        var field0 = $('input[name^="domainfield["][name$="[0]"]');
        var field1 = $('input[name^="domainfield["][name$="[1]"]');

        // Ids com colchetes — escape correto
        if (!field0.length) field0 = $('#inputDomainfield\\[0\\]\\[0\\], #inputDomainfield\\[1\\]\\[0\\], [id^="inputDomainfield"][id$="\\[0\\]"]');
        if (!field1.length) field1 = $('#inputDomainfield\\[0\\]\\[1\\], #inputDomainfield\\[1\\]\\[1\\], [id^="inputDomainfield"][id$="\\[1\\]"]');

        if ( (field0.length && field1.length) || $('input[name^="domainfield["]').length ){
          clearInterval(checkExist);
          console.log('[CPF/CNPJ] Campos encontrados!');

          var $f0 = field0.first();
          var $f1 = field1.first();

          // Esconde oficiais (com guarda de closest)
          [$f0, $f1].forEach(function($f){
            if (!$f || !$f.length) return;
            var $grp = $f.closest('.form-group, .form-group.row');
            if ($grp && $grp.length) $grp.hide();
          });
          console.log('[CPF/CNPJ] Campos oficiais escondidos');

          // Se já existe controller, não duplica
          if ($('#cpf-cnpj-controller').length){
            console.log('[CPF/CNPJ] Controller já existe, abortando inserção');
          } else {
            // Monta controller
            var newFieldHtml =
              '<div class="form-group row" id="cpf-cnpj-controller-wrapper">'
            + '  <label class="col-form-label col-md-4 col-lg-2" for="cpf-cnpj-controller">CPF ou CNPJ:</label>'
            + '  <div class="col-form-label col-checkbox col-md-8 col-lg-10">'
            + '    <input type="text" id="cpf-cnpj-controller" name="cpf-cnpj-controller" class="form-control input-250 input-inline" maxlength="18" placeholder="Digite CPF ou CNPJ" autocomplete="off">'
            + '    <span id="cpf-cnpj-message" style="display:block; font-size:12px; margin-top:5px; color:#666;">'
            + '      Formato do CPF (11 dígitos): NNN.NNN.NNN-NN<br>'
            + '      Formato do CNPJ (14 dígitos): NN.NNN.NNN/NNNN-NN'
            + '    </span>'
            + '  </div>'
            + '</div>';

            // Insere antes do primeiro oficial escondido, ou no container
            var $anchor = ($f0.length ? $f0 : $f1).closest('.form-group, .form-group.row');
            if ($anchor && $anchor.length){
              $anchor.before(newFieldHtml);
              console.log('[CPF/CNPJ] Campo controller inserido');
            } else {
              $('.domain-info-additional').prepend(newFieldHtml);
              console.log('[CPF/CNPJ] Campo controller inserido (fallback)');
            }
          }

          var $controller = $('#cpf-cnpj-controller');
          // Carrega valor existente se houver
          var existing = ($f1.val() || $f0.val() || '');
          if (existing){
            var masked = maskCpfCnpj(existing);
            $controller.val(masked);
            syncFields(masked);
            console.log('[CPF/CNPJ] Valor existente carregado:', masked);
          }

          // Listener do controller (espelha nos oficiais)
          $controller.on('input change blur', function(){
            var masked = syncFields($controller.val());
            $controller.val(masked);

            var len = digits(masked).length;
            $controller.prop('maxLength', 18); // 18 cobre CPF e CNPJ

            var $msg = $('#cpf-cnpj-message');
            if (len === 11){
              $msg.html(isValidCPF(masked) ? '<span style="color:green; font-weight:bold;">✓ CPF válido</span>' : '<span style="color:red; font-weight:bold;">✗ CPF inválido</span>');
            } else if (len === 14){
              $msg.html(isValidCNPJ(masked) ? '<span style="color:green; font-weight:bold;">✓ CNPJ válido</span>' : '<span style="color:red; font-weight:bold;">✗ CNPJ inválido</span>');
            } else if (len>0 && len<11){
              $msg.html('<span style="color:orange;">CPF incompleto ('+len+'/11)</span>');
            } else if (len>11 && len<14){
              $msg.html('<span style="color:orange;">CNPJ incompleto ('+len+'/14)</span>');
            } else {
              $msg.html('Formato do CPF (11 dígitos): NNN.NNN.NNN-NN<br>Formato do CNPJ (14 dígitos): NN.NNN.NNN/NNNN-NN');
            }
          });

          // Estado inicial dos botões
          window.__checkout.doc = !!(digits($controller.val()).length === 11 || digits($controller.val()).length === 14);
          window.__recomputeCheckout && window.__recomputeCheckout();

          console.log('[CPF/CNPJ] Sistema completamente inicializado!');
        }
      }, 100);

      // Timeout de segurança
      setTimeout(function(){
        clearInterval(checkExist);
        console.log('[CPF/CNPJ] Timeout atingido, busca encerrada');
      }, 10000);
    });
  })();
  </script>
  HTML;
}
