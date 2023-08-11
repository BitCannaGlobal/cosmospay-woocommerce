<?php 

session_start();
 
$cosmosPluginDir = plugin_dir_url( __FILE__ );
 
 
$configCosmosAddr = get_option('woocommerce_woo-cosmos_settings');
$configDisclaimer = get_option("cosmos_pay_disclaimer_approved");
 
$errorDisclaimer = false;
if (isset($_POST['checkDisclamer'])) {
  if(isset($_POST['disclamer1'])) {
    update_option( 'cosmos_pay_disclaimer_approved', 'true' );
    header('Location: ' . get_admin_url() . 'admin.php?page=' . $this->plugin_name . '-settings');
  } else {
    $errorDisclaimer = true;
  }
} 

// Call configuration file from our server
$string = wp_remote_retrieve_body( wp_remote_get( 'https://store-api.bitcanna.io' ) );
if (empty($string)) {
  wp_die('Unable to call configuration', 'Error');
}
// Decode configuration file from our server
$json_a = json_decode($string, true);
if ($json_a === null) {
  wp_die('Error in json configuration', 'Error');
} 
  
?>   
   
	<div class="wrap" align="center">
	
    <img src="<?php echo plugins_url(); ?>/<?php echo esc_attr($this->plugin_name); ?>/public/img/cosmos.png" width="160" height="120">
		<h1>Cosmos Pay settings</h1>
	</div>
	<br />
<div class="card-cosmos">
<?php if ($configDisclaimer === 'false') { ?>
        <div class="card-body"> 
          <div class="wrap">
            <div align="center"><h2>Cosmos Pay Disclaimer</h2></div>
            <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=<?php echo esc_attr($this->plugin_name); ?>-settings">
            <textarea id="story" name="story" rows="10" cols="86" disabled><?php 
              // $file = file_get_contents('disclaimer.txt', true);
              $disclaimer = wp_remote_retrieve_body( wp_remote_get( 'https://store-api.bitcanna.io/disclaimer' ) );
              echo esc_attr($disclaimer);
            ?>
            </textarea> 
            <br /><br />
            <fieldset>
            
                <?php if ($errorDisclaimer === true) { ?>
                  <font color="red">You must check the box to accept the disclaimer.</font>
                  <br /><br />
                <?php } ?>            
                <div>
                  <input type="checkbox" id="disclamer1" name="disclamer1">
                  <label for="disclamer1">I accept this disclaimer.</label>
                </div>
            </fieldset>
            <input id="checkDisclamer" name="checkDisclamer" type="hidden" value="true">
            <?php submit_button("Confirm"); ?>
 
            </form>
          </div>
        </div>
 
        <?php } else { ?>

        <div class="card-body">
          <div class="wrap">
           <?php 
           if ( isset( $_POST['update_address'] ) ) {
             
             foreach ( $json_a as $chains_data => $chain ) {
               if ( $chain['active'] === 'true' ) {
                 $configCosmosAddr[$chain['name']] = sanitize_text_field( $_POST[$chain['name']] );
               }
             }  
             update_option( 'woocommerce_woo-cosmos_settings', $configCosmosAddr );
             update_option( 'cosmos_pay_config_approved', 'true' );
           } 
           
            if(!empty($selected_payment_method->settings['option_name'])) {
              
            
           ?>   
          <form method="post"> 
              <table class="form-table">
              
          <?php     
 

            foreach ( $selected_payment_method->form_fields['option_name']['options'] as $key => $value ) {  
               $keyAvaible = array_search( $key, $selected_payment_method->settings['option_name'] );  
              
              
              if ( $keyAvaible === 0 || !empty( $keyAvaible ) ) { 
          ?>
                      <tr valign="top">
                      <th scope="row">Your <?php echo esc_attr( $value ); ?> address </th>
                      <?php 
                        if(!isset($configCosmosAddr[$value])) {
                          ?>
                            <td><input required="required" type="text" id="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $value ); ?>" value="" size="50" />
                          <?php
                        } else {
                          ?>
                            <td><input required="required" type="text" id="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $value ); ?>" value="<?php echo esc_attr( $configCosmosAddr[$value] ); ?>" size="50" />
                          <?php                          
                        }
                      
                      ?>
                      
                      <div id="goodAddr_<?php echo esc_attr( $value ); ?>" style="display: none; color:green;">This is a valid address.</div>
                      <div id="badAddr_<?php echo esc_attr( $value ); ?>" style="display: none; color:red;">This is an invalid address. Please double-check.</div>
                      <div id="badAddrPrefix_<?php echo esc_attr( $value ); ?>" style="display: none; color:red;">This address does not belong to this chain. Please update to the right address.</div>
                      </td>
                      <td>
                      <button id="target" value="<?php echo esc_attr( $value ); ?>" name="get_chain" class="button button-primary" type="button">
                        Connect <?php echo esc_attr( $value ); ?>
                      </button>   
                      </td>
                      </tr>           
 
          <?php 
              }                
            }
          ?>               
 
  
                 <input type="hidden" name="update_address" value="true">
                 <div id="resultAddr"></div>
              </table>
        <button type="submit" class="button button-primary" id="sendConfig" name="mymod_pc_form" >
          <i class="process-icon-save"></i> Save Changes
        </button>               
 

          </form>
          <?php } else { ?>
            <h4>No blockchain is configured, select a blockchain in the woocomerce payment configuration</h4>
          <?php } ?>
          
          </div>
        </div>
    <?php } ?>  
</div>



<script type="importmap">
  //"bech32": "https://unpkg.com/bech32@2.0.0/dist/index.js" 
  {
    "imports": {
      "bech32": "<?php echo plugins_url(); ?>/<?php echo esc_attr($this->plugin_name); ?>/public/js/bech32.js"
    }
  }
</script>
<script type="module">
import bech32 from "bech32";
  
jQuery(function($){  
 
  $.getJSON( "https://store-api.bitcanna.io", async function( result ) {
    result.forEach((element) => {
      console.log(element) 
      // $("#"+element.name).change(function() {
      $("#"+element.name).on('input', function() {   

        try {
          let bech32Decode = bech32.decode($(this).val())
          console.log(bech32Decode) 
          if (bech32Decode.prefix === element.coinLookup.addressPrefix) {
            $("#goodAddr_"+element.name).show();
            $("#badAddr_"+element.name).hide();    
            $("#badAddrPrefix_"+element.name).hide();
            $('#sendConfig').prop('disabled', false);
          } else {
            $("#goodAddr_"+element.name).hide();
            $("#badAddr_"+element.name).hide(); 
            $("#badAddrPrefix_"+element.name).show();
            $('#sendConfig').prop('disabled', true);
          }

        } catch (error) {
          console.error(error);
          $("#goodAddr_"+element.name).hide();
          $("#badAddrPrefix_"+element.name).hide();
          $("#badAddr_"+element.name).show();
          $('#sendConfig').prop('disabled', true);
        }      
      });        
    });         
  });     
});  
  
jQuery(function($){  
  $( "button[name='get_chain']" ).click( async function() {
    var chainCall = $(this).val()
    if (!window.keplr) {
      alert( "Please install keplr extension" );
    } else {
      $.getJSON( "https://store-api.bitcanna.io", async function( result ) {
        let foundChain = result.find(element => element.name === chainCall);        
        
        const chainId = foundChain.chainId
        await window.keplr.enable(chainId)  
        const offlineSigner = window.keplr.getOfflineSigner(chainId)
        const accounts = await offlineSigner.getAccounts()         
        $( '#' + foundChain.name ).val(accounts[0].address)
            $("#goodAddr_"+foundChain.name).show();
            $("#badAddr_"+foundChain.name).hide();    
            $("#badAddrPrefix_"+foundChain.name).hide();   
            $('#sendConfig').prop('disabled', false);
      });     
    } 
  });
});  

</script>




<script>

</script>
<style>
.card-cosmos {
  width: 900px; /*1*/
  margin: 0px auto; /*2*/
  background-color: white; /*3*/
  box-shadow: 0px 5px 20px #999; /*4*/
}
.card-cosmos a { /*5*/
  color: #333; 
  text-decoration: none;
}
.card-cosmos:hover .card-image img { /*6*/
  width: 160%;
  filter: grayscale(0);
} 
.card-image {
  height: 250px;/*1*/
  position: relative;/*2*/
  overflow: hidden;/*3*/
}
.card-image img {
  width: 150%;/*4*/
    /*5 - Méthode de centrage en fonction de la taille de l'image */
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  filter: grayscale(1);/*6*/
    /*7 - Transition */
  transition-property: filter width; 
  transition-duration: .3s;
  transition-timing-function: ease;
}
.card-body {
 
  padding: 15px 20px; /*2*/
  box-sizing: border-box; /*3*/
}
.card-date {
  font-family: 'Source Sans Pro', sans-serif;
}

.card-title, .card-excerpt {
   font-family: 'Playfair Display', serif;
}

.card-date, .card-title {
  text-align:center;
  text-transform:uppercase;
  font-weight: bold;
}

.card-date, .card-excerpt {
  color: #777;
}
textarea {
  resize: none;
}
</style>
