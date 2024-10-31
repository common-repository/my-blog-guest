<?php

function myblogguest_find_articles_page() {
    
	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
        showMessage("Sorry you are not authorized to access this page", true);
        return;
	}

?>

<div class="wrap">

	
<h2><?php echo WPMBG_DISPLAY_NAME ?> Find Articles</h2>
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
}  elseif (!token_okay($wpmbg_oauth_token)) {
 showMessage("Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>", true);
 exit();
}
?> 
<div id="poststuff" >
<div style="width:75%;" class="postbox-container" >
<div class="search_form">
<label>Category: </label>
<?php     
		$cats = obtain_categories($wpmbg_oauth_token);
	  	$options = display_categories($cats,$options);
?>	 

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
<option value="title">Title</option>
<option value="publish_date">Publish Date</option>
<option value="username">Username</option>
<option value="rating">Rating</option>
</select>

<select name="wpmbg_sort_order" id="wpmbg_sort_order">
<option value="asc">Asc</option>
<option value="desc">Desc</option>
</select>

<input type="checkbox" name="only_authorized" id="only_authorized" value="1" />&nbsp;<label for="only_authorized">Verified Authors Only</label>&nbsp;

<input type="hidden" name="wpmbg_start" id="wpmbg_start" value="0"/>
<input type="button" class="button" id="wpmbg_search_submit" value="Search" /><span class="loading" style="display:none"> <img src="<?php echo WPMBG_IMGS ?>/loading.gif" /></span><span id="wpmbg_search_status"></span>
</div>
<div id="search_results">

<table cellspacing="0" class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
    <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th>
    <th style="" class="manage-column column-pin  desc" id="author" scope="col"><span>Authors Rating</span></th>  
    <th style="" class="manage-column column-categories" id="categories" scope="col">Category</th>
    <th style="" class="manage-column column-pin" id="tags" scope="col">Pin</th> 
    <th style="" class="manage-column column-pin" id="tags" scope="col">Word Count</th>         
    <th style="" class="manage-column column-date  asc" id="date" scope="col"><span>Publish Date</span></a></th>
    </tr>
	</thead>
	<tfoot>
	<tr>
	    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
        <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th>
        <th style="" class="manage-column column-pin  desc" id="author" scope="col"><span>Authors Rating</span></th>  
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
<input class="button" type="button" name="wpmbg_search_prev" id="wpmbg_search_prev" value="Prev"/>
</span>
<span id="wpmbg_next_area">
<input class="button" type="button" name="wpmbg_search_next" id="wpmbg_search_next" value="Next"/>

</span>


<!-- Table row information to supply the JS-->
<table style="visibility:hidden">
<tbody id="table_row">
<tr valign="top" class="clone_results" id="post-3">
			<td class="post-title page-title column-title"><strong>
            <a href='<?php echo MBG_URL ?>/forum/articles_gallery.php?id=%ARTICLE_ID%' target="_blank" title='MyGuestBlog Article Preview' class='row-title'>%TITLE%</a></strong>
            <small>%DESCR%</small>
<div class="row-actions"><span class="edit"><a href="javascript:void(0);" onclick="mbgArticleOfferDlg(%ARTICLE_ID%)" title='MyGuestBlog Make Offer'>Make Offer</a> | </span></span>
<span class="trash"><a href='javascript:void(0);' onclick="mbgArticlePreview(%ARTICLE_ID%)" title='MyGuestBlog Article Preview'>Preview</a></span></div>
		</td>
        <td class="author column-author"><a target="_new" href="http://myblogguest.com/forum/profile.php?id=%ID_USER_AUTHOR%">%AUTHOR%</a>&nbsp;%GRAVATAR%</td>
        <td class="author column-author">%AUTHOR_RATING%</td>
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

function mbgFindArticlesAjax(){

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
        $only_authorized                = intval($_POST['only_authorized']);
        
	$wpmbg_sort_by 		= sanitize_text_field($_POST['wpmbg_sort_by']);
	$wpmbg_sort_order 	= sanitize_text_field($_POST['wpmbg_sort_order']);

	$wpmbg_start 		= intval($_POST['wpmbg_start']);
	$articles = find_articles($wpmbg_oauth_token,$options,$wpmbg_search_string,$wpmbg_category,$wpmbg_num_results,$wpmbg_start,$wpmbg_sort_by,$wpmbg_sort_order,$only_authorized);
	
    // Return String
    die(json_encode($articles));
}

// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgFindArticlesAjax', 'mbgFindArticlesAjax' );
add_action( 'wp_ajax_mbgFindArticlesAjax', 'mbgFindArticlesAjax' );



// отправка Article Offer - AJAX Handler

function mbgSendArticleOfferAjax()
{

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

$err = '';
$msg = '';
$ret = array();

$id_article		= isset($_POST['id_article']) ? intval($_POST['id_article']) : 0;
$offer			= isset($_POST['offer']) ? sanitize_text_field($_POST['offer']) : '';
$days			= isset($_POST['days']) ? intval($_POST['days']) : 0;

$options = get_option('wpmbg_options');
$id_site = isset($options['wpmbg_id_site']) ? intval($options['wpmbg_id_site']) : 0;

    if(empty($id_article) || empty($id_site) || empty($days) || empty($offer))
    {
    $err = 'Bad Request!';
    }
    else
    {
    $url = WPMBG_BASE_URL . "/send_offer";
	
    $body = array("id_article"=>$id_article, "offer" =>$offer, "days"=>$days, "id_site"=>$id_site);

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

add_action( 'wp_ajax_nopriv_mbgSendArticleOfferAjax', 'mbgSendArticleOfferAjax');
add_action( 'wp_ajax_mbgSendArticleOfferAjax', 'mbgSendArticleOfferAjax');



// Article Preview - AJAX Handler

function mbgArticlePreviewAjax()
{

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

$err = '';
$msg = '';
$ret = array();

$id_article		= isset($_POST['id_article']) ? intval($_POST['id_article']) : 0;

    if(empty($id_article))
    {
    $err = 'Bad Request!';
    }
    else
    {
    $url = WPMBG_BASE_URL. "/article/".intval($id_article);
	
    $body = array("id_article" => $id_article);

    $res = mbg_api($url, $body);
	
	if(is_string($res))
	{
	$err = $res;
	}
	else
	{
	$ret['code'] = mbgRunTpl('article_preview', array('article' => $res, 'links' => $res['links']));
	}
    }
    
$ret['err'] = $err;

die(json_encode($ret));
}

add_action( 'wp_ajax_nopriv_mbgArticlePreviewAjax', 'mbgArticlePreviewAjax');
add_action( 'wp_ajax_mbgArticlePreviewAjax', 'mbgArticlePreviewAjax');

