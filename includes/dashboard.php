<?php
// Function that output's the contents of the dashboard widget
function wpmbg_dashboard_widget_function() {

$options = get_option('wpmbg_options');
$wpmbg_users_oauth_token = $options['wpmbg_oauth_token'];
$notWorking = 0;
?>
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields( 'wpmbg_settings' ); ?>
<?php $options = get_option('wpmbg_options'); ?>
<?php 
$wpmbg_oauth_token = $options['wpmbg_oauth_token']; 
?>

<?php if ($wpmbg_oauth_token == "") { 
  $msg = "This plugin has yet to be authorized, please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>initialize plugin</a> for accept connecting to MyBlogGuest!";
  showMessage($msg, true);
  echo $msg;
return null;
}
 elseif (!token_okay($wpmbg_oauth_token)) {
 showMessage("Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>", true);

	$notWorking = 1;
}


if (!$notWorking) {
?>
		<script>
			var wpmbg_widget_options = <?php print json_encode($wpmbg_widget_options); ?>;
		</script>
		<p class='wpmbg-header' >
			Some articles based upon the category you selected: 
		</p>

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
<input type="hidden" name="wpmbg_start" id="wpmbg_start" value="0"/>
<input type="button" class="button" id="wpmbg_search_submit" value="Search" /><span class="loading" style="visibility:hidden"><img src="<?php echo WPMBG_IMGS ?>/loading.gif" /></span><span id="wpmbg_search_status"></span>
</div>
<div id="search_results">

<table cellspacing="0" class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
    <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th> 
    <th style="" class="manage-column column-date  asc" id="date" scope="col"><span>Publish Date</span></a></th>
    </tr>
	</thead>
	<tfoot>
	<tr>
	    <th style="" class="manage-column column-title  desc" id="title" scope="col"><span>Title</span></th>
        <th style="" class="manage-column column-author  desc" id="author" scope="col"><span>Author</span></th>   
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
            <a href="javascript: void(0);" onclick="mbgArticlePreview(%ARTICLE_ID%)" title="MyGuestBlog Artcile Preview" class="row-title">%TITLE%</a></strong>
            <small>%DESCR%</small>
    <div class="row-actions"><span class="edit"><a href="javascript:void(0);" onclick="mbgArticleOfferDlg(%ARTICLE_ID%)" title="MyGuestBlog Make Offer">Make Offer</a> | </span></span><span class="trash"> <a href="javascript: void(0);" onclick="mbgArticlePreview(%ARTICLE_ID%)" title="MyGuestBlog Article Preview">Preview</a></span></div>
		</td>
        <td class="author column-author">%AUTHOR%</td>
		<td class="date column-date"><abbr title="">%DATE%</abbr></td>
     </tr>
     </tbody>
</table>
<!-- end of table row info -->



<?php
} else {
	echo "Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>";

} // end of not working


}


function find_articles($oauth_token,$options,$wpmbg_search_string ="",$wpmbg_category="",$wpmbg_num_results="5",$wpmbg_start="0",$wpmbg_sort_by="title",$wpmbg_sort_order="asc",$only_authorized=0)
{
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC."class-http.php");
	}

	$url = WPMBG_BASE_URL . "/find_articles";	
			
	$request = new WP_Http;
	$body = array("oauth_token" => $oauth_token,"num"=>$wpmbg_num_results,"id_category" =>$wpmbg_category,"txt"=> $wpmbg_search_string,"start" =>$wpmbg_start,"sort_by"=>$wpmbg_sort_by,"sort_order"=>$wpmbg_sort_order,"only_authorized"=>$only_authorized);
	$result = $request->request($url,array('method' => 'POST','body' =>$body));
	//$result = $request->request($url);
	$html_content = array();
	
	if (isset($result->errors)) {
		// display error message of some sort
	} else {
		$html_content = $result['body'];
	}

	// get info from my offers
	$url = WPMBG_BASE_URL . "/my_offers";				
	$request = new WP_Http;
	$body = array("oauth_token" => $oauth_token,"num"=>100,"start" =>0);
	$result = $request->request($url,array('method' => 'POST','body' =>$body));
	$my_offers = $result['body'];

	// compare articles to offers
	$html_content = json_decode($html_content);
	$my_offers = (json_decode($my_offers));
	foreach ($html_content->articles as $ref => $art)
	{
		foreach ($my_offers->offers as $o_ref => $off)
		{
			if ($off->id_article == $art->id) { 
			$html_content->articles[$ref]->offer_placed = "y";
			
			} 
		}
	}

	return $html_content;
}	




// Function that beeng used in the action hook
function add_dashboard_widgets() {
	wp_add_dashboard_widget('wpmbg_dashboard_widget', 'MyBlogGuest', 'wpmbg_dashboard_widget_function');
}

// Register the new dashboard widget into the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'add_dashboard_widgets' );


//do_action( 'wp_dashboard_setup' );
?>