<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function digits(s){ return (s||'').replace(/\\D/g,''); }


  function getValue(id) {
    var el = document.getElementById(id);
    return el ? el.value : "";
  }

  function setValue(id, value) {
    var el = document.getElementById(id);
    if (el) {
      el.value = value;
    }
  }

  function autofillDomainAddress(){
    console.log("chamou o autofill domain");


    var firstName = getValue("inputFirstName");
    var lastName  = getValue("inputLastName");
    var email     = getValue("inputEmail");
    var phone     = getValue("inputPhone");
    var address1  = getValue("inputAddress1");
    var address2  = getValue("inputAddress2");
    var city      = getValue("inputCity");
    var state     = getValue("inputState");
    var postcode  = getValue("inputPostcode");

    setValue("inputDCFirstName", firstName);
    setValue("inputDCLastName", lastName);
    setValue("inputDCEmail", email);
    setValue("inputDCPhone", phone);
    setValue("inputDCAddress1", address1);
    setValue("inputDCAddress2", address2);
    setValue("inputDCCity", city);
    setValue("inputDCState", state);
    setValue("inputDCPostcode", postcode);
  }

  jQuery(function(){
    autofillDomainAddress();
    jQuery('#inputFirstName, #inputLastName, #inputEmail, #inputPhone, #inputAddress1, #inputAddress2, #inputCity, #inputState, #inputPostcode').on('input', function(){
      autofillDomainAddress(); 
    });
  });
})();
</script>
HTML;
}
