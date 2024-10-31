<div class="wrap">
<h2><?php echo WPMBG_DISPLAY_NAME ?> Articles Request</h2>

<?php 
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	
	wp_nonce_field('update-options');
	settings_fields( 'wpmbg_settings' );
?>

<div style="width:75%;" class="postbox-container">
<?php
    if($request['id'] > 0)
    {
    echo '<p><a target="_blank" href="'.MBG_URL.'/forum/elite/article_requests.php?id_request='.$request['id'].'">See it on MBG</a></p>';
    }
?>
<div id="request-edit">
<form action="<?php echo get_admin_url(); ?>admin.php?page=articles_request" method="post">

<table class="form-table">
<tbody>

<tr valign="top">
<th scope="row">
<label for="blogname">Status</label>
</th>
<td>

<?php	
echo "<span style='$style'>$status_name</span>";
?>

</td>
</tr>

<?php

	if($request['status'] == 99 && !empty($request['admin_comment']))
	{
	echo "<tr valign='top'>";
	echo "<th scope='row'>";
	echo "<label for='blogname'>Reject Reason</label>";
	echo "</th>";
	echo "<td>";
	echo "<span style='color: red;'>".$request['admin_comment']."</span>";
	echo "</td>";
	echo "</tr>";
	}
?>

<tr valign="top">
<th scope="row">
<label for="blogname">Request Title</label>
</th>
<td>
<input id="text" class="regular-text" type="text" value="<?php echo sanitize_text_field($request['title']); ?>" name="title">
</td>
</tr>

<tr valign="top">
<th scope="row">
<label>Category: </label>
</th>
<td>
<?php     
echo '<select id="id_category" class="regular-text" type="text" name="id_category">';

    foreach($categories as $category)
    {
    echo '<option id="category-'.$category['id'].'" value="'.$category['id'].'" '.(($request['id_category'] == $category['id']) ? ' selected="selected" ' : '').'>'.$category['category'].'</option>';
    }
echo '</select>';
?>
</td>
</tr>

<?php
	if($request['title'] != '' && $request['status'] != 99)
	{
	echo '<tr valign="top">';
	echo '<th scope="row">Archive Request</th>';
	echo '<td>';
	echo '<fieldset>';
	echo '<label for="closed">';
	echo '<input id="closed" type="checkbox" value="1" name="closed" '.(($request['status'] == 2) ? ' checked="checked" ' : '').'> ';
		if($request['status'] == 2)
		{
		echo 'Clear this checkbox to return this request in gallery.';
		}
		else
		{
		echo 'Set this checkbox to archive your request';
		}
	echo '</label><br />';
	echo '</fieldset>';
	echo '</td>';
	echo '</tr>';
	}
	
	if($request['status'] == 99)
	{
	echo '<tr valign="top">';
	echo '<th scope="row">Resubmit Request</th>';
	echo '<td>';
	echo '<fieldset>';
	echo '<label for="resubmit">';
	echo '<input id="closed" type="checkbox" value="1" name="resubmit"> ';
	echo '<span>Set this if you want send request to moderation</span>';
	echo '</label><br />';
	echo '</fieldset>';
	echo '</td>';
	echo '</tr>';
	}
?>

</tbody>
</table>

<h3>Request Text</h3>

<textarea id='txt' name='txt' class='large-text'><?php echo sanitize_text_field($request['txt']); ?></textarea>

<p class="submit">
<input id="submit" class="button-primary" type="submit" value="Save Changes" name="submit">
</p>

</form>
</div>

<?php

	if(!empty($request['ideas']))
	{
	echo '<h3>Request Ideas</h3>';
	echo '<div id="poststuff" >';
	echo '<div id="search_results">';

	echo '<table cellspacing="0" class="wp-list-table widefat fixed posts">';
	echo '	<thead>';
	echo '	<tr>';
	echo '    <th style="" class="manage-column column-idea  desc" id="idea" scope="col"><span>Idea</span></th>';
	echo '    <th style="" class="manage-column column-comment  asc" id="comment" scope="col"><span>Your Comment</span></a></th>';
	echo '	</tr>';
	echo '	</thead>';

	echo '	<tbody id="the-list">';

		foreach($request['ideas'] as $idea)
		{
		echo mbgRunTpl('idea_row', array('idea' => $idea));
		}
	
	echo '	</tbody>';
	echo '</table>';
	echo '</div><br />';
	echo '</div>';
	}
	
mbg_add_custom_box();
?>
</div></div>