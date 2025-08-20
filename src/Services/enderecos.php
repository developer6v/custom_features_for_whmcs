<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function digits(s){ return (s||'').replace(/\\D/g,''); }

  function autofillDomainAddress(){
    var firstName = document.getElementById("inputFirstName").value;
    var lastName = document.getElementById("inputLastName").value;
    var email = document.getElementById("inputEmail").value;
    var phone = document.getElementById("inputPhone").value;
    var address1 = document.getElementById("inputAddress1").value;
    var address2 = document.getElementById("inputAddress2").value;
    var city = document.getElementById("inputCity").value;
    var state = document.getElementById("inputState").value;
    var postcode = document.getElementById("inputPostcode").value;

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
