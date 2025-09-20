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
                
            function maskCpfCnpj($el){
                console.log("[DEBUG] maskCpfCnpj() chamado para elemento:", $el);

                var v = digits($el.val());
                console.log("[DEBUG] valor digitado (apenas números):", v);

                if (v.length > 14) v = v.slice(0,14);

                if (v.length <= 11){
                    if (v.length > 9)      v = v.replace(/^(\\d{3})(\\d{3})(\\d{3})(\\d{0,2}).*$/, "\$1.\$2.\$3-\$4");
                    else if (v.length > 6) v = v.replace(/^(\\d{3})(\\d{3})(\\d{0,3}).*$/, "\$1.\$2.\$3");
                    else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3}).*$/, "$1.$2");
                } else {
                    if (v.length > 12)     v = v.replace(/^(\\d{2})(\\d{3})(\\d{3})(\\d{4})(\\d{0,2}).*$/, "\$1.\$2.\$3/\$4-\$5");
                    else if (v.length > 8) v = v.replace(/^(\\d{2})(\\d{3})(\\d{3})(\\d{0,4}).*$/, "\$1.\$2.\$3/\$4");
                    else if (v.length > 5) v = v.replace(/^(\\d{2})(\\d{3})(\\d{0,3}).*$/, "\$1.\$2.\$3");
                    else if (v.length > 2) v = v.replace(/^(\\d{2})(\\d{0,3}).*$/, "\$1.\$2");
                }

                console.log("[DEBUG] valor após aplicar máscara:", v);

                $el.val(v);
                var len = digits(v).length;
                $el.prop('maxLength', (len >= 11 ? 18 : 14));

                console.log("[DEBUG] comprimento detectado:", len);

                if (typeof window.__setDocLen === "function") {
                    console.log("[DEBUG] chamando window.__setDocLen('other',", len, ")");
                    window.__setDocLen('other', len);
                } else {
                    console.warn("[DEBUG] window.__setDocLen não definido!");
                }
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
                            maskCpfCnpj(from); 
                        });
                    });
                }
            }, 300);

        });
    </script>

    HTML;
}
