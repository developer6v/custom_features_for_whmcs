<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  var panels = document.querySelectorAll('.panel-body');
  var src = (document.getElementById('phonenumber') ? document.getElementById('phonenumber').closest('.panel-body') : null) || panels[0] || document;
  var dst = (document.getElementById('domaincontactphonenumber') ? document.getElementById('domaincontactphonenumber').closest('.panel-body') : null) || panels[1] || panels[0] || document;

  function getValue(id) {
    var el = src.querySelector('#'+id+', [name="'+id+'"]');
    return el ? el.value : "";
  }

  function setValue(id, value) {
    var el = dst.querySelector('#'+id+', [name="'+id+'"]');
    if (el) {
      el.value = value;
      if (el.tagName === 'SELECT' && typeof jQuery !== 'undefined') jQuery(el).trigger('change');
    }
  }

  function autofillDomainAddress(){
    var firstName = getValue("firstname");
    var lastName  = getValue("lastname");
    var email     = getValue("email");
    var phone     = getValue("phonenumber") || getValue("domaincontactphonenumber");
    var address1  = getValue("address1");
    var address2  = getValue("address2");
    var city      = getValue("city");
    var state     = getValue("state");
    var postcode  = getValue("postcode");
    var empresa   = getValue("companyname");

    setValue("firstname", firstName);
    setValue("lastname", lastName);
    setValue("email", email);
    setValue("domaincontactphonenumber", phone);
    setValue("address1", address1);
    setValue("address2", address2);
    setValue("city", city);
    setValue("state", state);
    setValue("postcode", postcode);
    setValue("companyname", empresa);
  }

  jQuery(function(){
    autofillDomainAddress();
    jQuery(src).on('input change blur', 'input,select', function(){ autofillDomainAddress(); });
    setTimeout(autofillDomainAddress, 300);
    setTimeout(autofillDomainAddress, 1000);
    setTimeout(autofillDomainAddress, 2000);
  });
})();
</script>
HTML;
}
?>
