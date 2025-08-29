<?php
function hidefields() {
    return <<<HTML
        <script type="text/javascript">
            function checkAndHideFields() {
                // Verifica o "form-group" do campo "Número"
                var numberFieldParent = document.querySelector('.form-group #cl_custom_field_18');
                // Verifica o "form-group" do campo "Como você nos encontrou?"
                var howYouFoundFieldParent = document.querySelector('.form-group #cl_custom_field_24');
                // Verifica o "form-group" do campo "Complemento"
                var complementoFieldParent = document.querySelector('.form-group #cl_custom_field_19');
                
                // Se o "form-group" do campo "Número" existir, esconde ele
                if (numberFieldParent) {
                    numberFieldParent.closest('.form-group').style.display = 'none'; // Esconde o form-group inteiro
                }

                // Se o "form-group" do campo "Como você nos encontrou?" existir, esconde ele
                if (howYouFoundFieldParent) {
                    howYouFoundFieldParent.closest('.form-group').style.display = 'none'; // Esconde o form-group inteiro
                }

                // Se o "form-group" do campo "Complemento" existir, esconde ele
                if (complementoFieldParent) {
                    complementoFieldParent.closest('.form-group').style.display = 'none'; // Esconde o form-group inteiro
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
