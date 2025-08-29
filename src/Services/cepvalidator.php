<?php
function cepvalidator_script() {
    return <<<HTML
    <script>
    (function(){
        function byName(n){ return jQuery('input[name="'+n+'"]'); }
        function btns() {
            return jQuery('button[type="submit"], input[type="submit"], button#checkout');
        }
        function onlyDigits(s){ return (s||'').replace(/\D/g,''); }
        function setDisabled(dis){ btns().prop('disabled', dis); }
        function showMsg(msg){
            var $cep = byName('postcode');
            var id = "cep-validator-msg";
            var $msg = jQuery("#" + id);
            if (!$msg.length) {
                $msg = jQuery(
                    '<span id="'+id+'" style="color:red;font-size:12px;display:block;margin-top:4px;"></span>'
                );
                $cep.after($msg);
            }
            $msg.text(msg || '');
        }
        function validateCep(){
            var $cep = byName('postcode');
            if (!$cep.length) { return; }
            var cep = onlyDigits($cep.val());
            if (cep.length !== 8){
                setDisabled(true);
                showMsg('CEP inválido');
                return;
            }
            jQuery.getJSON('https://viacep.com.br/ws/'+cep+'/json/').done(function(d){
                if (d && d.erro) {
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

        // Função para adicionar a máscara de CEP
        function maskCep() {
            var $cep = byName('postcode');
            if (!$cep.length) { return; }
            $cep.mask('00000-000');  // Máscara para o CEP
        }

        // Função para garantir que o campo esteja pronto para aplicar a máscara
        function checkAndApplyMask() {
            var $cep = byName('postcode');
            if ($cep.length) {
                maskCep(); // Aplica a máscara quando o campo estiver disponível
                clearInterval(maskInterval); // Para o intervalo após aplicar a máscara
            }
        }

        jQuery(function(){
            var maskInterval = setInterval(checkAndApplyMask, 100); // Verifica a cada 100ms se o campo foi carregado
            setDisabled(true);
            validateCep();
            jQuery(document).on('change', 'input[name="postcode"]', function(){ validateCep(); });
        });
    })();
    </script>
    HTML;
}
?>
