<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function firstIn(c, sel){var e=jQuery(c).find(sel).get(0);return e||null}
  function gvIn(c, sel){var e=firstIn(c, sel);return e?e.value:""}
  function svIn(c, sel, v){var e=firstIn(c, sel);if(e)e.value=v}

  function syncPanels(){
    var src = jQuery('#phonenumber').closest('.panel-body');
    var dst = jQuery('#domaincontactphonenumber').closest('.panel-body');
    if(!src.length || !dst.length) return;

    var firstName = gvIn(src,'[name="firstname"]');
    var lastName  = gvIn(src,'[name="lastname"]');
    var email     = gvIn(src,'[name="email"]');
    var phone     = gvIn(src,'#phonenumber,[name="phonenumber"]');
    var address1  = gvIn(src,'[name="address1"]');
    var address2  = gvIn(src,'[name="address2"]');
    var city      = gvIn(src,'[name="city"]');
    var state     = gvIn(src,'[name="state"]');
    var postcode  = gvIn(src,'[name="postcode"]');
    var empresa   = gvIn(src,'[name="companyname"]');

    svIn(dst,'[name="firstname"]', firstName);
    svIn(dst,'[name="lastname"]',  lastName);
    svIn(dst,'[name="email"]',     email);
    svIn(dst,'#domaincontactphonenumber,[name="domaincontactphonenumber"]', phone);
    svIn(dst,'[name="address1"]',  address1);
    svIn(dst,'[name="address2"]',  address2);
    svIn(dst,'[name="city"]',      city);
    svIn(dst,'[name="state"]',     state);
    svIn(dst,'[name="postcode"]',  postcode);
    svIn(dst,'[name="companyname"]', empresa);
  }

  jQuery(function(){
    syncPanels();
    var src = jQuery('#phonenumber').closest('.panel-body');
    if(src.length){
      jQuery(src).on('input change blur', 'input,select', function(){ syncPanels(); });
    }
    setTimeout(syncPanels,300);
    setTimeout(syncPanels,1000);
    setTimeout(syncPanels,2000);
  });
})();
</script>
HTML;
}
?>
