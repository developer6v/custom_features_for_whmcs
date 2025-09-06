<?php
function cepvalidator_script() {
    return <<<HTML
<script>
(function(){
  'use strict';
  alert('[cepvalidator] init');

  function onlyDigits(s){ return (s||'').replace(/\\D/g,''); }
  function btns(){ return jQuery('button[type="submit"], input[type="submit"], button#checkout, #place_order'); }
  function setDisabled(dis){ btns().prop('disabled', dis); }

  function showMsg($cep, msg){
    var id = 'cep-validator-msg';
    var $msg = jQuery('#'+id);
    if(!$msg.length){
      $msg = jQuery('<span id="'+id+'" style="color:red;font-size:12px;display:block;margin-top:4px;"></span>');
      $cep.after($msg);
    }
    $msg.text(msg || '');
  }

  function maskCep($cep){
    $cep.on('input', function(){
      var v = onlyDigits(this.value).slice(0,8);
      if(v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
      this.value = v;
    });
  }

  function validateCep($cep){
    var cep = onlyDigits($cep.val());
    if(cep.length !== 8){
      setDisabled(true);
      showMsg($cep, 'CEP inválido');
      return;
    }
    jQuery.getJSON('https://viacep.com.br/ws/'+cep+'/json/')
      .done(function(d){
        if(d && d.erro){
          setDisabled(true);
          showMsg($cep, 'CEP inválido');
        }else{
          setDisabled(false);
          showMsg($cep, '');
        }
      })
      .fail(function(){
        setDisabled(true);
        showMsg($cep, 'CEP inválido');
      });
  }

  // procura repetidamente até achar o campo
  var interval = setInterval(function(){
    var $cep = jQuery('input[name="postcode"], #postcode, #billing_postcode, #shipping_postcode');
    if($cep.length){
      console.log('[cepvalidator] campo encontrado');
      clearInterval(interval);

      maskCep($cep);
      setDisabled(true);
      validateCep($cep);

      $cep.on('input blur change', function(){
        validateCep($cep);
      });
    }else{
      console.log('[cepvalidator] procurando campo...');
    }
  }, 1000);

})();
</script>
HTML;
}
?>
