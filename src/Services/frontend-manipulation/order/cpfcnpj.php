<?php
function cpfcnpj_script() {
   return <<<'HTML'
<script>
/* =====================================================================
   SISTEMA DE CPF/CNPJ PARA WHMCS (COM ALERTS DE DEBUG)
   - Máscara para controlador e cl_custom_field_1
   - Validação matemática real de CPF
   - Aceita CNPJ (14 dígitos)
   - Integra com window.__checkout
   - Mantém compatibilidade com Lagom
===================================================================== */

// =====================================================================
//  FUNÇÕES GLOBAIS COMPARTILHADAS
// =====================================================================

// =====================================================================
// EXIBIR MENSAGEM DE ERRO PARA CPF/CNPJ
// =====================================================================
function ensureDocMsg(afterEl) {
    const id = "cpf-cnpj-msg";
    let msg = document.getElementById(id);

    if (!msg) {
        msg = document.createElement("span");
        msg.id = id;
        msg.style.cssText = "color:red;font-size:12px;display:block;margin-top:4px;";
        
        if (afterEl && afterEl.parentNode) {
            afterEl.parentNode.insertBefore(msg, afterEl.nextSibling);
        } else {
            document.body.appendChild(msg);
        }
    }
    return msg;
}

function showDocMsg(input, text) {
    ensureDocMsg(input).textContent = text || "";
}

function digits(s){ return String(s||'').replace(/\D/g,''); }

// --- Validador REAL de CPF ---
function isValidCPF(v, element){
    var d = digits(v);

    if (d.length !== 11) {
        showDocMsg(element, "CPF com tamanho incorreto");
        return false;
    }
    if (/^(\d)\1{10}$/.test(d)) {
        showDocMsg(element, "CPF inválido (sequência repetida)");
        return false;
    }

    var sum = 0;
    for (var i = 0; i < 9; i++) sum += parseInt(d.charAt(i)) * (10 - i);
    var dv1 = (sum * 10) % 11;
    if (dv1 >= 10) dv1 = 0;
    if (dv1 != d[9]) {
        showDocMsg(element, "Dígito verificador incorreto");
        return false;
    }

    sum = 0;
    for (var i = 0; i < 10; i++) sum += parseInt(d.charAt(i)) * (11 - i);
    var dv2 = (sum * 10) % 11;
    if (dv2 >= 10) dv2 = 0;
    if (dv2 != d[10]) {
        showDocMsg(element, "Dígito verificador incorreto");
        return false;
    }

    showDocMsg(element, ""); // LIMPA mensagem
    return true;
}



// =====================================================================
//  INICIALIZAÇÃO DO SISTEMA (AGGREGATOR)
// =====================================================================
window.__checkout = window.__checkout || { cep:false, doc:false, company:true, login:false };

(function initAggregator(){
    if (window.__initCompanyAggregator) return;
    window.__initCompanyAggregator = true;

    window.__docState = { reg:0, other:0 };


    // ------------------------------------------------------
    // Carrega campo Empresa
    // ------------------------------------------------------
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

        window.__checkout.company = !required || company.value.trim().length > 0;
    }

    function attachCompanyListenerOnce(){
        var company = getCompanyInput();
        if (!company || company._companyListenerAttached) return;
        company._companyListenerAttached = true;

        company.addEventListener("input", function(){
            var required = company.hasAttribute('required');
            window.__checkout.company = !required || company.value.trim().length > 0;
            window.__recomputeCheckout();
        });
    }


    // ------------------------------------------------------
    // RECOMPUTE PRINCIPAL (onde valida CPF/CNPJ)
    // ------------------------------------------------------
    window.__recomputeCompany = function(){
        var elCtrl  = document.getElementById('cpfcnpjregistercontroller');
        var elOther = document.getElementById('cl_custom_field_1');

        var reg = elCtrl ? digits(elCtrl.value) : "";
        var oth = elOther ? digits(elOther.value) : "";

        var regLen = reg.length;
        var othLen = oth.length;

        // Empresa obrigatória se CNPJ
        var anyCnpj = regLen === 14 || othLen === 14;
        setCompanyRequired(anyCnpj);
        attachCompanyListenerOnce();


        // =====================================================
        //  🔥 Validação FINAL do Documento
        // =====================================================
        var docValid = false;

        // Caso CPF (11 dígitos)
        if (regLen === 11) docValid = isValidCPF(reg, elCtrl);
        else if (othLen === 11) docValid = isValidCPF(oth, elOther);

        // Caso CNPJ (14 dígitos)
        if (regLen === 14 || othLen === 14)
            docValid = true; // TODO: adicionar validador real de CNPJ se quiser

        // Caso vazio ou tamanho incompleto → inválido
        if (
            (regLen > 0 && regLen < 11) ||
            (othLen > 0 && othLen < 11) ||
            (regLen > 11 && regLen < 14) ||
            (othLen > 11 && othLen < 14)
        ){
            showDocMsg(elCtrl || elOther, "CPF/CNPJ inválido");
            docValid = false;
        }

        if (regLen === 0 && othLen === 0){
            showDocMsg(elCtrl || elOther, "");
            docValid = false;
        }
        window.__checkout.doc = docValid;
        window.__recomputeCheckout();
    };


    // usado pelas máscaras
    window.__setDocLen = function(source, len){
        if (source === 'reg') window.__docState.reg = len;
        if (source === 'other') window.__docState.other = len;
        window.__recomputeCompany();
    };

})();



// =====================================================================
//  RECÁLCULO DO BOTÃO CHECKOUT
// =====================================================================
window.__recomputeCheckout = function () {
    const g = window.__checkout;
    const valid = (g.cep && g.doc && g.company) || g.login;
    document.querySelectorAll('button#checkout, #place_order').forEach(b => b.disabled = !valid);
};



// =====================================================================
//  MÁSCARAS E COPIADOR
// =====================================================================
(function(){

    function trigger(el,t){
        if(!el) return;
        try{ el.dispatchEvent(new Event(t,{bubbles:true})); }catch(e){}
    }

    // ------------------------------------------------------------------
    // Máscara / Controlador PRINCIPAL
    // ------------------------------------------------------------------
    function maskCpfCnpjRegister($el){
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


    // ------------------------------------------------------------------
    // Máscara do campo original cl_custom_field_1
    // ------------------------------------------------------------------
    function maskCpfCnpjOther($el){
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
        window.__setDocLen("other", digits(v).length);
    }



    // ------------------------------------------------------------------
    // Copia de controller → campos #1 e #0 do Lagom
    // ------------------------------------------------------------------
    function copyOnce(){
        var from = document.getElementById('cpfcnpjregistercontroller');
        var t1 = document.getElementById('1');
        var t2 = document.getElementById('0');

        if(!from || (!t1 && !t2)) return;

        var val = from.value || "";

        [t1, t2].forEach(function(to){
            if (!to) return;
            if (to.value !== val){
                to.value = val;
                trigger(to, "input");
                trigger(to, "change");
                trigger(to, "blur");
            }
        });
    }



    // ------------------------------------------------------------------
    // INSERIR CONTROLADOR
    // ------------------------------------------------------------------
    var watcher = setInterval(function(){
        var $from = jQuery('#1');
        var $to   = jQuery('#0');

        if($from.length && $to.length){
            clearInterval(watcher);

            var formGroup = $from.closest('.form-group__wrapper');
            if (formGroup.length)
                formGroup.prepend('<input type="text" class="form-control" id="cpfcnpjregistercontroller" name="Cpf/CNPJ">');
            else
                jQuery('#1').before('<input type="text" class="form-control" id="cpfcnpjregistercontroller" name="Cpf/CNPJ">');

            var $new = jQuery('#cpfcnpjregistercontroller');

            $new.on("input change blur", function(){
                maskCpfCnpjRegister($new);
                copyOnce();
            });

            maskCpfCnpjRegister($new);
            copyOnce();
        }
    }, 200);



    // ------------------------------------------------------------------
    // MÁSCARA PARA O cl_custom_field_1
    // ------------------------------------------------------------------
    jQuery(function(){
        var watch2 = setInterval(function(){
            var $other = jQuery('#cl_custom_field_1');
            if ($other.length){
                clearInterval(watch2);

                maskCpfCnpjOther($other);
                $other.on("input change blur", function(){
                    maskCpfCnpjOther($other);
                });
            }
        }, 200);
    });

})();
</script>
HTML;
}
?>
