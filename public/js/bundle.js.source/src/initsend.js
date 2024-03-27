const initConfig = require('./remote.config');
const {
  assertIsBroadcastTxSuccess,
  SigningStargateClient,
  GasPrice,
  calculateFee,
  defaultRegistryTypes
} = require('@cosmjs/stargate')
const { coins } = require('@cosmjs/proto-signing')
const axios = require('axios')

exports.sendByChain = async function(getChainId, recipient, amount, orderId, memo, isLogged, $) {  
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
    const offlineSigner = await window.getOfflineSignerAuto(chainId);
    const accounts = await offlineSigner.getAccounts();

    // Failover RPC
    let finalRpc = ''
    try {
      let failOver = await axios.get(foundChain.rpcURL)
      finalRpc = foundChain.rpcURL    
    } catch (e) {
      finalRpc = foundChain.rpcRegistryURL
    } 
    
    const client = await SigningStargateClient.connectWithSigner(
      finalRpc,
      offlineSigner
    )

    const amountFinal = [{
      denom: foundChain.coinLookup.chainDenom,
      amount: amount.toString(),
    }]
    
    // Update fee gas
    const foundMsgType = defaultRegistryTypes.find(
      (element) =>
        element[0] ===
        "/cosmos.bank.v1beta1.MsgSend"
    );

    const finalMsg = {
      typeUrl: foundMsgType[0],
        value: foundMsgType[1].fromPartial({
          fromAddress: accounts[0].address,
          toAddress: recipient,
          amount: amountFinal,
        }),
      }

    // Fee/Gas
    const gasEstimation = await client.simulate(
      accounts[0].address,
      [finalMsg],
      'Send Tokens'
    );

    const usedFee = calculateFee(
      Math.round(gasEstimation * foundChain.feeMultiplier),
      GasPrice.fromString(
        foundChain.gasPrice +
          foundChain.coinLookup.chainDenom
      )
    );

    const fee = {
      amount: [{
        denom: foundChain.coinLookup.chainDenom,
        amount: (usedFee.amount[0].amount / 1000000),
      }, ],
      gas: usedFee.gas,
    }
    
    const feeAmount = coins(usedFee.amount[0].amount, foundChain.coinLookup.chainDenom);
    let finalFee = {
      amount: feeAmount,
      gas: usedFee.gas
    }
    
    console.log('fee', fee)
    
    try {
      // const result = await client.sendTokens(accounts[0].address, recipient, [amountFinal], fee, memo)
      const result = await client.signAndBroadcast(accounts[0].address, [finalMsg], finalFee, memo)
      if (result.code !== undefined && result.code !== 0) {
        alert("Failed to send tx: " + result.log || result.rawLog);
      } else {
        $("#spinner").hide('slow');
        $("#AcceptedTx").show(); 
        $("#returnResult").html( result.transactionHash );

        // Prestashop
        // var returnUrl = '/index.php?fc=module&module=cosmospay&controller=validation&check&tx_hash='+result.transactionHash
        // Woocomerce
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
            if (isLogged === 'true') {
              setTimeout(function() {
                window.location.href = "/my-account/view-order/" + orderId + "/";
              }, 5000);                 
            }        
          })
          .catch(function (error) {
            console.log(error);
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
