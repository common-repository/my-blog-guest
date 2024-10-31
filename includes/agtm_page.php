<?php



function myblogguest_articles_admin_page() {

$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

?>
<div class="wrap">
<h2><?php echo WPMBG_DISPLAY_NAME ?> Articles Given To Me</h2>
<?php 
	wp_enqueue_script('jquery');
//	wp_enqueue_script('thickbox',null,array('jquery'));
//	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	
?>

<?php wp_nonce_field('update-options'); ?>
<?php settings_fields( 'wpmbg_settings' ); ?>
<?php $options = get_option('wpmbg_options'); ?>
<?php $wpmbg_oauth_token = $options['wpmbg_oauth_token']; ?>
<?php if ($wpmbg_oauth_token == "") { 
  showMessage("This plugin has yet to be authorized, please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>initialize plugin</a> for accept connecting to MyBlogGuest!", true);
return null;
} elseif (!token_okay($wpmbg_oauth_token)) {
 showMessage("Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>", true);
 exit();
}
?>
<div id="poststuff" >
<div style="width:75%;" class="postbox-container">
<div class="search_form">

<label>Search String:</label>
<input name="wpmbg_search_string" id="wpmbg_search_string" size="30" />
<label>Number of Results:</label>
<select name="wpmbg_num_results" id="wpmbg_num_results">
<option value="5">5</option>
<option value="10">10</option>
<option value="50">50</option>
<option value="100">100</option>
</select>
<select name="wpmbg_sort_by" id="wpmbg_sort_by">
<option value="publish_date">Publish Date</option>
<option value="title">Title</option>
<option value="username">Username</option>
<option value="rating">Rating</option>
</select>

<select name="wpmbg_sort_order" id="wpmbg_sort_order">
<option value="asc">Asc</option>
<option value="desc">Desc</option>
</select>
<label>Status:</label>
<select name="wpmbg_status" id="wpmbg_status">
<option value="">All</option>
<option value="2" <?php if($status == 2) echo ' selected="selected" '; ?>>Pending Publication</option>
<option value="3" <?php if($status == 3) echo ' selected="selected" '; ?>>Published</option>
<option value="4" <?php if($status == 4) echo ' selected="selected" '; ?>>Direct Post Articles</option>
</select>

<input type="hidden" name="wpmbg_start" id="wpmbg_start" value="0"/>
<input type="button" class="button" id="wpmbg_agtm_submit" value="Search" /><span class="loading" style="display:none"><img src="<?php echo WPMBG_IMGS ?>/loading.gif" /></span><span id="wpmbg_search_status"></span>
</div>
<div id="search_results">

<table cellspacing="0" class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
    <th style="" class="manage-column column-status  desc" id="status" scope="col"><span>Status</span></th>
    <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th>
    <th style="" class="manage-column column-pin  desc" id="author" scope="col"><span>Authors Rating</span></th>  
    <th style="" class="manage-column column-pin  desc" id="author" scope="col"><span>Rate Author</span></th>  
    <th style="" class="manage-column column-categories" id="categories" scope="col">Category</th>
    <th style="" class="manage-column column-pin" id="tags" scope="col">Pin</th>
    <th style="" class="manage-column column-pin" id="tags" scope="col">Word Count</th>           
    <th style="" class="manage-column column-date  asc" id="date" scope="col"><span>Publish Date</span></a></th>
    </tr>
	</thead>
	<tfoot>
	<tr>
	    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
        <th style="" class="manage-column column-status  desc" id="status" scope="col"><span>Status</span></th>
        <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th>
        <th style="" class="manage-column column-pin  desc" id="author" scope="col"><span>Authors Rating</span></th>  
        <th style="" class="manage-column column-pin  desc" id="author" scope="col"><span>Rate Author</span></th>  
        <th style="" class="manage-column column-categories" id="categories" scope="col">Category</th>
        <th style="" class="manage-column column-pin" id="tags" scope="col">Pin</th>
        <th style="" class="manage-column column-pin" id="tags" scope="col">Word Count</th>               
        <th style="" class="manage-column column-date  asc" id="date" scope="col"><span>Publish Date</span></a></th>
       </tr>
	</tfoot>

	<tbody id="the-list">
    <tr><td span="2">Please Enter A Search Query</td></tr>
	</tbody>
</table>
</div><br />
<span id="wpmbg_prev_area">
<input class="button" type="button" name="wpmbg_agtm_prev" id="wpmbg_agtm_prev" value="Prev"/>
</span>
<span id="wpmbg_next_area">
<input class="button" type="button" name="wpmbg_agtm_next" id="wpmbg_agtm_next" value="Next"/>

</span>


<!-- Table row information to supply the JS-->
<table style="visibility:hidden">
<tbody id="table_row">
<tr valign="top" class="clone_results" id="post-3">
			<td class="post-title page-title column-title"><strong>
            <a href='#' title='MyGuestBlog Artcile Preview' class='row-title'>%TITLE%</a></strong>
            <small>%DESCR%</small>
            <span id="wpmbg_update_status%ARTICLE_ID%"></span>

<!--STANDARDFORM--> 
<div style="display:%STANDARDFORM%" id="StandardForm%ARTICLE_ID%">           
<div class="row-actions" ><span class="edit"><a id='publish_article' onclick="publishArticle('%ARTICLE_ID%');return false;" href='javascript:void(0)'>Import to drafts</a> | <a id='wpmbg_notify_mbg' href='javascript:void(0)' onclick="notifyMBG('%ARTICLE_ID%');return false;"> Inform <?php echo WPMBG_DISPLAY_NAME ?> of Publication</a> | </span> <span class="trash"> <a id='refuse_article' href='javascript:void(0)' onclick='mbgRejectArticle(%ARTICLE_ID%);' class="trash reject"><small>%REJECT%</small></a>  </span></div>
</div>
<!--/STANDARDFORM-->
<!-- ######### Direct Post Forms ########## -->
<!--DPFORM-->
<div style="display:%DPFORM%" id="DPForm%ARTICLE_ID%">           
<div class="row-actions">
<span class="edit">
<a id='approve_article' href='javascript:void(0)' onclick='jQuery("#wpmbg_approve_dp_form%ARTICLE_ID%").show(); return false;' class="trash reject" style='color: green;'><small>Approve DP</small></a> |
<a id="reject_article" href="javascript:void(0)" onclick="jQuery('#wpmbg_approve_dp_form%ARTICLE_ID%').hide(); sendRejectDP(%ARTICLE_ID%); return false;" class="trash reject" style="color: red;"><small>Reject DP</small></a>

<!-- Start of Approve Form -->
<span id="wpmbg_approve_dp_form%ARTICLE_ID%" style="display:none">
<form>
<input type="hidden" id="id_article" name="id_article" value="%ARTICLE_ID%" />
<input type="hidden" id="article_title%ARTICLE_ID%" name="article_title%ARTICLE_ID%" value="%TITLE%" />

<input type="radio" id="pub%ARTICLE_ID%" name="pub_sch%ARTICLE_ID%" value="published" onclick="jQuery('#wpmbg_approve_dp_scheduled_form%ARTICLE_ID%').hide();"><small>Published</small><br>
<input type="radio" id="sch%ARTICLE_ID%" name="pub_sch%ARTICLE_ID%" value="scheduled" onclick="jQuery('#wpmbg_approve_dp_scheduled_form%ARTICLE_ID%').show();"><small>Scheduled</small><br> 
<span id="wpmbg_approve_dp_scheduled_form%ARTICLE_ID%" style="display:none">
<small>Scheduled Date:<input id="sch_date%ARTICLE_ID%" type="text" class="datepicker" /></small>
</span>
<input type="button" onclick="sendApproveDP('%ARTICLE_ID%')" value="Approve DP" />
</form>
</span>        
<!-- End of Approve Form -->
</span>
</div></div>
<!--/DPFORM-->
<!-- ######### End Direct Post Forms ########## -->




		</td>
        <td class="status column-status">%STATUS%
        <br>


        </td>
        <td class="author column-author"><a target="_new" href="http://myblogguest.com/forum/profile.php?id=%ID_USER_AUTHOR%">%AUTHOR%</a>%GRAVATAR%</td>
        <td class="author column-author">%AUTHOR_RATING%</td>
        <td class="author column-pin"><a onclick="rateAuthor('%ID_USER_AUTHOR%',1,%AUTHOR_RATING%)" href="javascript: void(0);" ><img src="<?php echo WPMBG_IMGS ?>/thumb-up.png" /></a><a onclick="rateAuthor('%ID_USER_AUTHOR%',-1,%AUTHOR_RATING%)" href="javascript: void(0);" ><img src="<?php echo WPMBG_IMGS ?>/thumb-down.png" /></a></td>
		<td class="categories column-categories">%CATEGORY%</td>
		<td class="column-pin">%PIN%</td>
		<td class="column-pin">%NUM_WORDS%</td>        
		<td class="date column-date"><abbr title="">%DATE%</abbr></td>
     </tr>
     </tbody>
</table>
<!-- end of table row info -->


</div>
     

<?php
mbg_add_custom_box();
?>
</div></div>
<?php
}



function get_articles_given_to_me($options, $wpmbg_search_string ="", $wpmbg_category="", $wpmbg_num_results="5", $wpmbg_start="0", $wpmbg_id_site, $wpmbg_sort_by="publish_date", $wpmbg_sort_order="asc", $wpmbg_status="")
{
    $articles = "";
	$articles_url = WPMBG_BASE_URL . "/given_to_me_articles";
	
	$body = array("num"=>$wpmbg_num_results,"txt"=> $wpmbg_search_string,"start" =>$wpmbg_start,"id_site"=>$wpmbg_id_site,"sort_by"=>$wpmbg_sort_by,"sort_order"=>$wpmbg_sort_order,"status"=>$wpmbg_status);

	
	$articles = mbg_api($articles_url, $body);
	//print_r($result);

	$articles = str_replace("<br />", "", $articles);
	
return $articles;
	
} // end get articles given to me

function mbgArticlesGivenToMeAjax(){

    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 				= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];	
	$wpmbg_id_site 			= $options['wpmbg_id_site'];
	
	$wpmbg_search_string 		= sanitize_text_field($_POST['wpmbg_search_string']);
	$wpmbg_num_results 		= intval($_POST['wpmbg_num_results']);
	$wpmbg_category 		= intval($_POST['wpmbg_category']);
	$wpmbg_start 			= intval($_POST['wpmbg_start']);
	$wpmbg_sort_by 			= sanitize_text_field($_POST['wpmbg_sort_by']);
	$wpmbg_sort_order 		= sanitize_text_field($_POST['wpmbg_sort_order']);	
	$wpmbg_status 			= intval($_POST['wpmbg_status']);	
	
	$articles = get_articles_given_to_me($options,$wpmbg_search_string,$wpmbg_category,$wpmbg_num_results,$wpmbg_start,$wpmbg_id_site,$wpmbg_sort_by,$wpmbg_sort_order,$wpmbg_status);
	//print_r($articles);
    // Return String
    die(json_encode($articles));
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgArticlesGivenToMeAjax', 'mbgArticlesGivenToMeAjax' );
add_action( 'wp_ajax_mbgArticlesGivenToMeAjax', 'mbgArticlesGivenToMeAjax' );




function mbgPublishArticleAjax(){
	// This will obtain the article using the ID then attempt to publish directly to wordpress
	
    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 			= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_id_article	 	= intval($_REQUEST['wpmbg_id_article']);

	$article_url = WPMBG_BASE_URL . "/article_code/$wpmbg_id_article";
	$article = mbg_api($article_url);

		if(is_string($article)) die($article);
		
//	echo "<pre>";
//	print_r($article);
//	echo "</pre>\n";
	
	global $user_ID;
	
	$article_code = str_replace('&nbsp;', ' ', $article['code']);
	//$article_code = str_replace("\xA0", ' ', html_entity_decode($article_code));
	
	$new_post = array(
	'post_title' => $article['title'],
	'post_content' => $article_code,
	'post_status' => 'draft',
	'post_date' => date('Y-m-d H:i:s'),
	'post_author' => $user_ID,
	'post_type' => 'post',
	'post_category' => array(0),
	'filter' => true
	);
	
	$old_post = mbgGetPostByTitle(trim($article['title']));
	
			// Check whether post already exists
		if(!empty($old_post) && $options['published_articles'][$wpmbg_id_article] == $old_post->ID)
		{
		$message = "Already exists";
		}
		else
		{
		$post_id = wp_insert_post($new_post);
		
			if (!empty($post_id))
			{
			$options['published_articles'][$wpmbg_id_article] = $post_id;

			update_option('wpmbg_options',$options);
			$message = "Post Has Been Inserted Into Wordpress, click here to edit your post";
			$message .= "\n<a target='_new'   href='". site_url() . "/wp-admin/post.php?post=$post_id&action=edit'>Edit</a>";
			
			} else {
				// inserting post failed -- opps
			$message = "Inserting Post Into WordPress Failed";
			}

		}
		
	//print_r($wpmbg_id_article);
    // Return String
    die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgPublishArticleAjax', 'mbgPublishArticleAjax' );
add_action( 'wp_ajax_mbgPublishArticleAjax', 'mbgPublishArticleAjax' );



function mbgNotifyMBGAjax(){
	// This will obtain the article using the ID then attempt to publish directly to wordpress
	
    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 			= get_option('wpmbg_options');
	$wpmbg_id_article	 	= intval($_POST['wpmbg_id_article']);

	
	// use this to get the post_id
		if(isset($options['published_articles']))
		{
		$published_articles = &$options['published_articles'];
		}
		else
		{
		$published_articles = array();
		}
	
	if (array_key_exists($wpmbg_id_article,$published_articles))
	{
	$post_id = $published_articles[$wpmbg_id_article];
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
		
		$save_url = WPMBG_BASE_URL . "/save_url";
		
		$body = array("id_article" => $wpmbg_id_article, "url"=> $post_info->guid, "type"=> $post_status, "shedule_date" => $date_info[0]);
	
		$res = mbg_api($save_url, $body);
		
			if (is_string($res))
			{
			$message = $res;
			} 
			else 
			{
			$message ="MyBlogGuest Have Been Informed";
			unset($published_articles[$wpmbg_id_article]);
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
add_action( 'wp_ajax_nopriv_mbgNotifyMBGAjax', 'mbgNotifyMBGAjax' );
add_action( 'wp_ajax_mbgNotifyMBGAjax', 'mbgNotifyMBGAjax' );





function mbgRejectAjax(){
	// This will send reject message to MBG
	
    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 				= get_option('wpmbg_options');
	$wpmbg_id_article	 	= intval($_POST['wpmbg_id_article']);
	$wpmbg_refuse_reason	= sanitize_text_field($_POST['wpmbg_refuse_reason']);
	
	$save_url = WPMBG_BASE_URL . "/refuse";
	$request = new WP_Http;
		
	$body = array("id_article" => $wpmbg_id_article, "reason" => $wpmbg_refuse_reason);


	$res = mbg_api($save_url, $body);

		if(is_string($res))
		{
		$message = $res;
		} 
		else 
		{
		$message ="MyBlogGuest Have Been Informed";
		mbgDelDraftArticle($wpmbg_id_article);		// удаляем черновик статьи
		}	

   mbgUpdateGTMCache();
   die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgRejectAjax', 'mbgRejectAjax' );
add_action( 'wp_ajax_mbgRejectAjax', 'mbgRejectAjax' );

function mbgDPRejectAjax(){
	// This will send reject message to MBG for Direct Posts
	// get me the options		

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	$options 				= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_id_article	 	= intval($_POST['wpmbg_id_article']);
	$wpmbg_dp_refuse_reason 	= sanitize_text_field($_POST['wpmbg_dp_refuse_reason']);

	$wpmbg_dp_article_title = sanitize_text_field($_POST['wpmbg_dp_article_title']);	
	$post_info = mbgGetPostByTitle($wpmbg_dp_article_title, 'OBJECT', 'post');
	
		
	$save_url = WPMBG_BASE_URL . "/reject_dp";
		
	$body = array("id_article" => $wpmbg_id_article, "reason" => $wpmbg_dp_refuse_reason);

	$res = mbg_api($save_url, $body);

		if(is_string($res))
		{
		$message = $res;
		}
		else
		{
		$message = "MyBlogGuest Have Been Informed";		
		}


	if(is_object($post_info)) 
	{
		if ($post_info->post_status == "draft") {
			wp_delete_post($post_info->ID);
			
		}
	}

   mbgUpdateGTMCache();
   die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgDPRejectAjax', 'mbgDPRejectAjax' );
add_action( 'wp_ajax_mbgDPRejectAjax', 'mbgDPRejectAjax' );


function mbgDPApproveAjax(){
	// This will send reject message to MBG for Direct Posts
	// get me the options		

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	$options 				= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_id_article	 	= intval($_POST['wpmbg_id_article']);
	$wpmbg_dp_pub_sch 		= sanitize_text_field($_POST['wpmbg_dp_pub_sch']);
	$wpmbg_dp_sch_date 		= sanitize_text_field($_POST['wpmbg_dp_sch_date']);
	$wpmbg_dp_article_title	 	= sanitize_text_field($_POST['wpmbg_dp_article_title']);
	
	$post_info = mbgGetPostByTitle(trim($wpmbg_dp_article_title),'OBJECT','post');

		if(is_object($post_info))
		{
		$article_url = $post_info->guid;
	
		$save_url = WPMBG_BASE_URL . "/approve_dp";
		
		$body = array("id_article" =>$wpmbg_id_article,"type"=>$wpmbg_dp_pub_sch,'url'=>$article_url,'shedule_date'=>$wpmbg_dp_sch_date);

		$res = mbg_api($save_url, $body);

			if(is_string($res))
			{
			$message = $res;
			}
			else
			{
			$message = "MyBlogGuest Have Been Informed";
			}
		}
		else
		{
		$message = "Can't find post in WP database!";
		}

   mbgUpdateGTMCache();
   die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgDPApproveAjax', 'mbgDPApproveAjax' );
add_action( 'wp_ajax_mbgDPApproveAjax', 'mbgDPApproveAjax' );


function mbgRateAuthorAjax(){
	// This will send Rating for an author

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}
	
	$options 			= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= $options['wpmbg_oauth_token'];
	$wpmbg_rating	 		= $_POST['wpmbg_rating'];
	$wpmbg_rating_comment 		= $_POST['wpmbg_rating_comment'];
	$wpmbg_id_user_rating		= intval($_POST['wpmbg_id_user_rating']);
	
	$save_url = WPMBG_BASE_URL . "/rating";
	$body = array("id_user_rating" => $wpmbg_id_user_rating, "rating_add" => $wpmbg_rating, "comment" => $wpmbg_rating_comment);
	$res = mbg_api($save_url, $body);
	
	if(is_string($res))
	{
	$message = $res;
	}
	else
	{
	$message = "Rating Has Been Submitted";
	}
	
   die($message);
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgRateAuthorAjax', 'mbgRateAuthorAjax' );
add_action( 'wp_ajax_mbgRateAuthorAjax', 'mbgRateAuthorAjax' );

