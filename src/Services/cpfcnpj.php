<?php
function cpfcnpj_script() {
    return <<<HTML
<script>
(function(){
  function digits(s){ return (s||'').replace(/\D/g,''); }

  function toggleCompanyRequired(isCnpj){
    var $company = jQuery('input[name="companyname"]');
    if(!$company.length) return;
    if(isCnpj){ $company.attr('required','required').attr('aria-required','true'); }
    else { $company.removeAttr('required').removeAttr('aria-required'); }
  }

  function maskCpfCnpj($el){
    console.log("mask was called")
    var d = digits($el.val());
    if(d.length > 14) d = d.slice(0,14);
    var v = d;

    if(d.length <= 11){
      // CPF
      $el.attr('maxlength','14');
      if(d.length > 9)       v = d.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
      else if(d.length > 6)  v = d.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/,      "$1.$2.$3");
      else if(d.length > 3)  v = d.replace(/^(\d{3})(\d{0,3}).*$/,             "$1.$2");
    } else {
      // CNPJ
      $el.attr('maxlength','18');
      if(d.length > 12)      v = d.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
      else if(d.length > 8)  v = d.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/,        "$1.$2.$3/$4");
      else if(d.length > 5)  v = d.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/,               "$1.$2.$3");
      else if(d.length > 2)  v = d.replace(/^(\d{2})(\d{0,3}).*$/,                      "$1.$2");
    }

    $el.val(v);
    toggleCompanyRequired(d.length > 11);
  }

  jQuery(function(){
    var $field  = jQuery('#customfield1');
    var $field2 = jQuery('#cl_custom_field_1');

    if($field.length){
      maskCpfCnpj($field);
      $field.on('input blur paste', function(){ maskCpfCnpj($field); });
    }

    if($field2.length){
      maskCpfCnpj($field2);
      $field2.on('input blur paste', function(){ maskCpfCnpj($field2); });
    }
  });
})();
</script>
HTML;
}
