<?php
function cepvalidator_script() {
    return <<<HTML
<script>
;(() => {
  // --- Utils (vanilla) ---
  const $ = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => root.querySelectorAll(sel);
  const onlyDigits = s => (s||'').replace(/\\D/g, '');
  const getSubmitButtons = () =>
    Array.from($$('button[type="submit"], input[type="submit"], button#checkout, #place_order'));
  const setDisabled = dis => getSubmitButtons().forEach(b => b.disabled = !!dis);

  const ensureMsgEl = (afterEl) => {
    const id = 'cep-validator-msg';
    let msg = document.getElementById(id);
    if (!msg) {
      msg = document.createElement('span');
      msg.id = id;
      msg.style.cssText = 'color:red;font-size:12px;display:block;margin-top:4px;';
      if (afterEl && afterEl.parentNode) {
        afterEl.parentNode.insertBefore(msg, afterEl.nextSibling);
      } else {
        document.body.appendChild(msg);
      }
    }
    return msg;
  };
  const showMsg = (input, text) => { ensureMsgEl(input).textContent = text || ''; };

  const maskCep = (input) => {
    input.addEventListener('input', () => {
      let v = onlyDigits(input.value).slice(0,8);
      if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
      input.value = v;
    });
  };

  const validateCep = (input) => {
    const cep = onlyDigits(input.value);
    if (cep.length !== 8) { setDisabled(true); showMsg(input, 'CEP inválido'); return; }
    fetch('https://viacep.com.br/ws/'+cep+'/json/')
      .then(r => r.json())
      .then(d => {
        if (d && d.erro) { setDisabled(true); showMsg(input, 'CEP inválido'); }
        else { setDisabled(false); showMsg(input, ''); }
      })
      .catch(() => { setDisabled(true); showMsg(input, 'CEP inválido'); });
  };

  // --- Busca repetida até achar um campo de CEP ---
  const SELECTORS = [
    'input[name="postcode"]',
    'input[name="billing_postcode"]',
    'input[name="shipping_postcode"]',
    'input[id*="postcode"]',
    'input[name*="postcode"]',
    '#postcode', '#billing_postcode', '#shipping_postcode'
  ].join(',');

  let tries = 0;
  const timer = setInterval(() => {
    tries++;
    const input = $(SELECTORS);
    if (input) {
      clearInterval(timer);
      maskCep(input);
      setDisabled(true);
      if (input.value) validateCep(input);
      ['input','blur','change'].forEach(evt => input.addEventListener(evt, () => validateCep(input)));
    } else if (tries > 180) {
      clearInterval(timer); // aborta após 3 minutos
    }
  }, 1000);
})();
</script>
HTML;
}
?>
