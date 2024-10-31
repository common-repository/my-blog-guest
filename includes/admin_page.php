<?php
function myblogguest_admin_page() {

    
myblogguest_login();
myblogguest_site_login();    

?>

<style>
	.step { width: 600px; border: 1px solid #000; background-color: #eee; padding: 10px; margin-bottom: 20px;}
	.formlabel { float:left; width: 100px; font-weight:bold;display:inline; }
	.forminput { float:left; display:inline; }
	.forminput input { padding: 10px; font-size:14px; } 
	.forminput input[type=button] { padding: 9px!important; font-size:14px; } 
	
</style>

<div class="wrap">
<h2><?php echo WPMBG_DISPLAY_NAME ?> Setup</h2>

<?php 
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox', null, array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
?>

<?php wp_nonce_field('update-options'); ?>
<?php settings_fields( 'wpmbg_settings' ); ?>
<?php $options = get_option('wpmbg_options'); ?>
<div id="poststuff" >
<div style="width:75%;" class="postbox-container" >

<?php
    if(!empty($options))
    {
?>
<!-- Step 3 -->
<div id="step3" class="step">
<h2>Renew Access Token.</h2>
<p>Login to your <?php echo WPMBG_DISPLAY_NAME ?> Account</p>
<div class="formlabel"><h3>Login</h3></div>
<div class="forminput">
<?php 
$wpmbg_app_id =  $options['wpmbg_app_id'];

//$wpmbg_app_id = "%APP_ID%"; 

$redirect_uri = get_admin_url().'admin.php?page=wpmbg_settings';
$custom_auth_url = WPMBG_BASE_URL . '/authorize?client_id=' . $wpmbg_app_id . '&response_type=code&state=articles&redirect_uri='.urlencode($redirect_uri); ?>

<input type="hidden" value="<?php echo $custom_auth_url ?>" id="wpmbg_orig_url">
<a id="wpmbg_url" href="<?php echo $custom_auth_url ?>"><input type="button" value="Click Here To Login To <?php echo WPMBG_DISPLAY_NAME ?>"></a> <span class="wpmbg_login_valid"></span></div>
<!-- //Step 3 -->

<div class="clear"></div>
</div> 

<?php
    }
?>


<div class="step">
<h2>Init MyBlogGuest Connection.</h2>
<p>Login to your <?php echo WPMBG_DISPLAY_NAME ?> Account</p>
<div class="formlabel"><h3>Login</h3></div>
<div class="forminput">
<?php 
$wpmbg_app_id =  $options['wpmbg_app_id'];

$redirect_uri = get_admin_url().'admin.php?page=wpmbg_settings&scope=site';
$custom_auth_url = WPMBG_BASE_URL . '/authorize_site?redirect_uri='.urlencode($redirect_uri); 

?>

<input type="hidden" value="<?php echo $custom_auth_url ?>" id="wpmbg_orig_url">
<a id="wpmbg_url" href="<?php echo $custom_auth_url ?>"><input type="button" value="Click Here To Login To <?php echo WPMBG_DISPLAY_NAME ?>"></a> <span class="wpmbg_login_valid"></span></div>

<div class="clear"></div>
</div> 



<?php // load up the forth step -> 
//myblogguest_settings_page();
//echo "</div></div>";
mbg_add_custom_box();
?>
</div></div>
<?php
}

function myAjax(){

    //get data from our ajax() call

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

    $wpmbg_app_id = sanitize_text_field($_POST['wpmbg_app_id']);
	$options = get_option('wpmbg_options');
	$options['wpmbg_app_id'] = $wpmbg_app_id;

$updated =  update_option("wpmbg_options", $options );
	if (get_option('wpmbg_options') != "") {
	  $updated =  update_option("wpmbg_options", $options );
	
	} else {
		$deprecated = ' ';
		$autoload = 'no';
		$updated = add_option( "wpmbg_options", $options, $deprecated, $autoload );
			

	}


	
    $results = "<span style='color: Green;'> Your APP ID Has Been Saved</span>";

    // Return String
    die($results);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_myAjax', 'myAjax' );
add_action( 'wp_ajax_myAjax', 'myAjax' );


function myBlogGuestAuth(){

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	$error = "";
	$wpmbg_redirect_uri = WPMBG_REDIRECT_URI;
	$options = get_option('wpmbg_options');
	$wpmbg_app_secret = sanitize_text_field($_POST['wpmbg_app_secret']);
	$options['wpmbg_app_secret'] =$wpmbg_app_secret;

	if (get_option('wpmbg_options') != "") {
	  $updated =  update_option("wpmbg_options", $options );
	} else {
		$deprecated = ' ';
		$autoload = 'no';
		add_option( "wpmbg_options", $options, $deprecated, $autoload );
	}

		
	$auth_url = WPMBG_BASE_URL . "/token";
	$request = new WP_Http;
	
	$wpmbg_app_id = $options['wpmbg_app_id'];
	$wpmbg_auth_code = $options['wpmbg_auth_code'];
		
	$body = array("grant_type" => 'authorization_code',"client_id"=>$wpmbg_app_id,"code" =>$wpmbg_auth_code ,"client_secret"=> $wpmbg_app_secret,"redirect_uri"=>$wpmbg_redirect_uri);

	$result = $request->request($auth_url,array('method' => 'POST','body' =>$body));
	$html_content = array();
	
	if (isset($result->errors)) {
		// display error message of some sort
		$error =  "There was a  problem contacting the server!";
	} else {
		$html_content = $result['body'];
		$messages = json_decode($html_content);
		if (isset($messages->error)){
			$error = $messages->error;	
		} else {
			/* Contacting server was sucessful so save oauth_token */
			
			$options['wpmbg_oauth_token'] = $messages->access_token;
			$options['wpmbg_oauth_expires_in'] = $messages->expires_in;
			$options['wpmbg_oauth_created'] = time();

						
			if (get_option('wpmbg_options') != "") {
			  $updated =  update_option("wpmbg_options", $options );
			} else {
				$deprecated = ' ';
				$autoload = 'no';
				add_option( "wpmbg_options", $options, $deprecated, $autoload );
			}
		

				
		}
	}
	
	


	// clean up dirty JSON

    //get data from our ajax() call
	
  if ($error) {
	  $results = "<span style='color: red;'> Error: $error </span>";  	  
  } else {
	  $results = "<span style='color: Green;'> Authorized!</span>";  
  }

    // Return String
    die($results);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_myBlogGuestAuth', 'myBlogGuestAuth' );
add_action( 'wp_ajax_myBlogGuestAuth', 'myBlogGuestAuth' );



        /*
         *  Окончание авторизации сайта
         */
function myblogguest_site_login()
{
    
$auth_code = isset($_REQUEST['code']) ? sanitize_text_field($_REQUEST['code']) : '';
$scope     = isset($_REQUEST['scope']) ? sanitize_text_field($_REQUEST['scope']) : '';

        if(empty($auth_code) || $scope != 'site')
        {
        return false;
        }

	try
	{	
                        
        $redirect_uri = get_admin_url().'admin.php?page=wpmbg_settings';
        $settings_uri = WPMBG_BASE_URL.'/wp_plugin_settings?code='.$auth_code.'&redirect_uri='.urlencode($redirect_uri);
        $http_code =  0;

        $res = process_http($settings_uri, $http_code);

            if($http_code == 200 && !empty($res))
            {
            $options = json_decode($res, true);
        
                if(is_array($options))
                {
                    if(empty($options['errors']))
                    {
                    add_option('wpmbg_options', $options, '', 'yes');
                    update_option('wpmbg_options', $options);
                    delete_option('wpmbg_gtm_cache');
            
                    showMessage('The plugin was successfully initialized!');
                    }
                    else
                    {
                    throw new Exception($options['errors']['text']);
                    }
                }
                else
                {
                throw new Exception('Can`t connect to MBG');
                }
            }
            else
            {
            throw new Exception('Can`t connect to MBG, http code = '.$http_code);
            }

        showMessage('New setting has been accepted');
        
	}catch(Exception $e){
	die($e->getMessage());
	}            
}



function myblogguest_login()
{

$auth_code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : null;
$scope     = isset($_GET['scope']) ? sanitize_text_field($_GET['scope']) : null;

        if(empty($auth_code) || !empty($scope))
        {
        return false;
        }
        
	try
	{
	
	$options = get_option('wpmbg_options');
	$client_id = isset($options['wpmbg_app_id']) ? $options['wpmbg_app_id'] : null;
	$client_secret = isset($options['wpmbg_app_secret']) ? $options['wpmbg_app_secret'] : null;
	$redirect_uri = isset($options['wpmbg_redirect_uri']) ? $options['wpmbg_redirect_uri'] : null;

		if(empty($client_id) || empty($client_secret))
		{
		throw new Exception('Bad plugin options, please reinstall plugin from MyBlogGuest');
		}
	
	$options['wpmbg_auth_code'] = $auth_code;
	
	$token_uri =  WPMBG_BASE_URL.'/token';
	$http_code =  0;
	$postdata =   'grant_type=authorization_code&client_id='.$client_id.'&client_secret='.$client_secret.'&code='.$auth_code.'&redirect_uri='.urlencode($redirect_uri);

	$res = process_http($token_uri, $http_code, $postdata);
        
		if($http_code == 200 || $http_code == 201)
		{
		$arr = json_decode($res, true);
		$options['wpmbg_oauth_token'] = $arr['access_token'];
		update_option('wpmbg_options', $options);
		}
		else
		{
		throw new Exception('Can`t connect to MBG, http code = '.$http_code);
		}
                
        showMessage('New Token Accepted!');
	}
	catch(Exception $e){
	die($e->getMessage());
	}
}
