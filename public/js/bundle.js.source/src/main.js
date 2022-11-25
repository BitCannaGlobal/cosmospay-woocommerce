const {
  SigningCosmosClient
} = require("@cosmjs/launchpad");
 
import {
  DirectSecp256k1HdWallet
} from '@cosmjs/proto-signing'
import {
  assertIsBroadcastTxSuccess,
  SigningStargateClient,
} from '@cosmjs/stargate'
import initConfig from './remote.config'
import initKeplr from './addkeplr'
import initsend from './initsend'
import $ from 'jquery';
window.jQuery = $;
window.$ = $;
 


async function updateUi(data) {
    $("#address").last().html( data.accounts[0].address );
    $("#viewDenom").last().html(data.foundChain.coinLookup.viewDenom);
    $('#inputAddress').val(data.accounts[0].address);  
}
 

$(document).ready(function () {
    //console.log(cosmosConfig)
/*    cosmosConfig.forEach(function(item){ 
      $('#listChains')
 
        .append(`<label class="btn btn-secondary active">
                <input type="radio" id="${item.name}" name="get_chain" value="${item.name}" checked> 
                ${item.name}</label>`)
 
    });   */  
    
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
