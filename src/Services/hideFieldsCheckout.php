<?php
function hidefields() {
    return <<<HTML
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                // Esconde o campo "Número" e o label
                var numberField = document.getElementById('cl_custom_field_18');
                var numberLabel = document.querySelector('label[for="cl_custom_field_18"]');
                if (numberField) {
                    numberField.style.display = 'none';
                    if (numberLabel) {
                        numberLabel.style.display = 'none'; // Remove o label
                    }
                    numberField.value = 'N/A'; // Atribui "N/A" ao valor do campo
                }

                // Esconde o campo "Como você nos encontrou?" e o label
                var howYouFoundField = document.getElementById('cl_custom_field_24');
                var howYouFoundLabel = document.querySelector('label[for="cl_custom_field_24"]');
                if (howYouFoundField) {
                    howYouFoundField.style.display = 'none';
                    if (howYouFoundLabel) {
                        howYouFoundLabel.style.display = 'none'; // Remove o label
                    }
                }

                // Esconde o campo "Complemento" e o label
                var complementoField = document.getElementById('cl_custom_field_19');
                var complementoLabel = document.querySelector('label[for="cl_custom_field_19"]');
                if (complementoField) {
                    complementoField.style.display = 'none';
                    if (complementoLabel) {
                        complementoLabel.style.display = 'none'; // Remove o label
                    }
                }
            });
        </script>
HTML;
}
?>
