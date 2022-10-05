<?php 

session_start();
$cosmosTokenTime = time();
$cosmosPluginDir = plugin_dir_url( __FILE__ );

function generateRandomString($length = 10) {
  $characters = '0123456789';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
  return $randomString;
}
 
$configCosmosAddr = get_option('woocommerce_woo-cosmos_settings');
$configDisclaimer = get_option("cosmos_pay_disclaimer_approved");

$errorLogin = false;
if ( isset( $_POST['adminChecker'] ) ) {

  $user = get_user_by( 'login', wp_get_current_user()->user_login );
  if ( $user && wp_check_password( esc_attr( $_POST['adminChecker'] ), $user->data->user_pass, $user->ID ) ) {
    header( 'Location: ' . get_admin_url() . 'admin.php?page=' . $this->plugin_name . '-settings&cosmosToken=' . get_option('cosmosToken') );
    $cosmosTokenTime = time();
    $_SESSION["addressTimer"] = $cosmosTokenTime;    
  } else {
    $errorLogin = true;
  }
}

if ( isset( $_GET['cosmosToken'] ) ) {
  $cosmosToken = get_option( 'cosmosToken' );
  if ( $cosmosToken != $_GET['cosmosToken'] ) {    
    header('Location: ' . get_admin_url() . 'admin.php?page=' . $this->plugin_name . '-settings');
  } else if ( empty($_SESSION["addressTimer"] ) ) {
    header( 'Location: ' . get_admin_url() . 'admin.php?page=' . $this->plugin_name . '-settings' );
  } else if ( $cosmosTokenTime > ($_SESSION["addressTimer"] + 60000) ) {
    header('Location: ' . get_admin_url() . 'admin.php?page=' . $this->plugin_name . '-settings');
  }
}

$errorDisclaimer = false;
if (isset($_POST['checkDisclamer'])) {
  if(isset($_POST['disclamer1'])) {
    update_option( 'cosmos_pay_disclaimer_approved', 'true' );
    header('Location: ' . get_admin_url() . 'admin.php?page=' . $this->plugin_name . '-settings');
  } else {
    $errorDisclaimer = true;
  }
} 

if (!get_option("cosmosToken")) {
  add_option("cosmosToken", generateRandomString(32));
}

// Call configuration file from our server
// $string = file_get_contents( "https://store-api.bitcanna.io" );
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
<?php } elseif (!isset($_GET['cosmosToken'])) { ?>

        <div class="card-body">
        <?php if ($errorLogin === true) { ?>
          <font color="red">Login error!</font>
        <?php } ?>
          <div class="wrap">
 
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=<?php echo esc_attr($this->plugin_name); ?>-settings">
 
              <table class="form-table">
                <tr valign="top">
                <th scope="row">Admin password</th>
                <td><input type="password" name="adminChecker" value="" size="50" /></td>
                </tr> 
                
              </table>
              
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
          } 
          ?>   
          <form method="post"> 
              <table class="form-table">
                <?php 

                  foreach ($json_a as $chains_data => $chain) {
                    if ($chain['active'] === 'true') {
                    ?>
                      <tr valign="top">
                      <th scope="row">Your address <?php echo esc_attr( $chain['name'] ); ?></th>
                      <td><input type="text" id="<?php echo esc_attr( $chain['name'] ); ?>" name="<?php echo esc_attr( $chain['name'] ); ?>" value="<?php echo esc_attr( $configCosmosAddr[$chain['name']] ); ?>" size="50" /></td>
                      <td>
                      <button id="target" value="<?php echo esc_attr( $chain['name'] ); ?>" name="get_chain" class="button button-primary" type="button">
                        Connect <?php echo esc_attr( $chain['name'] ); ?>
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
              
              <?php submit_button(); ?>

          </form>
          </div>
        </div>
 <?php } ?>
 
</div>	
<script>

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
        console.log(accounts[0].address)
        $( '#' + foundChain.name ).val(accounts[0].address)
      });     
    } 
  });
});

</script>
<style>
.card-cosmos {
  width: 800px; /*1*/
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
