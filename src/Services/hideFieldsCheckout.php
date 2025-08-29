<?php
function hidefields() {
    return <<<HTML
        <script type="text/javascript">
            function checkAndHideFields() {
                // Verifica se os campos e labels existem
                var numberField = document.getElementById('cl_custom_field_18');
                var numberLabel = document.querySelector('label[for="cl_custom_field_18"]');
                var howYouFoundField = document.getElementById('cl_custom_field_24');
                var howYouFoundLabel = document.querySelector('label[for="cl_custom_field_24"]');
                var complementoField = document.getElementById('cl_custom_field_19');
                var complementoLabel = document.querySelector('label[for="cl_custom_field_19"]');
                
                // Se o campo "Número" existir, esconde ele e o label
                if (numberField && numberLabel) {
                    numberField.style.display = 'none';
                    numberLabel.style.display = 'none'; // Remove o label
                    numberField.value = 'N/A'; // Atribui "N/A" ao valor do campo
                }

                // Se o campo "Como você nos encontrou?" existir, esconde ele e o label
                if (howYouFoundField && howYouFoundLabel) {
                    howYouFoundField.style.display = 'none';
                    howYouFoundLabel.style.display = 'none'; // Remove o label
                }

                // Se o campo "Complemento" existir, esconde ele e o label
                if (complementoField && complementoLabel) {
                    complementoField.style.display = 'none';
                    complementoLabel.style.display = 'none'; // Remove o label
                }

                // Se todos os campos forem encontrados, para a execução
                if (numberField && howYouFoundField && complementoField) {
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
