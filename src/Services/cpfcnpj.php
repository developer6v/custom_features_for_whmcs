<?php
function cpfcnpj_script() {
    return <<<HTML
<script>
(function(){
  function digits(s){ return (s||'').replace(/\D/g,''); }

  function toggleCompanyRequiredInScope($field, isCnpj){
    var $scope = $field.closest('.panel-body');
    var $company = $scope.find('input[name="companyname"]');
    if(!$company.length) $company = jQuery('input[name="companyname"]'); 
    if(isCnpj){ $company.attr('required','required').attr('aria-required','true'); }
    else { $company.removeAttr('required').removeAttr('aria-required'); }
  }

  function maskCpfCnpj($el){
    var d = digits($el.val());
    if(d.length > 14) d = d.slice(0,14);
    var v = d;

    if(d.length <= 11){
      $el.attr('maxlength','14');
      if(d.length > 9)       v = d.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*$/, "$1.$2.$3-$4");
      else if(d.length > 6)  v = d.replace(/^(\d{3})(\d{3})(\d{0,3}).*$/,      "$1.$2.$3");
      else if(d.length > 3)  v = d.replace(/^(\d{3})(\d{0,3}).*$/,             "$1.$2");
      toggleCompanyRequiredInScope($el, false);
    } else {
      $el.attr('maxlength','18');
      if(d.length > 12)      v = d.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*$/, "$1.$2.$3/$4-$5");
      else if(d.length > 8)  v = d.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*$/,        "$1.$2.$3/$4");
      else if(d.length > 5)  v = d.replace(/^(\d{2})(\d{3})(\d{0,3}).*$/,               "$1.$2.$3");
      else if(d.length > 2)  v = d.replace(/^(\d{2})(\d{0,3}).*$/,                      "$1.$2");
      toggleCompanyRequiredInScope($el, true);
    }

    if($el.val()!==v) $el.val(v);
  }

  function applyOnceToVisible(){
    jQuery('input#customfield1, input#cl_custom_field_1, input[name="customfield[1]"]').each(function(){
      maskCpfCnpj(jQuery(this));
    });
  }

  jQuery(function(){
    applyOnceToVisible();

    jQuery(document).on('input blur paste', 'input#customfield1, input#cl_custom_field_1, input[name="customfield[1]"]', function(){
      maskCpfCnpj(jQuery(this));
    });

    var mo = new MutationObserver(function(){
      applyOnceToVisible();
    });
    mo.observe(document.documentElement, { childList:true, subtree:true });
    
    setTimeout(applyOnceToVisible, 300);
    setTimeout(applyOnceToVisible, 1000);
    setTimeout(applyOnceToVisible, 2000);
  });
})();
</script>
HTML;
}
?>
