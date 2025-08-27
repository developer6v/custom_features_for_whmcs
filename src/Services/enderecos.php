<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function getValue(id) {
    var el = document.getElementById(id);
    return el ? el.value : "";
  }

  function setValue(id, value) {
    var el = document.getElementById(id);
    if (el) el.value = value;
  }

  function autofillDomainAddress(){
    var firstName = getValue("inputFirstName");
    var lastName  = getValue("inputLastName");
    var email     = getValue("inputEmail");
    var phone     = getValue("inputPhone");
    var address1  = getValue("inputAddress1");
    var address2  = getValue("inputAddress2");
    var city      = getValue("inputCity");
    var state     = getValue("stateselect"); // pode vir de <select> sem problema
    var postcode  = getValue("inputPostcode");
    var empresa  = getValue("inputCompanyName");

    setValue("inputDCFirstName", firstName);
    setValue("inputDCLastName", lastName);
    setValue("inputDCEmail", email);
    setValue("inputDCPhone", phone);
    setValue("inputDCAddress1", address1);
    setValue("inputDCAddress2", address2);
    setValue("inputDCCity", city);
    setValue("inputDCState", state);
    setValue("inputDCPostcode", postcode);
    setValue("inputDCCompanyName", empresa);
  }

  // jQuery ready
  jQuery(function(){
    // 1) Sincroniza imediatamente
    autofillDomainAddress();

    // 2) Eventos que cobrem digitação, colagem e selects
    var sel = '#inputCompanyName, #inputFirstName, #inputLastName, #inputEmail, #inputPhone, #inputAddress1, #inputAddress2, #inputCity, #inputState, #inputPostcode';
    jQuery(document).on('input change blur', sel, function(){
      autofillDomainAddress();
    });

    // 3) "Safeguard" para autofill do navegador (alguns só preenchem após paint)
    setTimeout(autofillDomainAddress, 300);
    setTimeout(autofillDomainAddress, 1000);
    setTimeout(autofillDomainAddress, 2000);
  });
})();
</script>
HTML;
}
?>
