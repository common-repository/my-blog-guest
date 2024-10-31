<?php
function myblogguest_my_ig_page() {

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
        showMessage("Sorry you are not authorized to access this page", true);
        return;
	}

$options = get_option('wpmbg_options');
	
$wpmbg_status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;


wp_enqueue_script('jquery');
//	wp_enqueue_script('thickbox',null,array('jquery'));
//	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');


wp_nonce_field('update-options');
settings_fields( 'wpmbg_settings' );

$wpmbg_oauth_token = $options['wpmbg_oauth_token'];

    if ($wpmbg_oauth_token == "") { 
    showMessage("This plugin has yet to be authorized, please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>initialize plugin</a> for accept connecting to MyBlogGuest!", true);
    return null;
    }  elseif (!token_okay($wpmbg_oauth_token)) {
    showMessage("Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>", true);
    exit();
    }

echo mbgRunTpl('ig_given_to_me', array('options' => $options, 'wpmbg_status' => $wpmbg_status));
}

function mbgIgGivenToMeAjax(){

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
	$wpmbg_status	 		= intval($_POST['wpmbg_status']);

	$id_site = isset($options['wpmbg_id_site']) ? intval($options['wpmbg_id_site']) : 0;
	
	$wpmbg_sort_by 		= sanitize_text_field($_POST['wpmbg_sort_by']);
	$wpmbg_sort_order 	= sanitize_text_field($_POST['wpmbg_sort_order']);

	$wpmbg_start 		= intval($_POST['wpmbg_start']);
		
	$url = WPMBG_BASE_URL . "/given_to_me_ig";	
			
	$body = array("num" => $wpmbg_num_results,
		      "id_category" => $wpmbg_category,
		      "txt" => $wpmbg_search_string,
		      "status" => $wpmbg_status,
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
	    	$res['code'] = mbgRunTpl('ig_given_to_me_table', array('igs' => $res['infographics']));
	    	die (json_encode($res));
	    	}
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgIgGivenToMeAjax', 'mbgIgGivenToMeAjax');
add_action( 'wp_ajax_mbgIgGivenToMeAjax', 'mbgIgGivenToMeAjax');


function mbgImportIgToDraftAjax(){
	// This will obtain the ig description using the ID then attempt to publish directly to wordpress
	
    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 			= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_id_descr	 		= intval($_REQUEST['wpmbg_id_descr']);

	$url = WPMBG_BASE_URL . "/ig_descr_code/$wpmbg_id_descr";
	$descr = mbg_api($url);

		if(is_string($descr)) die($descr);
		
	global $user_ID;
	
	$descr_code = str_replace('&nbsp;', ' ', $descr['code']);
	//$descr_code = str_replace("\xA0", ' ', html_entity_decode($descr_code));
	
	$new_post = array(
	'post_title' => $descr['title'],
	'post_content' => $descr_code,
	'post_status' => 'draft',
	'post_date' => date('Y-m-d H:i:s'),
	'post_author' => $user_ID,
	'post_type' => 'post',
	'post_category' => array(0),
	'filter' => true
	);
	
	$old_post = mbgGetPostByTitle(trim($descr['title']));
	
		if(!isset($options['published_ig_descr']))
		{
		$options['published_ig_descr'] = array();
		}
		
			// Check whether post already exists
		if(!empty($old_post) && !empty($options['published_ig_descr'][$wpmbg_id_descr]) && $options['published_ig_descr'][$wpmbg_id_descr] == $old_post->ID)
		{
		$message = "Already exists";
		}
		else
		{
		$post_id = wp_insert_post($new_post);
		
			if (!empty($post_id))
			{
			$options['published_ig_descr'][$wpmbg_id_descr] = $post_id;

			update_option('wpmbg_options', $options);
			$message = "Post Has Been Inserted Into Wordpress, click here to edit your post";
			$message .= "\n<a target='_new'   href='". site_url() . "/wp-admin/post.php?post=$post_id&action=edit'>Edit</a>";


				// загружаем инфографику и вставляем ее в пост в виде вложения 
				
				try{
				$wp_upload_dir = wp_upload_dir();
				$filename = $wp_upload_dir['path']."/".$descr['filename'];
			
					if(!file_exists($filename) && is_writable($wp_upload_dir['path']))
					{
					//file_put_contents($filename, fopen($descr['resized_img_url'], 'r'));
				
					mbg_download_file($descr['resized_img_url'], $filename);
				
					$wp_filetype = wp_check_filetype(basename($filename), null);
				
						if(file_exists($filename) && $wp_filetype['type'] == 'image/jpeg')
						{

						$attachment = array(
							'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ), 
							'post_mime_type' => $wp_filetype['type'],
							'post_title' => $descr['title'],
							'post_content' => '',
							'post_status'  => 'inherit',
							);

						$attach_id = wp_insert_attachment($attachment, $filename, $post_id);

							if($attach_id)
							{
							require_once(ABSPATH . 'wp-admin/includes/image.php');
							$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

							wp_update_attachment_metadata( $attach_id,  $attach_data );
						
							$descr_code = '<p><img src="'.$wp_upload_dir['url']."/".$descr['filename'].'" /></p> '.$descr_code;
							wp_update_post(array(
						 	       	'ID' => $post_id,
						 	       	'post_content' => $descr_code
						 	       ));
							}
							else
							{
							throw new Exception("Can't insert attachment");
							}
						}
						else
						{
						throw new Exception("Can't download file");
						}						
					}
					else
					{
					throw new Exception("Can't download file");
					}
			
				}
				catch(Exception $e)
				{
				$message = "Failed insert image to post! (".$e->getMessage()."). Please try to <a href='".$descr['img_download_url']."'>manually image download</a>.";
				}
			} 
			else 
			{
				// inserting post failed -- opps
			$message = "Inserting Post Into WordPress Failed";
			}
		}
		
	//print_r($wpmbg_id_article);
    // Return String
    die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgImportIgToDraftAjax', 'mbgImportIgToDraftAjax' );
add_action( 'wp_ajax_mbgImportIgToDraftAjax', 'mbgImportIgToDraftAjax' );



function mbgIgNotifyMBGAjax(){
	// This will obtain the article using the ID then attempt to publish directly to wordpress
	
    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 			= get_option('wpmbg_options');
	$wpmbg_id_descr	 		= intval($_POST['wpmbg_id_descr']);

	
	// use this to get the post_id
	
	if(isset($options['published_ig_descr']))
	{
	$published_ig_descr = &$options['published_ig_descr'];
	}
	else
	{
	$published_ig_descr = array();
	}
	
	if (array_key_exists($wpmbg_id_descr, $published_ig_descr))
	{
	$post_id = $published_ig_descr[$wpmbg_id_descr];
	}
	
	
	if ($post_id <> "")
	{
	// post_id has been located lets get some info.
	$post_info = get_post($post_id);
		
	$do_ajax = true;
		if ($post_info->post_status == "publish")
		{
			$post_status = "published";
		} else if($post_info->post_status == "future"){
			$post_status = "scheduled";
		}
		else {
			$message = "First please publish or schedule an article!";
			$do_ajax = false;
		}
		
			//	print_r($post_info);
		if($do_ajax)
		{
		$date_info = explode(" ", $post_info->post_date);
		
		$save_url = WPMBG_BASE_URL . "/save_ig_url";
		
		$body = array("id_descr" => $wpmbg_id_descr, "url" => $post_info->guid, "type" => $post_status, "shedule_date" => $date_info[0]);
	
		$res = mbg_api($save_url, $body);
		
			if(is_string($res))
			{
			$message = $res;
			} 
			else 
			{
			$message ="MyBlogGuest Have Been Informed";
			unset($published_ig_descr[$wpmbg_id_descr]);
			update_option('wpmbg_options', $options);
			}
		}					
	}
	else 
	{	
	$message = "Wordpress could not locate your post, please Publish this article again";
	}

    mbgUpdateGTMCache();
    
    // Return String
    die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgIgNotifyMBGAjax', 'mbgIgNotifyMBGAjax' );
add_action( 'wp_ajax_mbgIgNotifyMBGAjax', 'mbgIgNotifyMBGAjax' );


function mbgIgRejectAjax(){
	// This will send reject message to MBG
	
    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 		= get_option('wpmbg_options');
	$wpmbg_id_descr	 	= intval($_POST['wpmbg_id_descr']);
	$wpmbg_refuse_reason	= sanitize_text_field($_POST['wpmbg_refuse_reason']);
	
	$save_url = WPMBG_BASE_URL . "/refuse_ig";
		
	$body = array("id_descr" => $wpmbg_id_descr,"reason" => $wpmbg_refuse_reason);


	$res = mbg_api($save_url, $body);

		if(is_string($res))
		{
		$message = $res;
		} 
		else 
		{
		$message ="MyBlogGuest Have Been Informed";
		mbgDelDraftIgDescr($wpmbg_id_descr);
		}

   mbgUpdateGTMCache();
   die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgIgRejectAjax', 'mbgIgRejectAjax' );
add_action( 'wp_ajax_mbgIgRejectAjax', 'mbgIgRejectAjax' );


