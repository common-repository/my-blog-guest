<?php
function myblogguest_request_admin_page() {

$options = get_option('wpmbg_options');	
$id_site = isset($options['wpmbg_id_site']) ? intval($options['wpmbg_id_site']) : 0;
$id_category = isset($_POST['id_category']) ? intval($_POST['id_category']) : intval($options['wpmbg_category']);

	if(isset($_POST['title']))	// сохраняем запрос
	{
	$res = mbg_api(WPMBG_BASE_URL.'/save_request' , array(
						      'id_site' => $id_site,
						      'title' => sanitize_text_field($_POST['title']),
						      'txt' => (isset($_POST['txt']) ? sanitize_text_field($_POST['txt']) : ''),
						      'close' => (isset($_POST['closed']) ? intval($_POST['closed']) : 0),
						      'resubmit' => (isset($_POST['resubmit']) ? intval($_POST['resubmit']) : 0),
                                                      'id_category' => $id_category,
			   			      ));
		if(is_string($res))
		{
		showMessage($res, true);
		}
	//var_dump($res);	
	}

$request = mbgGetRequest();
	
	if(is_string($request))
	{
		if($request == 'Empty Request')
		{
		$request = array(
				'id' => 0,
				'id_site' => $id_site,
				'title' => '',
				'status' => 0,
				'txt' => '',
				'date_edit' => '',
				'admin_comment' => '',
				'article_ignored' => 0,
				'status_name' => '',
				);
		}
		else
		{
		showMessage($request, true);
		return false;
		}
	}
        
$status_name = '';
$style = '';
	if(empty($request['title']))
	{
	$status_name = 'Request Empty';
	$style = '';
	}
	else if($request['status'] == 0)
	{
	$status_name = 'On Moderation';
	$style = '';
	}
	else if($request['status'] == 1)
	{
	$status_name = 'Active';
	$style = 'color: green;';
	}
	else if($request['status'] == 2)
	{
	$status_name = 'Archived';
	$style = '';
	}
	else if($request['status'] == 99)
	{
	$status_name = 'Rejected';
	$style = 'color: red;';
	}

$res = obtain_categories();

echo mbgRunTpl('request_page', array(
                                'request' => $request,
                                'style' => $style,
                                'status_name' => $status_name,
                                'categories' => (is_array($res) && isset($res['categories'])) ? $res['categories'] : null,
                                ));
}

function mbgRejectIdeaAjax(){

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

$reason		 		= sanitize_text_field($_POST['reason']);
$id_idea	 		= intval($_POST['id_idea']);
	
	
$res 	= mbg_api(WPMBG_BASE_URL.'/reject_idea', array(
							      'id_idea' => $id_idea,
							      'reason'  => $reason,
							      ), true);	
	
mbgClearRequestCache();

            // Return String
    	if(is_string($res))
    	{
    	die(json_encode(array('err' => $res)));
    	}
    	else
    	{
    	die(json_encode($res));
    	}
}


function mbgApproveIdeaAjax(){

	if( ! current_user_can(WPMBG_REQUIRED_CAPABILITY)) {
	die("Sorry you are not authorized to access this file");
	}

$reason		 		= sanitize_text_field($_POST['reason']);
$id_idea	 		= intval($_POST['id_idea']);
	
	
$res 	= mbg_api(WPMBG_BASE_URL.'/approve_idea', array(
						      'id_idea' => $id_idea,
						      'reason'  => $reason,
						      ), true);	
	

mbgClearRequestCache();

            // Return String
    	if(is_string($res))
    	{
    	die(json_encode(array('err' => $res)));
    	}
    	else
    	{
    	$res['code'] = mbgRunTpl('idea_row', array('idea' => $res));
    	die(json_encode($res));
    	}
}


// create custom Ajax call for WordPress
add_action( 'wp_ajax_nopriv_mbgRejectIdeaAjax', 'mbgRejectIdeaAjax' );
add_action( 'wp_ajax_mbgRejectIdeaAjax', 'mbgRejectIdeaAjax' );

add_action( 'wp_ajax_nopriv_mbgApproveIdeaAjax', 'mbgApproveIdeaAjax' );
add_action( 'wp_ajax_mbgApproveIdeaAjax', 'mbgApproveIdeaAjax' );
