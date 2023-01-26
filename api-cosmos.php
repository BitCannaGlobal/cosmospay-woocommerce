<?php
header('Content-Type: application/json');

$wc_cosmos_options = get_option( 'woocommerce_woo-cosmos_settings'); 

/*if ( isset( $_GET['order_id'] ) OR isset( $_POST['order_id'] ) ) {
  $order = wc_get_order( $_REQUEST['order_id'] );
  $userWp = wp_get_current_user( );
  if ( $order->user_id !== $userWp->ID ) {
    wp_die( 'Mhhhh!', 'Error' );
  } 
}  */
 
$cosmos_parse_referer = parse_url(sanitize_text_field($_SERVER['HTTP_REFERER']));
$comsos_referer = $cosmos_parse_referer['scheme'] . '://' . $cosmos_parse_referer['host'];
 
if (get_site_url() !== $comsos_referer) {
 wp_die( 'Mhhhh!', 'Error' );
}
 
if (isset($_GET['tx_hash']) && isset($_GET['order_id'])) {
  $orderId = sanitize_text_field( $_GET['order_id'] );
  $orderTx = sanitize_text_field( $_GET['tx_hash'] );
  $order = wc_get_order( $orderId );

  if (!is_numeric($orderId)) 
    wp_die('Bad order_id', 'Error');
  if (!$order) 
    wp_die('Order does not exist', 'Error');
    
  // Validate payement
  $dbMemo = wc_get_order_item_meta( $orderId , '_cosmos_memo', true );
  $dbPrice = wc_get_order_item_meta( $orderId, '_cosmos_order_price', true );
  $dbLcd = wc_get_order_item_meta( $orderId, '_cosmos_lcd_pay', true );
  $getChainOrder = wc_get_order_item_meta( $orderId, '_cosmos_chain_pay', true );
  $dbAdresse = $wc_cosmos_options[ $getChainOrder ]; // Config plugin

  $getTx = file_get_contents( $dbLcd.'/cosmos/tx/v1beta1/txs/' . $orderTx );
  $getTx = json_decode( $getTx );
  
  $ubcnaAmount = $dbPrice * 1000000;
  //var_dump($wc_cosmos_options);
  if ($dbMemo === $getTx->tx->body->memo) {
    if ($ubcnaAmount === (float) $getTx->tx->body->messages[0]->amount[0]->amount) {
      if ($dbAdresse === $getTx->tx->body->messages[0]->to_address) {
        $order = new WC_Order($orderId);
        if (!empty($order)) {
          $note = 'Tx of order: '.esc_attr( $orderTx );
          $order->update_status( 'processing' ); // completed
          $order->add_order_note( $note , true );
          // TODO add link to explorer
          // wc_update_order_item_meta($orderId, '_cosmos_tx', esc_attr());
        }
        echo '{ "error":false, "message":"Payement accepted!" }';
      } else
        echo '{ "error":true, "message":"Bad adresse" }';
    } else
      echo '{ "error":true, "message":"Bad amount" }';
  } else
    echo '{ "error":true, "message":"Bad memo" }';  
      
  
} elseif ( isset($_POST['switch']) && isset($_POST['order_id']) && isset($_POST['nonce']) ) {
  // var_dump(wp_verify_nonce($_POST['nonce']));
  $nonce = sanitize_text_field( $_POST['nonce'] );
  if ( ! wp_verify_nonce($nonce, 'cosmos_select_chain')) 
    wp_die('Error nonce', 'Error');

  $orderId = sanitize_text_field( $_POST['order_id'] );
  $switchId = sanitize_text_field( $_POST['switch'] );
  $order = wc_get_order( $orderId );
  $orderData = $order->get_data(); // order data
  
  if ( !is_numeric( $orderId ) ) 
    wp_die( 'Bad order_id', 'Error' );
  if (!$order) 
    wp_die( 'Order does not exist', 'Error' );
  // Update name of chain selected
  wc_update_order_item_meta( $orderId, '_cosmos_chain_pay', esc_attr( $switchId ) );
    
  // Call configuration file from our server
  // $string = file_get_contents( "https://store-api.bitcanna.io" );
  $string = wp_remote_retrieve_body( wp_remote_get( 'https://store-api.bitcanna.io' ) );

  if (empty($string)) {
    wp_die( 'Unable to call configuration', 'Error' );
  }
  // Decode configuration file from our server
  $json_a = json_decode( $string, true );
  if ($json_a === null) {
    wp_die('Error in json configuration', 'Error');
  }
  // Get name of chain selected
  $chainChecking = wc_get_order_item_meta($orderId,'_cosmos_chain_pay', true );
  
  // Foreach configuration file and compare selected chain
  // And select it for get good price!! 
  foreach ($json_a as $chains_data => $chain) {
    if ($chain['name'] === $chainChecking) {        
      wc_update_order_item_meta($orderId, '_cosmos_lcd_pay', esc_attr($chain['apiURL']));  
      // Here is define the good chain selected
      $dataChain = $chain;
    }        
  }    
  // Check the currency from woocommerce and convert it to price chain
  $currencyList = "usd,aed,ars,aud,bdt,bhd,bmd,brl,cad,chf,clp,cny,czk,dkk,eur,gbp,hkd,huf,idr,ils,inr,jpy,krw,kwd,lkr,mmk,mxn,myr,ngn,nok,nzd,php,pkr,pln,rub,sar,sek,sgd,thb,try,twd,uah,vef,vnd,zar,xdr";
  
  if ($dataChain['coingeckoId'] === 'bitcanna') {
    $dataValueCoin = wp_remote_retrieve_body( wp_remote_get( 'https://bcnaracle.bitcanna.io/bcnaracle.json' ) );
  } else {
    $dataValueCoin = wp_remote_retrieve_body( wp_remote_get( 'https://api.coingecko.com/api/v3/simple/price?ids='.$dataChain['coingeckoId'].'&vs_currencies='.$currencyList ) );  
  }
 
  $decodedData = json_decode($dataValueCoin);
  $currencyNow = strtolower(get_woocommerce_currency());  
  // And display good price!
  // datachain->coingeckoId->currency
  $coinPriceGeeko = $decodedData->{$dataChain['coingeckoId']}->$currencyNow;
  
  // Chain chain id in db
  // Save data of order
  $finalBcnaValue = $orderData["total"] / $coinPriceGeeko;
  $finalBcnaValueTronc = number_format($finalBcnaValue, 3, '.', '');  
  wc_update_order_item_meta($orderId, '_cosmos_price', esc_attr($coinPriceGeeko));
  wc_update_order_item_meta($orderId, '_cosmos_order_price', esc_attr($finalBcnaValueTronc));  
  if (!wc_get_order_item_meta($orderId, '_cosmos_order_start_time'))
    wc_update_order_item_meta($orderId, '_cosmos_order_start_time', esc_attr(time()));
  
  $getChainPay = wc_get_order_item_meta( $orderId , '_cosmos_chain_pay', true ); 
  $getLcdPay = wc_get_order_item_meta( $orderId , '_cosmos_lcd_pay', true ); 
  $getCosmosPrice = wc_get_order_item_meta( $orderId , '_cosmos_price', true ); 
  $getOrderPrice = wc_get_order_item_meta( $orderId , '_cosmos_order_price', true );
  $getstartTime = wc_get_order_item_meta( $orderId , '_cosmos_order_start_time', true );
  
  echo '{ 
    "current_chain": "'.esc_attr( $getChainPay ).'", 
    "lcd": "'.esc_attr( $getLcdPay ).'", 
    "CosmosPrice": "'.esc_attr( $getCosmosPrice ).'", 
    "OrderPrice": "'.esc_attr( $getOrderPrice ).'",
    "chainDenom": "'.esc_attr( $dataChain['coinLookup']['viewDenom'] ).'",
    "adressToPay": "'.esc_attr( $wc_cosmos_options[$getChainPay] ).'",
    "fee": "'.esc_attr( $dataChain['fee']['amount'] ).'",
    "gas": "'.esc_attr( $dataChain['fee']['gas'] ).'",
    "startTime": "'.esc_attr( $getstartTime ).'"
  }';
  //$note = 'Change selected chain: '.$getChainPay;
  //$order->add_order_note( $note , true );  
} elseif ( isset( $_POST['switchMethod'] ) && isset( $_POST['order_id'] ) && isset( $_POST['nonce'] ) ) {
 
  $nonce = sanitize_text_field( $_POST['nonce'] );
  if ( ! wp_verify_nonce($nonce, 'cosmos_switch_method')) 
    wp_die('Error nonce', 'Error');

  $orderId = sanitize_text_field( $_POST['order_id'] );
  wc_update_order_item_meta( $orderId, '_cosmos_method', sanitize_text_field( $_POST['switchMethod'] ) );
  echo 'Update';
  
} elseif (isset($_POST['finalData']) && isset($_POST['order_id'])) {
  $orderId = sanitize_text_field( $_POST['order_id'] );
  
  wc_update_order_item_meta($orderId, '_cosmos_blocked', esc_attr('true'));
  $getChainPay = wc_get_order_item_meta( $orderId , '_cosmos_chain_pay', true ); 
  $getLcdPay = wc_get_order_item_meta( $orderId , '_cosmos_lcd_pay', true ); 
  $getCosmosPrice = wc_get_order_item_meta( $orderId , '_cosmos_price', true ); 
  $getOrderPrice = wc_get_order_item_meta( $orderId , '_cosmos_order_price', true );
  $getMethod = wc_get_order_item_meta( $orderId , '_cosmos_method', true );
  $getBlocked = wc_get_order_item_meta( $orderId , '_cosmos_blocked', true );
  $getstartTime = wc_get_order_item_meta( $orderId , '_cosmos_order_start_time', true );
  $dbMemo = wc_get_order_item_meta( $orderId , '_cosmos_memo', true );
  
  echo '{ 
    "current_chain": "'.esc_attr( $getChainPay ).'", 
    "lcd": "'.esc_attr( $getLcdPay ).'", 
    "CosmosPrice": "'.esc_attr( $getCosmosPrice ).'", 
    "OrderPrice": "'.esc_attr( $getOrderPrice ).'",
    "adressToPay": "'.esc_attr( $wc_cosmos_options[ $getChainPay ] ).'",
    "memo": "'.esc_attr( $dbMemo ).'",
    "method": "'.esc_attr( $getMethod ).'",
    "blocked": "'.esc_attr( $getBlocked ).'",
    "startTime": "'.esc_attr( $getstartTime ).'"
  }';
  
} elseif ( isset( $_POST['order_id'] ) && isset( $_POST['cancel'] ) && isset( $_POST['nonce'] ) ) {

  $nonce = sanitize_text_field( $_POST['nonce'] );
  if ( ! wp_verify_nonce($nonce, 'cosmos_delete_order')) 
    wp_die('Error nonce', 'Error');

  $orderId = sanitize_text_field( $_POST['order_id'] );
  $order = wc_get_order( $orderId );
  $orderData = $order->get_data(); // order data
  $userWp = wp_get_current_user();
  
  if ($order->user_id !== $userWp->ID) {
    //wp_die( 'Bad userId', 'Error' );
    echo '{ error: "Bad userId"}';
    exit;
  }  
 
  $order->update_status('cancelled');
    
  echo '{ "return": "canceled" }';
  
} elseif ( isset($_GET['check']) && isset( $_GET['order_id'] ) ) {

  $orderId = sanitize_text_field( $_GET['order_id'] );
  $getLcdPay = wc_get_order_item_meta( $orderId , '_cosmos_lcd_pay', true ); 
  $json = file_get_contents($getLcdPay.'/cosmos/tx/v1beta1/txs?events=message.action=%27/cosmos.bank.v1beta1.MsgSend%27&order_by=ORDER_BY_DESC&pagination.limit=10');
  $obj = json_decode($json);
    
  echo $json;  
} else
  echo 'Bad parameters';
  
