<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  var syncing=false;

  var groups={
    firstname:  ['[name="firstname"]'],
    lastname:   ['[name="lastname"]'],
    email:      ['[name="email"]'],
    phone:      ['[name="phonenumber"]','[name="domaincontactphonenumber"]'],
    address1:   ['[name="address1"]'],
    city:       ['[name="city"]'],
    state:      ['[name="state"]'],
    postcode:   ['[name="postcode"]'],
    company:    ['[name="companyname"]']
  };

  function firstNonEmpty(list){
    for(var i=0;i<list.length;i++){
      var $els=jQuery(list[i]);
      for(var j=0;j<$els.length;j++){
        var v=$els.eq(j).val();
        if(v!=null && v!=="") return v;
      }
    }
    return "";
  }

  function setAll(list,val,except){
    for(var i=0;i<list.length;i++){
      var $els=jQuery(list[i]);
      $els.each(function(){
        if(this===except) return;
        if(this.value!==val){
          this.value=val;
          if(this.tagName==="SELECT") jQuery(this).trigger("change");
        }
      });
    }
  }

  function init(){
    if(syncing) return;
    syncing=true;
    for(var k in groups){
      var v=firstNonEmpty(groups[k]);
      if(v!=="") setAll(groups[k],v,null);
    }
    syncing=false;
  }

  function bind(){
    var sels=[];
    for(var k in groups){ sels=sels.concat(groups[k]); }
    var uniq=[].filter.call(sels,function(v,i,arr){ return arr.indexOf(v)===i; });
    jQuery(document).on("input change blur", uniq.join(", "), function(){
      if(syncing) return;
      syncing=true;
      var el=this, val=jQuery(this).val();
      for(var k in groups){
        if(jQuery(el).is(groups[k].join(", "))){
          setAll(groups[k],val,el);
        }
      }
      syncing=false;
    });
  }

  jQuery(function(){
    bind();
    init();
    setTimeout(init,300);
    setTimeout(init,1000);
    setTimeout(init,2000);
  });
})();
</script>
HTML;
}
?>
