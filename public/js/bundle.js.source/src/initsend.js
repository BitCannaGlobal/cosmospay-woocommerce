const initConfig = require('./remote.config');
const {
  SigningCosmosClient
} = require("@cosmjs/launchpad");
const {
  assertIsBroadcastTxSuccess,
  SigningStargateClient,
} = require('@cosmjs/stargate')
const axios = require('axios')

exports.sendByChain = async function(getChainId, recipient, amount, orderId, memo, $) {  
    //console.log(initConfig.default)
    let foundChain = initConfig.default.find(element => element.name === getChainId); 
    //console.log(foundChain)
    
  amount = parseFloat(amount);
  if (isNaN(amount)) {
    alert("Invalid amount");
    return false;
  }

  amount *= 1000000;
  amount = Math.floor(amount);

  (async () => {
    // See above.
    const chainId = foundChain.chainId;
    await window.keplr.enable(chainId);
    const offlineSigner = window.getOfflineSigner(chainId);
    const accounts = await offlineSigner.getAccounts();

    const client = await SigningStargateClient.connectWithSigner(
      foundChain.rpcURL,
      offlineSigner
    )

    const amountFinal = {
      denom: foundChain.coinLookup.chainDenom,
      amount: amount.toString(),
    }
    const fee = {
      amount: [{
        denom: foundChain.coinLookup.chainDenom,
        amount: '5000',
      }, ],
      gas: '200000',
    }
    
    try {
      const result = await client.sendTokens(accounts[0].address, recipient, [amountFinal], fee, memo)
      assertIsBroadcastTxSuccess(result)
      //console.log(result)
      if (result.code !== undefined && result.code !== 0) {
        alert("Failed to send tx: " + result.log || result.rawLog);
      } else {
        $("#spinner").hide('slow');
        $("#AcceptedTx").show(); 
        //alert("Succeed to send tx");
        $("#returnResult").html( result.transactionHash );
        //console.log(order_id);
         
        // var returnUrl = '/index.php?fc=module&module=cosmospay&controller=validation&check&tx_hash='+result.transactionHash
        var returnUrl = '/api-cosmos/?tx_hash='+result.transactionHash+'&order_id='+orderId          
        
        axios.get(returnUrl)
          .then(function (response) {
            console.log(response);
            $("#returnResultStore").html( response.data.message );
            $("#sendForm").hide(); 
            $("#viewFinalTx").show();
  
            $("#checkAdresse").show();
            $("#waitingcheckAdresse").hide();
            $("#checkAdresse").css("color", "#31BF91");
            $("#checkAmount").show();
            $("#waitingcheckAmount").hide();
            $(".woocommerce-thankyou-order-received").css("border-color", "#20c005");
            $(".woocommerce-thankyou-order-received").css("color", "#20c005");
            $(".woocommerce-thankyou-order-received").html("Payment accepted");
            //$("#validateTxAmount").hide('slow');
            $("#validateTx").hide('slow');
            $("#finalUrlTx").attr("href", "https://www.mintscan.io/" + foundChain.mintscanId + "/txs/"+result.transactionHash)
            $("#viewFinalTx").show(1000);
            $("#timer").hide();
            setTimeout(function() {
              window.location.href = "/my-account/view-order/"+orderId+"/";   
            }, 5000);        
          })
          .catch(function (error) {
            // console.log(error);
          });  
      }      
    } catch (e) {
        console.error(e);
        $("#keplrError").html(e);
        $("#spinner").hide();
        $("#cancelTx").show(); 
    } finally {
        // console.log('We do cleanup here');
    } 
    
  })();  
  
};
