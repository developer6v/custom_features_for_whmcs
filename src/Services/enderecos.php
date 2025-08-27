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


    document.getElementById('inputDCFirstName').value = firstName;
    document.getElementById('inputDCLastName').value = lastName;
    document.getElementById('inputDCEmail').value = email;
    document.getElementById('inputDCPhone').value = phone;
    document.getElementById('inputDCAddress1').value = address1;
    document.getElementById('inputDCAddress2').value = address2;
    document.getElementById('inputDCCity').value = city;
    document.getElementById('inputDCState').value = state;
    document.getElementById('inputDCPostcode').value = postcode;
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
