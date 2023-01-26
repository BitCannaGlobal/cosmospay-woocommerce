<?php 
  $permalinkApi = get_permalink( get_option("cosmos_api_post_id") );
  $dbMemo = wc_get_order_item_meta( $order_id , '_cosmos_memo', true );
  $isBlocked = wc_get_order_item_meta( $order_id , '_cosmos_blocked', true );
  
  $rules = get_option( 'rewrite_rules' );
  if (!empty($rules)) {
    $finalApiUrl = get_home_url() . '/api-cosmos';
  } else {
    $cosmos_api_post_id = get_option( 'cosmos_api_post_id' );
    $finalApiUrl = get_home_url() . '/?page_id=' . $cosmos_api_post_id;
  }
  
  if ( is_user_logged_in() ) {
    $isLogged = 'true';
  } else {
    $isLogged = 'false';
  }  
 
  if( wp_is_mobile() ){
    $isMobile = 'true';
  } else {
    $isMobile = 'false';
  }  
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
 
<br /><br />
<div class="cosmos-card" id="mainTransaction">
  <br />
  <div id="mainPay">
    <div class="cosmos-payinfo">
      <div class="cosmos-card-title">
        <img style="vertical-align:middle" id="chainIcon" src="" width="25" height="25">  
        <div class="cosmos-h3" id="finalAmount"></div> 
      </div>
      <div class="cosmos-card-amount">
        <?php echo get_woocommerce_currency_symbol() ?>  <?php echo esc_attr( $order->get_total() ) ?>
        <?php echo get_woocommerce_currency() ?>          
      </div>
    </div>
    
    <div class="cosmos-content">
      Select your cryptocurrency:
      <div class="box">
        <select id="selectChain">
          <?php     
            
            foreach ( $selected_payment_method->form_fields['option_name']['options'] as $key => $value ) {                    
              $keyAvaible = array_search( $key, $selected_payment_method->settings['option_name'] );  
              if ( $keyAvaible === 0 || !empty( $keyAvaible ) ) {
                $setDefault = esc_attr( $selected_payment_method->form_fields['option_name']['options'][$selected_payment_method->settings['option_name'][0]] );
              ?>
          <option id="<?php echo esc_attr( $value ); ?>"><?php echo esc_attr( $value ); ?></option>
          <?php }                
            }
          ?> 
        </select>
      </div>
    
      <br /><br />
 
      Select your prefered way to pay:
      <div class="box">
        <select id="selectMethod">
          <?php if (!wp_is_mobile()) { ?>
            <option value="keplr">Pay automatically with keplr</option>
          <?php } ?>          
          <option value="another">Pay with another wallet</option>
        </select>
      </div>
      <br /><br />
      <button class="buttonSend" id="sendStep2">Next</button>
      <div class="cancelTx" align="center"> 
        <a href="" id="cancel" style="color: red;"><u>Cancel</u></a>
      </div>
    </div>
  </div>
  <div id="cancelTx1" align="center" style="display: none;">
    <img src="<?php echo plugins_url(); ?>/<?php echo esc_attr($this->plugin_name); ?>/public/img/cancel.png" width="75" height="75">
    <br /><br />
    <div class="cosmos-h5">Your payment has been canceled (The time is over)</div>
    <br /><br />
  </div>
</div>
<div class="cosmos-card" id="mainTransaction2" style="display: none;">
  <br />
  <div class="cosmos-payinfo">
    <div class="cosmos-card-title">
      <img  style="vertical-align:middle"  id="chainIcon2" src="" width="25" height="25"> 
        <div class="cosmos-h3" id="finalAmount2"></div>
    </div>
    <div class="cosmos-card-amount">
      <?php echo get_woocommerce_currency_symbol() ?> <?php echo esc_attr( $order->get_total() ) ?>
      <?php echo get_woocommerce_currency() ?> 
    </div>
  </div>
  <div class="cosmos-content">
    <div  align="center"> 
      <div class="loader" id="spinner"></div>
      <div id="cancelTx" align="center" style="display: none;">
        <img src="<?php echo plugins_url(); ?>/<?php echo esc_attr($this->plugin_name); ?>/public/img/cancel.png" width="75" height="75">
        <br /><br />
        
        <div class="cosmos-h5">Keplr canceled</div>
        <div id="keplrError" style="color: red;"></div><br />
        <button class="buttonRetry" id="retry">Retry</button> 
        <div class="cancelTx" align="center"> 
          <a href="" id="cancel2" style="color: red;"><u>Cancel</u></a>
        </div>
      </div>
      <div id="AcceptedTx" align="center" style="display: none;">
        <img src="<?php echo plugins_url(); ?>/<?php echo esc_attr($this->plugin_name); ?>/public/img/accepted.png" width="75" height="75">
        <br /><br />
        <div class="cosmos-h5">Payment accepted</div>
        <a href="" id="finalUrlTx" target="_blank">View transaction</a>
      </div>
    </div>
  </div>
</div>
<div class="cosmos-card" id="mainTransaction3" style="display: none;">
  <br />
  <div class="cosmos-payinfo">
    <div class="cosmos-card-title">
      <img id="chainIcon3" src="" width="25" height="25"> 
        <div class="cosmos-h3" id="finalAmount3"></div>
    </div>
    <div class="cosmos-card-amount">
      <?php echo get_woocommerce_currency_symbol() ?>  <?php echo $order->get_total() ?>
      <?php echo get_woocommerce_currency() ?> 
    </div>
  </div>
  <div class="d-flex justify-content-center">
    <div class="cosmos-content" id="manualFinal">
      <div id="phase1">
         Please send the <span class="cosmos-bold">exact</span> same amount of coins to the following address 
        <div class="input-wrapper" id="copyRecep">
          <input type="text" id="recipient" name="recipient" value=""  aria-label="readonly input example" readonly>
        </div>
        <span style="display: none; color: green;" style="copyAddress" id="copyAddress">Address copied</span><br />
         
        Add the following ID to the <span class="cosmos-bold">MEMO</span> field in your transaction
        <div class="input-wrapper" id="copyMemo">
          <input value="" type="text" id="memo" name="memo" aria-label="readonly input example" readonly> 
        </div>
        <span style="display: none; color: green;" id="copyMemoMessage">Memo copied</span>
        <br />
        <p>Make sure to send your transaction to the <span class="cosmos-bold">correct address</span> with the <span class="cosmos-bold">precise amount</span> and the <span class="cosmos-bold">correct MEMO</span>. If you need help, contact customer support.</p>
        <div class="cancelTx" align="center"> 
          <a href="" id="cancel2" style="color: red;"><u>Cancel</u></a>
        </div>        
      </div>
      <div id="phase2" align="center" style="display: none;">
        <br />
        <div class="loader" id="spinnerManual"></div>
        <hr>
        <h5 align="center">Checking</h5>
      </div>
      <div id="phase3" style="display: none;">
        <div id="AcceptedTx" align="center">
          <img src="<?php echo plugins_url(); ?>/<?php echo esc_attr($this->plugin_name); ?>/public/img/accepted.png" width="75" height="75">
          <br /> 
          <div class="cosmos-h5">Payment accepted</div>
          <a href="" id="finalUrlTx" target="_blank">View transaction</a>
        </div>
      </div>
      <div id="errorManual" style="display: none;">
        <div align="center">
          <div class="cosmos-h5">Error</div>
          <div id="errorMessage"></div>
        </div>
      </div>
    </div>
  </div>
</div>
 
<div class="timerCard">
Time left: <span class="cosmos-bold"><span id="minutes"></span>:<span id="seconds"></span></span>
</div>
<script>
  //     if (!window.getOfflineSigner || !window.keplr) {
  //       alert("Please install keplr extension");
  //     }
  
  function myTimer(sendTo, amount, memo) {
    var order_id = "<?php echo esc_attr( $order_id ) ?>";
    var mainDomain = "<?php echo get_home_url( ) ?>";
    
    var myVar = setInterval( () => {
      $.get( mainDomain+"/api-cosmos/?check=manual&order_id=" + order_id, function( data, status ) {     
        $.each( data.tx_responses, function( i, item ) {
          if( item.tx.body.memo === memo ) {        
            clearInterval( myVar )      
            $( "#phase1" ).hide( )
            $( "#phase2" ).show( )
            
            console.log( 'Transaction found!\n Memo: ' +item.tx.body.memo+ '\n Txhash: '+item.txhash )
  
            setTimeout( function() {
              $.get( mainDomain+"/api-cosmos/?tx_hash="+item.txhash+"&order_id=" + order_id, function( final_data, final_status ) {
                if ( final_data.error === true)  {
                  $( "#phase2" ).hide( )
                  $( "#errorManual" ).show( )
                  $( "#errorMessage" ).html( final_data.message )  
                  return
                } else {
                  $( "#phase2" ).hide( )
                  $( "#phase3" ).show( )
                  $( ".woocommerce-thankyou-order-received" ).css( "border-color", "#20c005" )
                  $( ".woocommerce-thankyou-order-received" ).css( "color", "#20c005" )
                  $( ".woocommerce-thankyou-order-received" ).html( "Payment accepted!" )               
                }
              })   
            }, 4000 )       
          }
        })
      })   
    }, 10000 )    
  }

  jQuery( function( $ ){  
    var order_id = "<?php echo esc_attr( $order_id ) ?>";
    var mainDomain = "<?php echo esc_attr( get_home_url() ) ?>";
    var finalApiUrl = "<?php echo esc_attr( $finalApiUrl ) ?>";
    var memo = "<?php echo esc_attr( $dbMemo ) ?>";
    var isBlocked = "<?php echo esc_attr( $isBlocked ) ?>";
    var isLogged = "<?php echo $isLogged ?>";
    var nonceSelectChain = "<?php echo esc_attr( wp_create_nonce( 'cosmos_select_chain' ) ) ?>"
    var nonceDeleteOrder = "<?php echo esc_attr( wp_create_nonce( 'cosmos_delete_order' ) ) ?>"
    var nonceSwitchMethod = "<?php echo esc_attr( wp_create_nonce( 'cosmos_switch_method' ) ) ?>"
    var setDefault = "<?php echo esc_attr( $setDefault ) ?>"
    var isMobile = "<?php echo $isMobile ?>"
 
    startChecking( 
      order_id, 
      mainDomain, 
      finalApiUrl,
      memo, 
      isBlocked, 
      isLogged,
      nonceSelectChain, 
      nonceDeleteOrder, 
      nonceSwitchMethod,
      setDefault,
      isMobile
    );
  })
  function copyRecipient() {
    var copyText = document.querySelector( "#recipient" )
    copyText.select()
    document.execCommand( "copy" )
    $( "#copyAddress" ).show( )   
  }
  function copyMemo() {
    var copyText = document.querySelector( "#memo" )
    copyText.select()
    document.execCommand( "copy" )
    $( "#copyMemoMessage" ).show( )
  }
  document.querySelector( "#copyRecep" ).addEventListener( "click", copyRecipient )
  document.querySelector( "#copyMemo" ).addEventListener( "click", copyMemo )
</script>

<style>
.woocommerce-order-received .woocommerce-order {
  max-width: 1000px;
  margin: 0 auto;
}
</style>
