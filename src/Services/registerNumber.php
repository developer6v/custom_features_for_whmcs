<?php
function enderecos() {
    return <<<HTML
<script>
(function(){
  function trigger(el,t){ if(!el) return; try{ el.dispatchEvent(new Event(t,{bubbles:true})); }catch(e){} }

  function copyOnce(){
    var from = document.getElementById('0');
    var to   = document.getElementById('1');
    if(!from || !to) return false;

    var val = (from.value != null) ? from.value : '';
    if(to.value !== val){
      to.value = val;
      ['input','change','blur'].forEach(function(ev){ trigger(to, ev); });
    }
    return true;
  }

  // Aguarda os campos existirem; ao achar, copia e liga o espelhamento em tempo real
  var watcher = setInterval(function(){
    var from = document.getElementById('inputPromotionCode');
    var to   = document.getElementById('customfield19');
    if(from && to){
      clearInterval(watcher);
      copyOnce();
      ['input','change'].forEach(function(ev){
        from.addEventListener(ev, copyOnce);
      });
    }
  }, 300);
})();
</script>
HTML;
}
?>
