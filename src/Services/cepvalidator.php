<?php
function cepvalidator_script() {
    return <<<HTML
    <script>
    (function(){
    function byName(n){return jQuery('input[name="'+n+'"]');}
    function btns() {
        return jQuery('button[type="submit"], input[type="submit"], button#checkout');
    }
    function onlyDigits(s){return (s||'').replace(/\\D/g,'');}
    function setDisabled(dis){btns().prop('disabled',dis);}
    function showMsg(msg){
        var \$cep=byName('postcode');
        var id="cep-validator-msg";
        var \$msg=jQuery("#"+id);
        if(!\$msg.length){
            \$msg=jQuery('<span id="'+id+'" style="color:red;font-size:12px;display:block;margin-top:4px;"></span>');
            \$cep.after(\$msg);
        }
        \$msg.text(msg||'');
    }
    function validateCep(){
        console.log("chamou")
        var \$cep=byName('postcode');
        if(!\$cep.length){return;}
        var cep=onlyDigits(\$cep.val());
        if(cep.length!==8){
            setDisabled(true);
            showMsg('CEP inválido');
            return;
        }
        jQuery.getJSON('https://viacep.com.br/ws/'+cep+'/json/').done(function(d){
            if(d && d.erro){
                setDisabled(true);
                showMsg('CEP inválido');
            } else {
                setDisabled(false);
                showMsg('');
            }
        }).fail(function(){
            setDisabled(true);
            showMsg('CEP inválido');
        });
    }

    // Função para aplicar a máscara de CEP
    function applyCepMask() {
        var \$cep = byName('postcode');
        if ($\cep.length) {
            \$cep.mask('00000-000'); // Máscara de CEP no formato XXXXX-XXX
        }
    }

    // Verifica se o campo de CEP existe, e se sim, aplica a máscara e validação
    var checkExist = setInterval(function() {
        if (jQuery("input[name='postcode']").length) {
            clearInterval(checkExist); // Quando o elemento aparecer, pare de verificar
            // Aplique a máscara de CEP e a validação
            applyCepMask();
            jQuery(document).on('change blur input','input[name="postcode"]',function(){validateCep();});
        }
    }, 100); // Verifique a cada 100 milissegundos
    })();
    </script>
    HTML;
}
