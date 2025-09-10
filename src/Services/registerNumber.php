jQuery(function() {
  // Verifica periodicamente se os campos estão disponíveis
  var checkExist = setInterval(function() {
    var $from = jQuery('#1');
    var $to = jQuery('#0');

    // Alerta para verificar se os campos estão sendo encontrados
    if ($from.length && $to.length) {
      alert("Campos encontrados");
      clearInterval(checkExist);
      copyOnce();

      // Liga a função copyOnce ao evento 'input' e 'change'
      $from.on('input change', function() {
        copyOnce();
      });
    } else {
      alert("Campos não encontrados");
    }
  }, 300);
});

// Função copyOnce (com uma pequena modificação para depuração)
function copyOnce() {
  var from = document.getElementById('1');
  var to   = document.getElementById('0');
  
  if (!from || !to) {
    alert("Campos não encontrados na função copyOnce");
    return false;
  }

  var val = (from.value != null) ? from.value : '';
  if (to.value !== val) {
    to.value = val;
    ['input', 'change', 'blur'].forEach(function(ev) {
      trigger(to, ev);
    });
    alert("Valor copiado: " + val);  // Alerta com o valor copiado
    maskCpfCnpjRegister(from);  // Aplica a máscara ao campo 'from'
  }
  return true;
}

// Função para aplicar a máscara de CPF/CNPJ (com alerta)
function maskCpfCnpjRegister(el) {
  alert("Máscara chamada");
  var v = digits(el.value);  // Usando el.value ao invés de jQuery
  if (v.length > 14) v = v.slice(0, 14);

  if (v.length <= 11) {
    if (v.length > 9) {
      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
    } else if (v.length > 6) {
      v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
    } else if (v.length > 3) {
      v = v.replace(/^(\d{3})((\d{0,3})).*$/, "$1.$2");
    }
  } else {
    if (v.length > 12) {
      v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
    } else if (v.length > 8) {
      v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/, "$1.$2.$3/$4");
    } else if (v.length > 5) {
      v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
    } else if (v.length > 2) {
      v = v.replace(/^(\d{2})(\d{0,3}).*$/, "$1.$2");
    }
  }

  el.value = v;  // Atualiza o campo com o valor formatado
  alert("Valor formatado: " + v);  // Alerta com o valor formatado

  // Flags globais (AND do checkout)
  window.__checkout.doc = (v.length === 11 || v.length === 14);
  window.__recomputeCheckout();

  toggleCompanyRequired(v.length > 11);
}

function digits(s) {
  return (s || '').replace(/\D/g, '');  // Remove todos os caracteres não numéricos
}
