function startChecking( order_id, mainDomain, finalApiUrl, memo, isBlocked, isLogged, nonceSelectChain, nonceDeleteOrder, nonceSwitchMethod, setDefault, isMobile ) {

  $('.woocommerce-thankyou-order-received').hide()
  $('.woocommerce-order-details').hide()
  $('.woocommerce-customer-details').hide()


  $( '#chainIcon' ).hide()
  $( '#chainIcon2' ).hide()
  $( '#mainTransaction' ).hide() 
  
  if ( isBlocked === 'true' ) {    
    $.post( finalApiUrl, { order_id: order_id, finalData: 'finalData' }, async function ( result ) {    
      let foundChain = await exportCosmosConfig.initConfig.find( element => element.name === result.current_chain )
      $( '#finalAmount2, #finalAmount3' ).html( result.OrderPrice + ' ' + foundChain.coinLookup.viewDenom )  
      $( '#chainIcon2, #chainIcon3' ).attr( 'src', foundChain.coinLookup.icon )
      $( '#chainIcon2, #chainIcon3' ).show( )
      timerOrder( result.startTime ) 
      
      if( result.method === 'keplr' ) { 
        $( "#mainTransaction2" ).fadeIn( 500 )      
        keplrData = await exportCosmosConfig.initKeplr.addKeplrChain( result.current_chain )
        exportCosmosConfig.initsend.sendByChain( result.current_chain, result.adressToPay, result.OrderPrice, order_id, memo, isLogged, $ )  
      } else {
        $( "#mainTransaction2" ).fadeOut( 500 )
        $( "#mainTransaction3" ).fadeIn( 500 )
        $( "#spinner" ).hide()
        $( "#recipient" ).val( result.adressToPay )
        $( "#memo" ).val( result.memo )   
        myTimer(result.adressToPay, result.OrderPrice, result.memo)
      }
    }) 
  } else {
    $('#mainTransaction').show();
    // https://store-wp.walaxy.io/index.php?name=api-cosmos
    // $.post( mainDomain+"/api-cosmos/", { switch: setDefault, order_id: order_id, nonce: nonceSelectChain }, async function( result ) {
    $.post( finalApiUrl, { switch: setDefault, order_id: order_id, nonce: nonceSelectChain }, async function( result ) {
      let foundChain = await exportCosmosConfig.initConfig.find( element => element.name === setDefault );
      $( "#returnChain" ).html( result.current_chain )
      $( "#returnLcd" ).html( result.lcd )
      $( "#finalAmount" ).html( result.OrderPrice + ' ' + result.chainDenom )
      $( "#inputAmount" ).val( result.OrderPrice ) 
      $( "#inputAddress" ).val( result.adressToPay )
      $( "#tableInputAddress").html( result.adressToPay )
      $( "#chainIcon" ).attr( 'src', foundChain.coinLookup.icon )
      $( "#chainIcon" ).show()
      timerOrder( result.startTime )
    });  
 
    let finalMethod = ''
    if(isMobile === true) {
      finalMethod = 'another'
    } else
      finalMethod = 'keplr'
      
      console.log(isMobile)
      console.log(finalMethod)
    $.post( finalApiUrl, { switchMethod: finalMethod, order_id: order_id, nonce: nonceSwitchMethod  }, function( result ) {
    });      
  }
    
  
  $( '#sendStep2' ).click(function () {
      $( '#mainTransaction' ).hide()
      $.post( finalApiUrl, { order_id: order_id, finalData: 'finalData' }, async function ( result ) {    
        let foundChain = exportCosmosConfig.initConfig.find( element => element.name === result.current_chain )
        $( '#finalAmount2, #finalAmount3' ).html( result.OrderPrice + ' ' + foundChain.coinLookup.viewDenom )
        $( '#chainIcon2, #chainIcon3' ).attr('src', foundChain.coinLookup.icon)
        $( '#chainIcon2, #chainIcon3' ).show()

        timerOrder( result.startTime )
        if( result.method === 'keplr' ) {
          $( '#mainTransaction' ).fadeOut( 500 )
          $( '#mainTransaction2' ).fadeIn( 500 )
          keplrData = await exportCosmosConfig.initKeplr.addKeplrChain( result.current_chain )
          exportCosmosConfig.initsend.sendByChain(result.current_chain, result.adressToPay, result.OrderPrice, order_id, memo, isLogged, $)  
        } else {
          $( '#mainTransaction2' ).fadeOut( 500 )
          $( '#mainTransaction3' ).fadeIn( 500 )
          $( "#spinner" ).hide()
          $( "#recipient" ).val( result.adressToPay )
          $( "#memo" ).val( result.memo )
          myTimer( result.adressToPay, result.OrderPrice, result.memo )
        }          
      })
  })
  
  $( '#retry' ).click(function () {
    $( "#spinner" ).show()
    $( "#cancelTx" ).hide()
    
    $.post( finalApiUrl, { order_id: order_id, finalData: 'finalData' }, async function ( result ) {    
      let foundChain = exportCosmosConfig.initConfig.find( element => element.name === result.current_chain )
      
      keplrData = await exportCosmosConfig.initKeplr.addKeplrChain( result.current_chain )
      exportCosmosConfig.initsend.sendByChain( result.current_chain, result.adressToPay, result.OrderPrice, order_id, memo, isLogged, $ )
    }) 
  })
  
  $( '#selectChain' ).change( function() {

      let foundChain = exportCosmosConfig.initConfig.find( element => element.name === $(this).val() )    
      $.post( finalApiUrl, { switch: $(this).val(), order_id: order_id, nonce: nonceSelectChain }, function( result ) {
        $( "#returnChain" ).html( result.current_chain )
        $( "#returnLcd" ).html( result.lcd )
        $( "#finalAmount" ).html( result.OrderPrice + ' ' + result.chainDenom )
        $( "#inputAmount" ).val( result.OrderPrice )  
        $( "#inputAddress" ).val( result.adressToPay )
        $( "#tableInputAddress" ).html( result.adressToPay )
      });    
      $( '#chainIcon' ).attr( 'src', foundChain.coinLookup.icon ) 
      $( '#chainIcon' ).show()
  });

  $( '#selectMethod' ).change( function() {
      $.post( finalApiUrl, { switchMethod: $(this).val(), order_id: order_id, nonce: nonceSwitchMethod }, function( result ) {
      })  
  });
 
  $( '#cancel, #cancel2' ).click( function () { 
    $.post( finalApiUrl, { order_id: order_id, cancel: 'true', nonce: nonceDeleteOrder }, function( result ) {
      window.location.reload()
    })  
  }) 
   

  function timerOrder( time ) {
    var timestamp = ( time * 1000 )      
    var date = new Date( timestamp )

    const countDownDateTime = timestamp  + 3600000 // 1 hour = 3600000 // 10 mn = 600000

    const minutesValue = document.querySelector( "#minutes" )
    const secondsValue = document.querySelector( "#seconds" )

    // run this function every 1000 ms or 1 second
    let cosmosTime = setInterval( function () {
      const dateTimeNow = new Date( ).getTime( )
      let difference = countDownDateTime - dateTimeNow
      // calculating time and assigning values
      minutesValue.innerHTML = Math.floor(
        ( difference % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 )
      );
      secondsValue.innerHTML = Math.floor( (difference % (1000 * 60)) / 1000 )
      if ( difference < 0 ) {
        clearInterval( cosmosTime )
        $( "#spinner" ).hide()
        $( "#cancelTx" ).show()
        $( "#cancelTx1" ).show()      
        $( "#retry" ).hide()
        $( "#mainPay" ).hide()
        
        minutesValue.innerHTML = '00'
        secondsValue.innerHTML = '00'
        $.post( finalApiUrl, { order_id: order_id, cancel: 'true', nonce: nonceDeleteOrder }, function( result ){
          // console.log(result)
          window.location.reload()
        })        
      }
    }, 1000 )
  }
}

 
