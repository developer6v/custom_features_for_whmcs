<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function $$all(key){
    var list=[];
    var byId=document.getElementById(key); if(byId) list.push(byId);
    var byName=document.getElementsByName(key); for(var i=0;i<byName.length;i++){ list.push(byName[i]); }
    return list;
  }
  function getValue(key){
    var els=$$all(key);
    for(var i=0;i<els.length;i++){
      var v=els[i].value;
      if(v!=null && v!=="") return v;
    }
    return "";
  }
  function setValue(key,value){
    var els=$$all(key);
    for(var i=0;i<els.length;i++){
      if(els[i].value!==value){
        els[i].value=value;
        if(els[i].tagName==="SELECT"){ if(typeof jQuery!=="undefined") jQuery(els[i]).trigger("change"); }
      }
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
    setValue("phonenumber", phone);
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
    var sel=[
      '[name="firstname"]','[name="lastname"]','[name="email"]',
      '[name="phonenumber"]','[name="domaincontactphonenumber"]',
      '[name="address1"]','[name="address2"]','[name="city"]',
      '[name="state"]','[name="postcode"]','[name="companyname"]',
      '#firstname','#lastname','#email','#phonenumber','#domaincontactphonenumber',
      '#address1','#address2','#city','#state','#postcode','#companyname'
    ].join(", ");
    jQuery(document).on("input change blur", sel, function(){ autofillDomainAddress(); });
    setTimeout(autofillDomainAddress,300);
    setTimeout(autofillDomainAddress,1000);
    setTimeout(autofillDomainAddress,2000);
  });
})();
</script>
HTML;
}
?>
