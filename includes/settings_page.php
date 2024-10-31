<?php
function myblogguest_settings_page() {
	
	if (isset($_POST["update_settings"])) {  
    // Do the saving  
		$cat_id = intval($_POST['wpmbg_category']);
		$id_site = intval($_POST['wpmbg_id_site']);

		$options = get_option('wpmbg_options');
		$options['wpmbg_category'] 	= $cat_id;
		$options['wpmbg_id_site'] 	= $id_site;
		//$options['wpmbg_oauth_token'] = ""; Test blanking of toekn

		$updated =  update_option("wpmbg_options", $options );

		// set a good error message ....
		$error_message =  "<div class='updated settings-error'><p><strong>Settings Have Been Saved</strong></p></div>";
		
		if ($id_site == "") 
		{
			$error_message =  "<div class='error settings-error'><p><strong>Please Select A Default Blog</strong></p></div>";				
		} elseif ($cat_id == "")
		{
			$error_message =  "<div class='error settings-error'><p><strong>Please Select a Default Category</strong></p></div>";									
		}
		
		
	
	}  


?>

<?php echo $error_message;?>
	<?php 
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
?>
<?php $options = get_option('wpmbg_options'); 
	  $wpmbg_oauth_token = $options['wpmbg_oauth_token']; 
      $cats = obtain_categories($wpmbg_oauth_token); 
	  $blogs = get_list_of_blogs($wpmbg_oauth_token); 	
	  
	  if ($cats == "") {
	   	echo  "<div class='error settings-error'><p><strong>We Can't Communicate with MyBlogGuest, try again or renew access token</strong></p></div>";									
	   
	  } elseif ($blogs == "") { 
	  		echo  "<div class='error settings-error'><p><strong>You Have yet to Define Any Blogs or We Can't Communicate with MyBlogGuest</strong></p></div>";									
	   }   
}

function myBlogGuestSettingsAjax(){

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

    //get data from our ajax() call
		
    $wpmbg_app_id = sanitize_text_field($_POST['wpmbg_app_id']);
	$options = get_option('wpmbg_options');
	$options['wpmbg_app_id'] = $wpmbg_app_id;

//	$updated =  update_option("wpmbg_options", $options );

    $results = "<span style='color: Green;'> Your APP ID Has Been Saved</span>";

    // Return String
    die($results);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_myBlogGuestSettingsAjax', 'myBlogGuestSettingsAjax' );
add_action( 'wp_ajax_myBlogGuestSettingsAjax', 'myBlogGuestSettingsAjax' );

