<?php
function cpfcnpj_script_cart() {
return <<<'HTML'
<script>
console.log("cpf/cnpj CART iniciado");

/* ==========================================
   UTILIDADES
========================================== */
function digits(s){ return String(s||'').replace(/\D/g,''); }

/* ==========================================
   MENSAGEM DE ERRO
========================================== */
function ensureDocMsg(afterEl) {
    const id = "cpf-cnpj-msg-cart";
    let msg = document.getElementById(id);

    if (!msg) {
        msg = document.createElement("span");
        msg.id = id;
        msg.style.cssText = "color:red;font-size:12px;display:block;margin-top:4px;";
        afterEl.parentNode.insertBefore(msg, afterEl.nextSibling);
    }

    return msg;
}

function showDocMsg(input, text) {
    const msg = ensureDocMsg(input);
    msg.textContent = text || "";
}

/* ==========================================
   VALIDADOR REAL CPF
========================================== */
function isValidCPF(v){
    let d = digits(v);

    if (d.length !== 11) return false;
    if (/^(\d)\1{10}$/.test(d)) return false;

    let sum = 0;
    for (let i = 0; i < 9; i++) sum += d[i] * (10 - i);
    let dv1 = (sum * 10) % 11;
    if (dv1 >= 10) dv1 = 0;
    if (dv1 != d[9]) return false;

    sum = 0;
    for (let i = 0; i < 10; i++) sum += d[i] * (11 - i);
    let dv2 = (sum * 10) % 11;
    if (dv2 >= 10) dv2 = 0;

    return dv2 == d[10];
}

/* ==========================================
   CHECKOUT STATE
========================================== */
window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

window.__recomputeCheckout = function(){
    const g = window.__checkout;
    const ok = (g.cep && g.doc && g.company) || g.login;

    document.querySelectorAll("button#checkout, #place_order")
        .forEach(b => b.disabled = !ok);
};

/* ==========================================
   VALIDAÇÃO DO DOC
========================================== */
window.__validateCartDocument = function(input){

    const raw = digits(input.value);
    const len = raw.length;

    let valid = false;

    // CPF
    if (len === 11){
        valid = isValidCPF(raw);
        showDocMsg(input, valid ? "" : "CPF inválido");
    }
    // CNPJ
    else if (len === 14){
        valid = true;
        showDocMsg(input, "");
    }
    // Incompleto
    else if (len > 0){
        valid = false;
        showDocMsg(input, "CPF/CNPJ inválido");
    }
    // Vazio
    else {
        valid = false;
        showDocMsg(input, "");
    }

    window.__checkout.doc = valid;
    window.__recomputeCheckout();
};

/* ==========================================
   MÁSCARA + BINDING
========================================== */
(function(){

    function mask($el){
        let v = digits($el.val());
        if (v.length > 14) v = v.slice(0,14);

        if (v.length <= 11){
            if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2})$/, "$1.$2.$3-$4");
            else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3})$/, "$1.$2.$3");
            else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3})$/, "$1.$2");
        } else {
            if (v.length > 12)     v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})$/, "$1.$2.$3/$4-$5");
            else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4})$/, "$1.$2.$3/$4");
            else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3})$/, "$1.$2.$3");
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3})$/, "$1.$2");
        }

        $el.val(v);
    }

    jQuery(function(){
        var watch = setInterval(function(){

            // campo real do cart
            const $field = jQuery("#customfield1");

            if ($field.length){
                clearInterval(watch);

                console.log("Campo #customfield1 encontrado");

                // inicia máscara
                mask($field);

                // inicia validação
                window.__validateCartDocument($field[0]);

                // bindings
                $field.on("input change blur", function(){
                    mask($field);
                    window.__validateCartDocument($field[0]);
                });
            }

        }, 200);
    });

})();
</script>
HTML;
}
?>
