<?php

function cpfcnpj_script_cart() {
    return <<<'HTML'
<script>
console.log("cpfcnpj CART carregado");

/* ============================================================
   Utils
============================================================ */
function digits(s){ return String(s||'').replace(/\D/g,''); }

/* ============================================================
   Mensagem de erro
============================================================ */
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
    ensureDocMsg(input).textContent = text || "";
}

/* ============================================================
   Validador REAL de CPF
============================================================ */
function isValidCPF(v, element){
    var d = digits(v);

    if (d.length !== 11) {
        showDocMsg(element, "CPF com tamanho incorreto");
        return false;
    }
    if (/^(\d)\1{10}$/.test(d)) {
        showDocMsg(element, "CPF inválido");
        return false;
    }

    var sum = 0;
    for (var i = 0; i < 9; i++) sum += parseInt(d.charAt(i)) * (10 - i);
    var dv1 = (sum * 10) % 11;
    if (dv1 >= 10) dv1 = 0;

    if (dv1 != d[9]) {
        showDocMsg(element, "Dígito verificador inválido");
        return false;
    }

    sum = 0;
    for (var i = 0; i < 10; i++) sum += parseInt(d.charAt(i)) * (11 - i);
    var dv2 = (sum * 10) % 11;
    if (dv2 >= 10) dv2 = 0;

    if (dv2 != d[10]) {
        showDocMsg(element, "Dígito verificador inválido");
        return false;
    }

    showDocMsg(element, "");
    return true;
}

/* ============================================================
   Aggregator do CART
============================================================ */
window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

(function ensureAggregator(){
    if (window.__initCompanyAggregatorCart) return;
    window.__initCompanyAggregatorCart = true;

    window.__docState = { reg:0 };

    function getCompany(){
        return document.querySelector('input[name="companyname"]');
    }

    function setCompanyRequired(required){
        var company = getCompany();
        if (!company) return;

        var formGroup = company.closest('.form-group');
        if (!formGroup) return;

        var label = formGroup.querySelector("label");
        if (label){
            label.textContent = required ? "Empresa" : "Empresa (opcional)";
        }

        window.__checkout.company = !required || (company.value.trim().length > 0);
    }

    function attachCompanyListenerOnce(){
        var company = getCompany();
        if (!company || company._done) return;
        company._done = true;
        company.addEventListener("input", function(){
            var required = company.hasAttribute("required");
            window.__checkout.company = !required || (company.value.trim().length > 0);
            window.__recomputeCheckout();
        });
    }

    window.__recomputeCompany = function(){
        var len = window.__docState.reg;

        // Se for CNPJ → empresa obrigatória
        var isCnpj = len === 14;
        setCompanyRequired(isCnpj);
        attachCompanyListenerOnce();

        // =====================================================
        // 🔥 Validação final do documento no CART
        // =====================================================
        var docValid = false;
        var input = document.getElementById("customfield1");
        if (!input) return;

        var raw = digits(input.value);

        if (raw.length === 11){
            docValid = isValidCPF(raw, input);
        }
        else if (raw.length === 14){
            // Aceita CNPJ sem verificação
            showDocMsg(input, "");
            docValid = true;
        }
        else if (raw.length > 0){
            showDocMsg(input, "CPF/CNPJ inválido");
            docValid = false;
        }
        else {
            // vazio
            showDocMsg(input, "");
            docValid = false;
        }

        window.__checkout.doc = docValid;
        window.__recomputeCheckout();
    };

    window.__setDocLen = function(source, len){
        window.__docState.reg = len;
        window.__recomputeCompany();
    };

})();

/* ============================================================
   Recompute do botão checkout (CART)
============================================================ */
window.__recomputeCheckout = function(){
    const g = window.__checkout;
    const valid = (g.cep && g.doc && g.company) || g.login;
    document.querySelectorAll("button#checkout, #place_order")
        .forEach(b => b.disabled = !valid);
};

/* ============================================================
   Máscara + binding
============================================================ */
(function(){
    function digits(s){ return (s||'').replace(/\D/g,''); }

    function maskCpfCnpj($el){
        var v = digits($el.val());
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
        window.__setDocLen("reg", digits(v).length);
    }

    jQuery(function(){
        var watch = setInterval(function(){
            var $field = jQuery("#customfield1");
            if ($field.length){
                clearInterval(watch);

                maskCpfCnpj($field);
                window.__recomputeCompany();

                $field.on("input change blur", function(){
                    maskCpfCnpj($field);
                });
            }
        }, 200);
    });
})();
</script>
HTML;
}
?>
