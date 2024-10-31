<?php
function myblogguest_offers_admin_page() {
    
	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
        showMessage("Sorry you are not authorized to access this page", true);
        return;
	}

?>

<div class="wrap">
<h2><?php echo WPMBG_DISPLAY_NAME ?> Article Offers Made</h2>

<?php 
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
?>

<?php wp_nonce_field('update-options'); ?>
<?php settings_fields( 'wpmbg_settings' ); ?>
<?php $options = get_option('wpmbg_options'); ?>
<?php $wpmbg_oauth_token = $options['wpmbg_oauth_token']; ?>
<?php if ($wpmbg_oauth_token == "") { 
  showMessage("This plugin has yet to be authorized, please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>initialize plugin</a> for accept connecting to MyBlogGuest!", true);
return null;
}
 elseif (!token_okay($wpmbg_oauth_token)) {
 showMessage("Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>", true);
 exit();
}

?>
<div id="poststuff" >
<div style="width:75%;" class="postbox-container">
<div class="search_form">
<label>Number of Results:</label>
<select name="wpmbg_num_results" id="wpmbg_num_results">
<option value="5">5</option>
<option value="10">10</option>
<option value="50">50</option>
<option value="100">100</option>
</select>


<input type="hidden" name="wpmbg_start" id="wpmbg_start" value="0"/>
<input type="button" class="button" id="wpmbg_article_offers_submit" value="Search" /><span class="loading" style="display:none"> <img src="<?php echo WPMBG_IMGS ?>/loading.gif" /></span><span id="wpmbg_search_status"></span>
</div>
<div id="search_results">

<table cellspacing="0" class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
    <th style="" class="manage-column column-status  desc" id="status" scope="col"><span>Status</span></th>
    <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th>
    <th style="" class="manage-column column-categories" id="offer" scope="col">Offer</th>
    <th style="" class="manage-column column-pin" id="tags" scope="col">Time Frame</th>      
    <th style="" class="manage-column column-date  asc" id="date" scope="col"><span>Create Date</span></a></th>
    </tr>
	</thead>
	<tfoot>
	<tr>
	    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
          <th style="" class="manage-column column-status  desc" id="status" scope="col"><span>Status</span></th>
        <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th>
        <th style="" class="manage-column column-categories" id="offer" scope="col">Offer</th>
        <th style="" class="manage-column column-pin" id="tags" scope="col">Time Frame</th>      
        <th style="" class="manage-column column-date  asc" id="date" scope="col"><span>Create Date</span></a></th>
       </tr>
	</tfoot>

	<tbody id="the-list">
    <tr><td span="2">Please Enter A Search Query</td></tr>
	</tbody>
</table>
</div><br />
<span id="wpmbg_prev_area">
<input class="button" type="button" name="wpmbg_article_offers_prev" id="wpmbg_article_offers_prev" value="Prev"/>
</span>
<span id="wpmbg_next_area">
<input class="button" type="button" name="wpmbg_article_offers_next" id="wpmbg_article_offers_next" value="Next"/>

</span>


<!-- Table row information to supply the JS-->
<table style="visibility:hidden">
<tbody id="table_row">
<tr valign="top" class="clone_results" id="post-3">
			<td class="post-title page-title column-title"><strong>
            <a href='<?php echo MBG_URL ?>/forum/articles_gallery.php?id=%ARTICLE_ID%' title='MyGuestBlog Artcile Preview' class='thickbox row-title'>%TITLE%</a></strong>
            <small>%DESCR%</small>
		</td>
        <td class="status column-status">%STATUS%<div class="reason">%REJECT_REASON%</div></td>
        <td class="author column-author"><a target="_new" href="http://myblogguest.com/forum/profile.php?id=%ID_USER_AUTHOR%">%AUTHOR%</a></td>
		<td class="categories column-categories">%OFFER%</td>
		<td class="column-pin">%TIME_FRAME%</td>
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

function get_offers_made($oauth_token,$options,$wpmbg_search_string ="",$wpmbg_category="",$wpmbg_num_results="100",$wpmbg_start="0")
{


	$articles_url = WPMBG_BASE_URL . "/my_offers";
	$request = new WP_Http;
	
	
	$body = array("oauth_token" => $oauth_token,"num"=>$wpmbg_num_results,"start" =>$wpmbg_start);
//print_r($body);

	$result = $request->request($articles_url,array('method' => 'POST','body' =>$body));
	//print_r($result);
	$html_content = array();
	
	if (isset($result->errors)) {
		// display error message of some sort
		$error =  "There was a  problem contacting the server!";
	} else {
		$html_content = $result['body'];

		if (isset($messages->error)){
			$error = $messages->error;	
			print_r($error);
		} else {
			/* Contacting server was sucessful  */
			//print_r($html_content);
		}	
	}
	//print_r($html_content);
	
	$html_content = str_replace("<br />","",$html_content);
	$articles = json_decode($html_content);
	return $articles;
	
} // end get articles given to me


function mbgOffersMadeAjax(){
//	echo "did we get here";
    //get data from our ajax() call

		if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
		die("Sorry you are not authorized to access this file");
		}

	// get me the options		
	$options 				= get_option('wpmbg_options');
	$wpmbg_oauth_token 		= sanitize_text_field($options['wpmbg_oauth_token']);
	$wpmbg_search_string 		= sanitize_text_field($_POST['wpmbg_search_string']);
	$wpmbg_num_results 		= intval($_POST['wpmbg_num_results']);
	$wpmbg_category 		= intval($_POST['wpmbg_category']);
	$wpmbg_start 			= intval($_POST['wpmbg_start']);
	
	
	$articles 	= get_offers_made($wpmbg_oauth_token,$options,$wpmbg_search_string,$wpmbg_category,$wpmbg_num_results,$wpmbg_start);	
	

    // Return String
    die(json_encode($articles));
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgOffersMadeAjax', 'mbgOffersMadeAjax' );
add_action( 'wp_ajax_mbgOffersMadeAjax', 'mbgOffersMadeAjax' );

?>