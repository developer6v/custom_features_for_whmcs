<?php
function cpfcnpj_script() {
    return <<<HTML
<script>
(function(){
  function digits(s){ return (s||'').replace(/\\D/g,''); }

  function toggleCompanyRequired(isCnpj){
    var \$company = jQuery('input[name="companyname"]');
    if(!\$company.length) return;
    if(isCnpj){
      \$company.attr('required', 'required').attr('aria-required', 'true');
    }else{
      \$company.removeAttr('required').removeAttr('aria-required');
    }
  }

  function maskCpfCnpj(\$el){
    var v = digits(\$el.val());
    if(v.length > 14) v = v.slice(0,14);
    if(v.length <= 11){
      if(v.length > 9){
        v = v.replace(/^(\\d{3})(\\d{3})(\\d{3})(\\d{0,2}).*$/, "\$1.\$2.\$3-\$4");
      } else if(v.length > 6){
        v = v.replace(/^(\\d{3})(\\d{3})(\\d{0,3}).*$/, "\$1.\$2.\$3");
      } else if(v.length > 3){
        v = v.replace(/^(\\d{3})(\\d{0,3}).*$/, "\$1.\$2");
      }
    } else {
      if(v.length > 12){
        v = v.replace(/^(\\d{2})(\\d{3})(\\d{3})(\\d{4})(\\d{0,2}).*$/, "\$1.\$2.\$3/\$4-\$5");
      } else if(v.length > 8){
        v = v.replace(/^(\\d{2})(\\d{3})(\\d{3})(\\d{0,4}).*$/, "\$1.\$2.\$3/\$4");
      } else if(v.length > 5){
        v = v.replace(/^(\\d{2})(\\d{3})(\\d{0,3}).*$/, "\$1.\$2.\$3");
      } else if(v.length > 2){
        v = v.replace(/^(\\d{2})(\\d{0,3}).*$/, "\$1.\$2");
      }
    }
    \$el.val(v);
    toggleCompanyRequired(digits(v).length > 11);
  }

  jQuery(function(){
    var \$field = jQuery('#customfield1');
    if(\$field.length){
      maskCpfCnpj(\$field);
      \$field.on('input', function(){ maskCpfCnpj(\$field); });
    }
  });
})();
</script>
HTML;
}
