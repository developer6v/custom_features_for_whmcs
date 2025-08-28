<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function autofillDomainAddress() {
    // seleciona o primeiro formulário (cliente)
    var formCliente = document.querySelector("form[data-gtm-form-interact-id]"); 
    // seleciona o segundo formulário (domínio)
    var formDominio = document.querySelector(".panel-body:nth-of-type(2)"); 

    if (!formCliente || !formDominio) return;

    function getValue(form, selector) {
      var el = form.querySelector(selector);
      return el ? el.value : "";
    }
    function setValue(form, selector, value) {
      var el = form.querySelector(selector);
      if (el) el.value = value;
    }

    // coleta valores do formulário cliente
    var firstName = getValue(formCliente, "#firstname");
    var lastName  = getValue(formCliente, "#lastname");
    var email     = getValue(formCliente, "#email");
    var phone     = getValue(formCliente, "#phonenumber");
    var address1  = getValue(formCliente, "#address1");
    var city      = getValue(formCliente, "#city");
    var state     = getValue(formCliente, "#state");
    var postcode  = getValue(formCliente, "#postcode");
    var empresa   = getValue(formCliente, "#companyname");

    // aplica no formulário domínio
    setValue(formDominio, "#firstname", firstName);
    setValue(formDominio, "#lastname", lastName);
    setValue(formDominio, "#email", email);
    setValue(formDominio, "#domaincontactphonenumber", phone);
    setValue(formDominio, "#address1", address1);
    setValue(formDominio, "#city", city);
    setValue(formDominio, "#state", state);
    setValue(formDominio, "#postcode", postcode);
    setValue(formDominio, "#companyname", empresa);
  }

  jQuery(function(){
    autofillDomainAddress();

    var sel = "#firstname, #lastname, #email, #phonenumber, #address1, #city, #state, #postcode, #companyname";
    jQuery(document).on("input change blur", sel, autofillDomainAddress);

    setTimeout(autofillDomainAddress, 300);
    setTimeout(autofillDomainAddress, 1000);
    setTimeout(autofillDomainAddress, 2000);
  });
})();
</script>
HTML;
}
?>
