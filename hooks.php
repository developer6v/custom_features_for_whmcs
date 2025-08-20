<?php
if (!defined('WHMCS')) { die('Access denied'); }

add_hook('AdminAreaFooterOutput', 1, function($vars) {
    return <<<HTML
<script>
(function(){
  function byName(n){return jQuery('input[name="'+n+'"]');}
  function btns(){return jQuery('button[type="submit"],input[type="submit"]');}
  function onlyDigits(s){return (s||'').replace(/\\D/g,'');}
  function setDisabled(dis){btns().prop('disabled',dis);}
  function validateCep(emitAlert){
    var \$cep=byName('postcode');
    if(!\$cep.length){return;}
    var raw=\$cep.val();
    var cep=onlyDigits(raw);
    if(cep.length!==8){setDisabled(true);return;}
    jQuery.getJSON('https://viacep.com.br/ws/'+cep+'/json/').done(function(d){
      if(d && d.erro){setDisabled(true); if(emitAlert){alert('CEP inválido');}}
      else {setDisabled(false);}
    }).fail(function(){setDisabled(true); if(emitAlert){alert('CEP inválido');}});
  }
  jQuery(function(){
    validateCep(false);
    jQuery(document).on('change blur','input[name="postcode"]',function(){validateCep(true);});
    jQuery(document).on('input','input[name="postcode"]',function(){validateCep(false);});
  });
})();
</script>
HTML;
});
