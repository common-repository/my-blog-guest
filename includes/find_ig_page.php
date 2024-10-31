<?php
function myblogguest_find_ig_page() {

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
        showMessage("Sorry you are not authorized to access this page", true);
        return;
	}

        wp_enqueue_script('jquery');
//	wp_enqueue_script('thickbox',null,array('jquery'));
//	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');

wp_nonce_field('update-options');
settings_fields( 'wpmbg_settings' );
$options = get_option('wpmbg_options');
$wpmbg_oauth_token = $options['wpmbg_oauth_token'];

      if ($wpmbg_oauth_token == "") { 
      showMessage("This plugin has yet to be authorized, please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>initialize plugin</a> for accept connecting to MyBlogGuest!", true);
      return null;
      }  elseif (!token_okay($wpmbg_oauth_token)) {
      showMessage("Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>", true);
      exit();
      }
//var_dump($options);

echo mbgRunTpl('ig_page', array('options' => $options));
}

function mbgFindIgAjax(){

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

	//get data from our ajax() call

	// get me the options		
	$options = get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_search_string 		= sanitize_text_field($_POST['wpmbg_search_string']);
	$wpmbg_num_results 		= intval($_POST['wpmbg_num_results']);
	$wpmbg_category 		= intval($_POST['wpmbg_category']);

	$id_site = isset($options['wpmbg_id_site']) ? intval($options['wpmbg_id_site']) : 0;
	
	$wpmbg_sort_by 		= sanitize_text_field($_POST['wpmbg_sort_by']);
	$wpmbg_sort_order 	= sanitize_text_field($_POST['wpmbg_sort_order']);

	$wpmbg_start 		= intval($_POST['wpmbg_start']);
		
	$url = WPMBG_BASE_URL . "/find_ig";	
			
	$body = array("num" => $wpmbg_num_results,
		      "id_category" => $wpmbg_category,
		      "txt" => $wpmbg_search_string,
		      "start" => $wpmbg_start,
		      "sort_by" => $wpmbg_sort_by,
		      "sort_order" => $wpmbg_sort_order,
		      "id_site" => $id_site,
		      );
	
	$res = mbg_api($url, $body);
	
	    	if(is_string($res))
	    	{
	    	die (json_encode(array('err' => $res)));
	    	}
	    	else
	    	{
	    	$res['code'] = mbgRunTpl('ig_page_table', array('igs' => $res['infographics']));
	    	die (json_encode($res));
	    	}
}


// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgFindIgAjax', 'mbgFindIgAjax' );
add_action( 'wp_ajax_mbgFindIgAjax', 'mbgFindIgAjax' );



// отправка IG Offer - AJAX Handler

function mbgSendIgOfferAjax()
{

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

$err = '';
$msg = '';
$ret = array();

$id_descr		= isset($_POST['id_descr']) ? intval($_POST['id_descr']) : 0;
$offer			= isset($_POST['offer']) ? sanitize_text_field($_POST['offer']) : '';
$days			= isset($_POST['days']) ? intval($_POST['days']) : 0;

$options = get_option('wpmbg_options');
$id_site = isset($options['wpmbg_id_site']) ? intval($options['wpmbg_id_site']) : 0;

    if(empty($id_descr) || empty($id_site) || empty($days) || empty($offer))
    {
    $err = 'Bad Request!';
    }
    else
    {
    $url = WPMBG_BASE_URL . "/send_ig_offer";
	
    $body = array("id_descr"=>$id_descr, "offer" =>$offer, "days"=>$days, "id_site"=>$id_site);

    $res = mbg_api($url, $body);
	
	if(is_string($res))
	{
	$err = $res;
	}
	else
	{
	$msg = "Your Offer Has Been Submitted";
	}
    }
    
$ret['err'] = $err;
$ret['msg'] = $msg;

die(json_encode($ret));
}

add_action( 'wp_ajax_nopriv_mbgSendIgOfferAjax', 'mbgSendIgOfferAjax');
add_action( 'wp_ajax_mbgSendIgOfferAjax', 'mbgSendIgOfferAjax');

