<?php

function registerNumberTeste () {
    return <<<HTML
    <script>

        jQuery(document).ready(function($) {
            alert("document caleled")
            console.log("[DEBUG] jQuery(document).ready disparou");

            function digits(s) {
                console.log("[DEBUG] digits() chamado com:", s);
                return (s || '').replace(/\\D/g, '');
            }
                
       

            var watcher = setInterval(function(){
                console.log("[DEBUG] watcher chamado...");

                var from = document.getElementById('1');
                var to   = document.getElementById('0');

                console.log("[DEBUG] from:", from, "to:", to);

                if(from && to){
                    console.log("[DEBUG] elementos encontrados! Limpando watcher e configurando listeners.");
                    clearInterval(watcher);

                    copyOnce(); // cuidado: se essa função não existir, vai quebrar

                    ['input','change','blur'].forEach(ev => {
                        console.log("[DEBUG] adicionando listener para evento:", ev);
                        from.addEventListener(ev, function(){ 
                            console.log("[DEBUG] evento", ev, "detectado no campo FROM");
                        });
                    });
                }
            }, 300);

        });
    </script>

    HTML;
}
