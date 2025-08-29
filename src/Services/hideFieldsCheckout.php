<?php
function hidefields() {
    return <<<HTML
        <script type="text/javascript">
            function checkAndHideFields() {
                // Verifica se os campos e labels existem
                var numberFieldParent = document.querySelector('.form-group #cl_custom_field_18');
                var howYouFoundFieldParent = document.querySelector('.form-group #cl_custom_field_24');
                var complementoFieldParent = document.querySelector('.form-group #cl_custom_field_19');
                
                // Se o "form-group" do campo "Número" existir, esconde ele
                if (numberFieldParent) {
                    numberFieldParent.style.display = 'none'; // Esconde o campo e o label
                }

                // Se o "form-group" do campo "Como você nos encontrou?" existir, esconde ele
                if (howYouFoundFieldParent) {
                    howYouFoundFieldParent.style.display = 'none'; // Esconde o campo e o label
                }

                // Se o "form-group" do campo "Complemento" existir, esconde ele
                if (complementoFieldParent) {
                    complementoFieldParent.style.display = 'none'; // Esconde o campo e o label
                }

                // Se todos os campos forem encontrados, para a execução
                if (numberFieldParent && howYouFoundFieldParent && complementoFieldParent) {
                    return; // Sai da função
                }

                // Se algum campo não foi encontrado, tenta novamente após 100ms
                setTimeout(checkAndHideFields, 100);
            }

            // Chama a função para verificar e esconder os campos
            checkAndHideFields();
        </script>
HTML;
}
?>
