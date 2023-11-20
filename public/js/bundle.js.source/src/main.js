import initConfig from './remote.config'
import initKeplr from './addkeplr'
import initsend from './initsend'
import $ from 'jquery';
window.jQuery = $;
window.$ = $;
 
jQuery.fn.load = function(callback){ $(window).on("load", callback) };


async function updateUi(data) {
    $("#address").last().html( data.accounts[0].address );
    $("#viewDenom").last().html(data.foundChain.coinLookup.viewDenom);
    $('#inputAddress').val(data.accounts[0].address);  
}
 

$(document).ready(function () {
  var keplrData = ''
  $('input[name="get_chain"]').click(function () { 
    if ($(this).is(':checked')) {
      keplrData = initKeplr.addKeplrChain($(this).val());
        keplrData.then(
          function(value) { 
            updateUi(value)
            console.log(value) 
          },
        function(error) { console.log(error) }
      );  
    }
  });
 
    
});
$(document).ready(function () {
  $("form").submit(async function (event) {
    let recipient = document.sendForm.recipient.value;
    let amount = document.sendForm.amount.value;
    let get_chain = document.sendForm.get_chain.value; 
    let order_id = document.sendForm.order_id.value;
    let memo = document.sendForm.memo.value;
    
    initsend.sendByChain(get_chain, recipient, amount, order_id, memo, $)    
    event.preventDefault();
  });
});

export default { initConfig, initsend, initKeplr };
