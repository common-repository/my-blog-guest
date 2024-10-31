<?php

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'myblogguest_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'myblogguest_remove' );

	
		

/* call register settings function */
add_action( 'admin_init', 'register_wpmbg_settings' );
	
if ( is_admin() ){
	/* Call the html code and register the menus */
 	
	
	
	add_action('admin_menu', 'myblogguest_admin_menu');
	
	function myblogguest_admin_menu() {

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	return;
	}

	//add_settings_error( 'fields_main_input', 'texterror', 'Incorrect value entered!', 'error' );

	add_menu_page(WPMBG_DISPLAY_NAME, WPMBG_DISPLAY_NAME , WPMBG_REQUIRED_CAPABILITY, 'mbg-main-menu', 'myblogguest_find_articles_page','http://myblogguest.com/img/myblogguest.ico'); 
	add_submenu_page( 'mbg-main-menu', 'Find Articles', 'Find Articles', WPMBG_REQUIRED_CAPABILITY, 'mbg-main-menu', 'myblogguest_find_articles_page');
	add_submenu_page( 'mbg-main-menu', 'Article Offers', 'Article Offers', WPMBG_REQUIRED_CAPABILITY, 'article_offers', 'myblogguest_offers_admin_page');
	add_submenu_page( 'mbg-main-menu', 'Articles Given To Me', 'Articles Given To Me', WPMBG_REQUIRED_CAPABILITY, 'article_management', 'myblogguest_articles_admin_page');

		// infographics gallery
	add_submenu_page( 'mbg-main-menu', 'Find Infographics', 'Find Infographics', WPMBG_REQUIRED_CAPABILITY, 'find_ig', 'myblogguest_find_ig_page');
//	add_submenu_page( 'mbg-main-menu', 'Infographic Offers', 'Infographic Offers', WPMBG_REQUIRED_CAPABILITY, 'ig_offers', 'myblogguest_ig_offers_page');
	add_submenu_page( 'mbg-main-menu', 'Infographics Given To Me', 'Infographics Given To Me', WPMBG_REQUIRED_CAPABILITY, 'ig_management', 'myblogguest_my_ig_page');
		
		// eBooks gallery
	add_submenu_page( 'mbg-main-menu', 'Find eBooks', 'Find eBooks', WPMBG_REQUIRED_CAPABILITY, 'find_books', 'myblogguest_find_books_page');
//	add_submenu_page( 'mbg-main-menu', 'Infographic Offers', 'Infographic Offers', WPMBG_REQUIRED_CAPABILITY, 'ig_offers', 'myblogguest_ig_offers_page');
	add_submenu_page( 'mbg-main-menu', 'eBooks Given To Me', 'eBooks Given To Me', WPMBG_REQUIRED_CAPABILITY, 'books_management', 'myblogguest_my_books_page');
		
		// article requests gallery
	add_submenu_page( 'mbg-main-menu', 'Articles Request', 'Articles Request', WPMBG_REQUIRED_CAPABILITY, 'articles_request', 'myblogguest_request_admin_page');

	add_submenu_page( 'mbg-main-menu',  WPMBG_DISPLAY_NAME .' Setup', 'Settings', WPMBG_REQUIRED_CAPABILITY, 'wpmbg_settings', 'myblogguest_admin_page');	

		/* Load UP CSS & Javascript files */
		
		if (WPMBG_LOAD_JS) {
		 	wp_register_script('wpmbg_process', WPMBG_JS_URL, array('jquery') );
 		 	wp_localize_script('wpmbg_process', 'mbgAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));     
		 	wp_enqueue_script('wpmbg_process');
		 }
		
		if (WPMBG_LOAD_CSS) {
			wp_enqueue_style('mbg-style', WPMBG_CSS_URL);
		}
	}
}

	
function myblogguest_install() {
/* Creates new database field */
	add_option("wpmbg_options", '', '', 'yes');
}

function myblogguest_remove() {
/* Deletes the database field */
	delete_option('wpmbg_options');
}

function register_wpmbg_settings() {

//register our settings
	register_setting( 'wpmbg_settings', 'wpmbg_options','wpmbg_options_validate' );
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function wpmbg_options_validate($input) {
   
    return $input;
}

function my_filter($string) {

    // do stuff to string

    return $string;

}

add_filter('pre_kses', 'my_filter');


function showMessage($message, $errormsg = false)
{
	if ($errormsg) {
		echo '<div id="message" class="error">';
	}
	else {
		echo '<div id="message" class="updated fade">';
	}

	echo "<p><strong>$message</strong></p></div>";
}    

function showAdminMessages()
{
	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	return;
	}

	$options 			= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_category 		= $options['wpmbg_category'];
	$wpmbg_id_site 			= $options['wpmbg_id_site'];
	$wpmbg_version_message  = get_option('wpmbg_version_message');
	
	if ($wpmbg_oauth_token <> "") {

        $request = mbgGetRequest();
        
            if(is_array($request))
            {
        	if(!empty($request['ideas']))
                {
                    foreach($request['ideas'] as $idea)
                    {
                        if($idea['status'] == 0)
                        {
                        $msg = 'You\'ve got a new guest post idea "'.$idea['title'].'"!<br />';
                        $msg .= 'Please <a href="'.get_admin_url().'admin.php?page=articles_request">review</a> and <a href="javascript:void(0);" onclick="mbgApproveIdea('.$idea['id'].');">approve</a> if you like it!';
                        showMessage($msg, true);
                        break;
                        }
                    }
                }
            }
        
	$arr = mbgGetGTM();	
	$article_available = !empty($arr['article_available']) ? $arr['article_available'] : null;
	$article_dp = !empty($arr['article_dp']) ? $arr['article_dp'] : null;
	$ig_available = !empty($arr['ig_available']) ? $arr['ig_available'] : null;
        $book_available = !empty($arr['book_available']) ? $arr['book_available'] : null;
		
		if (!empty($article_available)){

		$msg = "<p>Congrats! Your offer for article '".$article_available['title']."' was accepted. Please go <a href='".get_admin_url()."admin.php?page=article_management&status=2&id_category=".$article_available['id_category']."'>here</a> to import it to drafts and review.</p>";

			if(!empty($article_available['authorship_name']))
			{
			$msg .= '<p>This is verified author: It is highly advisable that you create a separate contributor account for him/her on your (Wordpress) blog. The author\'s Gravatar email: <b style="color: blue;">'.$article_available['authorship_email'].'</b> The author\'s verified name: <b style="color: blue;">'.$article_available['authorship_name'].'</b>. Google Authorship is the future of the Internet and important part of SEO! Read more <a target="_blank" href="http://myblogguest.com/blog/google-authorship-practice-and-author-rank-stipulations-at-myblogguest/">here</a></p>';
			}

		showMessage($msg, true);
		}

		if (!empty($ig_available)){

		$msg = "<p>Congrats! Your offer for infographic '".$ig_available['title']."' was accepted. Please go <a href='".get_admin_url()."admin.php?page=ig_management&status=2&id_category=".$ig_available['id_category']."'>here</a> to import it to drafts and review.</p>";

			if(!empty($ig_available['authorship_name']))
			{
			$msg .= '<p>This is verified author: It is highly advisable that you create a separate contributor account for him/her on your (Wordpress) blog. The author\'s Gravatar email: <b style="color: blue;">'.$ig_available['authorship_email'].'</b> The author\'s verified name: <b style="color: blue;">'.$ig_available['authorship_name'].'</b>. Google Authorship is the future of the Internet and important part of SEO! Read more <a target="_blank" href="http://myblogguest.com/blog/google-authorship-practice-and-author-rank-stipulations-at-myblogguest/">here</a></p>';
			}

		showMessage($msg, true);
		}

		if (!empty($book_available)){

		$msg = "<p>Congrats! Your offer for eBook '".$book_available['title']."' was accepted. Please go <a href='".get_admin_url()."admin.php?page=books_management&status=2&id_category=".$book_available['id_category']."'>here</a> to import it to drafts and review.</p>";

			if(!empty($book_available['authorship_name']))
			{
			$msg .= '<p>This is verified author: It is highly advisable that you create a separate contributor account for him/her on your (Wordpress) blog. The author\'s Gravatar email: <b style="color: blue;">'.$book_available['authorship_email'].'</b> The author\'s verified name: <b style="color: blue;">'.$book_available['authorship_name'].'</b>. Google Authorship is the future of the Internet and important part of SEO! Read more <a target="_blank" href="http://myblogguest.com/blog/google-authorship-practice-and-author-rank-stipulations-at-myblogguest/">here</a></p>';
			}

		showMessage($msg, true);
		}

                
		if (!empty($article_dp)){
				// Shows as an error message. You could add a link to the right page if you wanted.
		
		$post_info = get_page_by_title(trim($article_dp['title']),'OBJECT','post');
			if(!empty($post_info))
			{
			$msg = "MyBlogGuest user has sent you a direct article '".$article_dp['title']."'. Please <a href='".get_admin_url()."post.php?post=".$post_info->ID."&action=edit'>review it here</a>. You can publish / schedule (This will update the author of the status automatically) or trash it (and thus automatically reject the article)";
			
				if(!empty($article_dp['authorship_name']))
				{
				$msg .= '<p>This is verified author: It is highly advisable that you create a separate contributor account for him/her on your (Wordpress) blog. The author\'s Gravatar email: <b style="color: blue;">'.$article_dp['authorship_email'].'</b> The author\'s verified name: <b style="color: blue;">'.$article_dp['authorship_name'].'</b>. Google Authorship is the future of the Internet and important part of SEO! Read more <a target="_blank" href="http://myblogguest.com/blog/google-authorship-practice-and-author-rank-stipulations-at-myblogguest/">here</a></p>';
				}
			}
			else
			{
                        $msg = "You have articles directly posted from MyBlogGuest that need to be published or rejected -  <a href='".  get_admin_url() ."admin.php?page=article_management&status=4'>click here to administer them</a>";
			}
			
		showMessage($msg, true);
		}
	}
	
	if ($wpmbg_version_message != "")
	{
	showMessage("$wpmbg_version_message", true);
	}

	if(defined('WPMBG_TEST_ENV'))
	showMessage('Now my-blog-guest work in TEST mode!', true);

}

/** 
  * Call showAdminMessages() when showing other admin 
  * messages. The message only gets shown in the admin
  * area, but not on the frontend of your WordPress site. 
  */
add_action('admin_notices', 'showAdminMessages');

add_action('admin_init', 'syncPosts');


// backwards compatible (before WP 3.0)
// add_action( 'admin_init', 'myplugin_add_custom_box', 1 );

/* Do something with the data entered */
//add_action( 'save_post', 'myplugin_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function mbg_add_custom_box() {
	  echo '<div style="width:25%;" class="postbox-container ">';        
      wp_enqueue_script('postbox');
	  add_meta_box('metabox_like', 'My Blog Guest', metabox_info, 'mappress_sidebar', 'side', 'core');
      do_meta_boxes('mappress_sidebar', 'side', null);    
	  echo "</div>";   
}

   function metabox_info() {
        echo "<ul>";
        echo '<li><a href="'. get_admin_url() .'admin.php?page=mbg-main-menu">Here</a>: Browse guest articles, hover over any of them to preview it or offer your blog for free</li>';
        echo '<li><a href="'. get_admin_url() .'admin.php?page=article_offers">Here</a>: Please see all your guest post offers on this page</li>';
        echo '<li><a href="http://myblogguest.com/" target="_blank"><img src="'.WPMBG_IMGS.'/myblogguest-125x125.jpg" alt="My Blog Guest" width="125" height="125" border="0"/></a>';
        echo "</li>";
        echo "</ul>";
    }


    /* post status handling */
    
function mbg_draft_to_publish($post)
{
	/*
	if this post comes from MBG, we need to inform MBG author that his post is published
	*/
	
$options        		= get_option('wpmbg_options');
$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];

	// определяем, не основан ли данный пост на статье с MBG
	
$article = mbgGetUnpublishedArticle($post->post_title);

	if(!empty($article))
	{
		if($article->status == 4)
		{
				// approve Direct Post
				
		$save_url = WPMBG_BASE_URL . "/approve_dp";
		$request = new WP_Http;
		
		$body = array("oauth_token" => $wpmbg_oauth_token, "id_article" =>$article->id, "type"=>'published', "url"=>$post->guid);

		$result = $request->request($save_url,array('method' => 'POST','body' =>$body));
		}
		
		if($article->status == 2)
		{
				// confirm publication
				
		$save_url = WPMBG_BASE_URL . "/save_url";
		$request = new WP_Http;
		
		$body = array("oauth_token" => $wpmbg_oauth_token, "id_article" =>$article->id, "type"=>'published', "url"=>$post->guid);

		//print_r($body);
		$result = $request->request($save_url, array('method' => 'POST', 'body' =>$body));
		}
		
        
	mbgUpdateGTMCache();
	
	return;
	}
	
	
	// определяем, не основан ли данный пост на статье с MBG
	
$descr = mbgGetUnpublishedIgDescr($post->post_title);

	if(!empty($descr))
	{		
		if($descr->status == 2)
		{
				// confirm publication
				
		$save_url = WPMBG_BASE_URL . "/save_ig_url";
		
		$body = array("id_descr" =>$descr->id, "type"=>'published', "url"=>$post->guid);

		//print_r($body);
		$result = mbg_api($save_url, $body);
		}
		
	mbgUpdateGTMCache();
	}
        
	// определяем, не основан ли данный пост на описании книги с MBG
	
$descr = mbgGetUnpublishedBookDescr($post->post_title);

	if(!empty($descr))
	{		
		if($descr->status == 2)
		{
				// confirm publication
				
		$save_url = WPMBG_BASE_URL . "/save_book_url";
		
		$body = array("id_descr" =>$descr->id, "type"=>'published', "url"=>$post->guid);

		//print_r($body);
		$result = mbg_api($save_url, $body);
		}
		
	mbgUpdateGTMCache();
	}
}

add_action( 'draft_to_publish', 'mbg_draft_to_publish' );

function mbg_draft_to_future($post)
{
	/*
	if this post comes from MBG, we need to inform MBG author that his post is sheduled
	*/
	
$options        		= get_option('wpmbg_options');
$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];

$article = mbgGetUnpublishedArticle($post->post_title);

	if(!empty($article))
	{
	$date_info = explode(" ", $post->post_date);
	$shedule_date = $date_info[0];
	
		if(!empty($shedule_date))
		{
			if($article->status == 4)
			{
				// approve Direct Post
				
			$save_url = WPMBG_BASE_URL . "/approve_dp";
		
			$body = array("id_article" => $article->id, "type"=> 'scheduled', "shedule_date"=> $shedule_date, "url"=> $post->guid);

			$result = mbg_api($save_url, $body);
			}
		
			if($article->status == 2)
			{
				// confirm publication
				
			$save_url = WPMBG_BASE_URL . "/save_url";
		
			$body = array("id_article" =>$article->id, "type"=>'scheduled', "shedule_date"=>$shedule_date,  "url"=>$post->guid);

				//print_r($body);
			$result = mbg_api($save_url, $body);
			}
		}
		
	mbgUpdateGTMCache();
	return;
	}
	
	
$descr = mbgGetUnpublishedIgDescr($post->post_title);

	if(!empty($descr))
	{
	$date_info = explode(" ", $post->post_date);
	$shedule_date = $date_info[0];
	
		if(!empty($shedule_date))
		{		
			if($descr->status == 2)
			{
				// confirm publication
				
			$save_url = WPMBG_BASE_URL . "/save_ig_url";
		
			$body = array("id_descr" =>$descr->id, "type"=>'scheduled', "shedule_date"=>$shedule_date,  "url"=>$post->guid);

				//print_r($body);
			$result = mbg_api($save_url, $body);
			}
		}
		
	mbgUpdateGTMCache();
	}
        
$descr = mbgGetUnpublishedBookDescr($post->post_title);

	if(!empty($descr))
	{
	$date_info = explode(" ", $post->post_date);
	$shedule_date = $date_info[0];
	
		if(!empty($shedule_date))
		{		
			if($descr->status == 2)
			{
				// confirm publication
				
			$save_url = WPMBG_BASE_URL . "/save_book_url";
		
			$body = array("id_descr" =>$descr->id, "type"=>'scheduled', "shedule_date"=>$shedule_date,  "url"=>$post->guid);

				//print_r($body);
			$result = mbg_api($save_url, $body);
			}
		}
		
	mbgUpdateGTMCache();
	}
}

add_action( 'draft_to_future', 'mbg_draft_to_future' );

function mbg_draft_to_trash($post)
{
	/*
	if this post comes from MBG, we need to inform MBG author that his post is removed
	*/
	
$options        		= get_option('wpmbg_options');
$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];

$article = mbgGetUnpublishedArticle($post->post_title);

	if(!empty($article))
	{
		if($article->status == 4)
		{
				// reject Direct Post
				
		$save_url = WPMBG_BASE_URL . "/reject_dp";
		
                    if(!empty($_SESSION['article_reject_reasons']) && !empty($_SESSION['article_reject_reasons'][$article->id]))
                    {
                    $reason = $_SESSION['article_reject_reasons'][$article->id];
                    }
                    else
                    {
                    $reason = 'Article removed from the blog.';
                    }
                        
		$body = array("id_article" => $article->id, "reason" => $reason);

		$result = mbg_api($save_url, $body);
		}
		
		if($article->status == 2)
		{
				// reject article publication
				
		$save_url = WPMBG_BASE_URL . "/refuse";

                    if(!empty($_SESSION['article_reject_reasons']) && !empty($_SESSION['article_reject_reasons'][$article->id]))
                    {
                    $reason = $_SESSION['article_reject_reasons'][$article->id];
                    }
                    else
                    {
                    $reason = 'Article removed from the blog.';
                    }

		$body = array("id_article" => $article->id, "reason" => $reason);

		//print_r($body);
		$result = mbg_api($save_url, $body);
		}
	
	mbgUpdateGTMCache();
	return;
	}
	
	// Infographics
        
$descr = mbgGetUnpublishedIgDescr($post->post_title);

	if(!empty($descr))
	{
		if($descr->status == 2)
		{
				// confirm publication
				
		$save_url = WPMBG_BASE_URL . "/refuse_ig";

                    if(!empty($_SESSION['ig_reject_reasons']) && !empty($_SESSION['ig_reject_reasons'][$descr->id]))
                    {
                    $reason = $_SESSION['ig_reject_reasons'][$descr->id];
                    }
                    else
                    {
                    $reason = 'Article removed from the blog.';
                    }

		$body = array("id_descr" => $descr->id, "reason" => $reason);

		//print_r($body);
		$result = mbg_api($save_url, $body);
		}
	
	mbgUpdateGTMCache();
	return;
	}

        // eBooks
$descr = mbgGetUnpublishedBookDescr($post->post_title);

	if(!empty($descr))
	{
		if($descr->status == 2)
		{
				// discard publication
				
		$save_url = WPMBG_BASE_URL . "/refuse_book";

                    if(!empty($_SESSION['book_reject_reasons']) && !empty($_SESSION['book_reject_reasons'][$descr->id]))
                    {
                    $reason = $_SESSION['book_reject_reasons'][$descr->id];
                    }
                    else
                    {
                    $reason = 'Article removed from the blog.';
                    }

		$body = array("id_descr" => $descr->id, "reason" => $reason);

		//print_r($body);
		$result = mbg_api($save_url, $body);
		}
	
	mbgUpdateGTMCache();
	return;
	}
}

add_action( 'draft_to_trash', 'mbg_draft_to_trash' );
add_action( 'publish_to_trash', 'mbg_draft_to_trash' );
add_action( 'future_to_trash', 'mbg_draft_to_trash' );


function mbgGetUnpublishedArticle($title)
{
	$options 				= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_category 		= $options['wpmbg_category'];
	$wpmbg_id_site 			= $options['wpmbg_id_site'];
	$wpmbg_version_message  = get_option('wpmbg_version_message');
	//print_r($wpmbg_version_message);
//	echo "bang";
	
	if ($wpmbg_oauth_token <> "") {

		$articles = get_articles_given_to_me($options, "", -1, 100, 0, $wpmbg_id_site, "publish_date", "desc", "2,4");

		if (!empty($articles)) {
			foreach ($articles['articles'] as $article)
			{
				if(cmpTitle($title, $article['title']))
				return array_to_obj($article);
			
			}	// end foreach
		}
	}
	
return null;
}


function mbgGetUnpublishedIgDescr($title)
{
	$options 				= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_category 		= $options['wpmbg_category'];
	$wpmbg_id_site 			= $options['wpmbg_id_site'];
	$wpmbg_version_message  = get_option('wpmbg_version_message');
	//print_r($wpmbg_version_message);
//	echo "bang";
	
	if ($wpmbg_oauth_token <> "") {

	$url = WPMBG_BASE_URL . "/given_to_me_ig";	
			
	$body = array(
		      "status" => 2,
		      "id_site" => $wpmbg_id_site,
		      );
	
	$res = mbg_api($url, $body);

		if(is_array($res) && !empty($res['infographics']))
		{
			foreach($res['infographics'] as $ig)
			{
				if(!empty($ig['descriptions']))
				{
					foreach($ig['descriptions'] as $descr)
					{
						if(cmpTitle($title, $descr['title']))
						return array_to_obj($descr);
					}
				}
			}
		}
	}
	
return null;
}


function mbgGetUnpublishedBookDescr($title)
{
	$options 				= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_category 		= $options['wpmbg_category'];
	$wpmbg_id_site 			= $options['wpmbg_id_site'];
	$wpmbg_version_message  = get_option('wpmbg_version_message');
	//print_r($wpmbg_version_message);
//	echo "bang";
	
	if ($wpmbg_oauth_token <> "") {

	$url = WPMBG_BASE_URL . "/given_to_me_books";
			
	$body = array(
		      "status" => 2,
		      "id_site" => $wpmbg_id_site,
		      );
	
	$res = mbg_api($url, $body);

		if(is_array($res) && !empty($res['books']))
		{
			foreach($res['books'] as $book)
			{
				if(!empty($book['descriptions']))
				{
					foreach($book['descriptions'] as $descr)
					{
						if(cmpTitle($title, $descr['title']))
						return array_to_obj($descr);
					}
				}
			}
		}
	}
	
return null;
}


    // common ajax handlers

function mbgLoadTplAjax()
{
    if(!isset($_POST) || !is_array($_POST))
    {
    $_POST = array();
    }
    
$ret = array();
$err = '';

$tpl = isset($_POST['tpl']) ? trim(sanitize_text_field($_POST['tpl'])) : '';


    if(empty($tpl))
    {
    $err .= 'Bad tpl!';
    }
    
    if(empty($err))
    {
    $ret['code'] = mbgRunTpl($tpl, $_POST);
    }
    
die(json_encode($ret));
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgLoadTplAjax', 'mbgLoadTplAjax' );
add_action( 'wp_ajax_mbgLoadTplAjax', 'mbgLoadTplAjax' );


        /*
         *  принимает URL поста в $_POST['url'] и определяет, создан ли данный пост из
         *  статьи или описания с MBG. 
         *  Возвращает ID сущности с MBG, из которой создан пост
         */

function mbgIsMBGArticle()
{
    if(!isset($_POST) || !is_array($_POST))
    {
    $_POST = array();
    }
    
$ret = array();
$ret['id_article'] = 0;
$ret['id_ig_descr'] = 0;
$err = '';
$options = get_option('wpmbg_options');
$published_articles = isset($options['published_articles']) ? $options['published_articles'] : array();
$published_ig_descr = isset($options['published_ig_descr']) ? $options['published_ig_descr'] : array();
$published_books_descr = isset($options['published_books_descr']) ? $options['published_books_descr'] : array();

$url = isset($_POST['url']) ? trim(sanitize_text_field($_POST['url'])) : '';

$query = parse_url($url, PHP_URL_QUERY);
$vars = array();
parse_str($query, $vars);
$post_id = isset($vars['post']) ? intval($vars['post']) : 0;

    if(!empty($post_id))
    {
        if(!empty($published_articles))
        {
        $ret['id_article'] = array_search($post_id, $published_articles);
        }

        if(empty($ret['id_article']))
        {
            if(!empty($published_ig_descr))
            {
            $ret['id_ig_descr'] = array_search($post_id, $published_ig_descr);
            }        
        }
        
        if(empty($ret['id_article']) && empty($ret['id_ig_descr']))
        {
            if(!empty($published_books_descr))
            {
            $ret['id_book_descr'] = array_search($post_id, $published_books_descr);
            }        
        }
    }
    
die(json_encode($ret));
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgIsMBGArticle', 'mbgIsMBGArticle' );
add_action( 'wp_ajax_mbgIsMBGArticle', 'mbgIsMBGArticle' );


    /*
     * Сохраняет в сессии reject reason введенный пользователем, для статьи, инфографики или книги
     */

function mbgSaveRejectReason()
{
    if(!isset($_POST) || !is_array($_POST))
    {
    $_POST = array();
    }
    
$ret = array();
$err = '';

$reason = isset($_POST['reason']) ? trim(sanitize_text_field($_POST['reason'])) : '';
$id_article = isset($_POST['id_article']) ? intval($_POST['id_article']) : 0;
$id_ig_descr = isset($_POST['id_ig_descr']) ? intval($_POST['id_ig_descr']) : 0;
$id_book_descr = isset($_POST['id_book_descr']) ? intval($_POST['id_book_descr']) : 0;


    if(!empty($reason))
    {
        if(!empty($id_article))
        {
            if(empty($_SESSION['article_reject_reasons']))
            {
            $_SESSION['article_reject_reasons'] = array();
            }
        $_SESSION['article_reject_reasons'][$id_article] = $reason;
        }
        
        if(!empty($id_ig_descr))
        {
            if(empty($_SESSION['ig_reject_reasons']))
            {
            $_SESSION['ig_reject_reasons'] = array();
            }
        $_SESSION['ig_reject_reasons'][$id_ig_descr] = $reason;
        }
        
        if(!empty($id_book_descr))
        {
            if(empty($_SESSION['book_reject_reasons']))
            {
            $_SESSION['book_reject_reasons'] = array();
            }
        $_SESSION['book_reject_reasons'][$id_book_descr] = $reason;
        }
    }

die(json_encode($ret));
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgSaveRejectReason', 'mbgSaveRejectReason' );
add_action( 'wp_ajax_mbgSaveRejectReason', 'mbgSaveRejectReason' );

