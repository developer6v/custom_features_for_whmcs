<?php

function registerNumberTeste () {
    return <<<HTML
    <script>

        jQuery(document).ready(function($){
            function digits(s) {
                return (s || '').replace(/\D/g, '');
            }
                
            function maskCpfCnpj($el){
                var v = digits($el.val());
                if (v.length > 14) v = v.slice(0,14);

                if (v.length <= 11){
                    if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
                    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
                    else if (v.length > 3) v = v.replace(/^(\d{3})((\d{0,3})).*$/, "$1.$2");
                } else {
                    if (v.length > 12)     v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
                    else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/, "$1.$2.$3/$4");
                    else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/, "$1.$2.$3");
                    else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3}).*$/, "$1.$2");
                }

                $el.val(v);
                var len = digits(v).length;
                $el.prop('maxLength', (len >= 11 ? 18 : 14));

                // >>> Atualiza o agregador como campo "other"
                window.__setDocLen('other', len);
            }

            var watcher = setInterval(function(){
                console.log("watcher called")
                var from = document.getElementById('1');
                var to   = document.getElementById('0');
                if(from && to){
                    clearInterval(watcher);
                    copyOnce();
                    ['input','change','blur'].forEach(ev => from.addEventListener(ev, function(){ maskCpfCnpj(from); }));
                }
            }, 300);
        });
    </script>

    HTML;
}