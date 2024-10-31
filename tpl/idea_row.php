<?php 

echo '<tr id="idea-'.$idea['id'].'">';
echo '<td>';
echo '<h3 style="font-weight: 900;">'.sanitize_text_field($idea['title']).'</h3>';
echo '<p class="info"><b>Author: </b> <a target="_blank" href="http://myblogguest.com/forum/profile.php?id='.$idea['id_user'].'">'.$idea['username'].'</a></p>';
echo '<p class="info"><b>Status: </b> ';
	if($idea['status'] == 0)
	{
	echo '<span>New</span>';
	}
	else if($idea['status'] == 1)
	{
	echo '<span style="color: green;">Confirmed</span>';
	}
	else if($idea['status'] == 2)
	{
	echo '<span style="color: orange;">Article Attached (Pending)</span>';
	}

echo '</p>';

	if($idea['status'] == 2 && !empty($idea['id_article']) && !empty($idea['article_title']))
	{
	echo '<p class="info"><b>Article:</b> '.sanitize_text_field($idea['article_title']).' (<a href="'.get_admin_url().'admin.php?page=article_management&status=2">manage</a>)</p>';
	}

echo '<p style="font-style: italic;">'.sanitize_text_field($idea['author_comment']).'</p>';
echo '</td>';

echo '<td>';
	if($idea['status'] == 0)
	{
	echo '<p><input type="text" id="blogger-comment-'.$idea['id'].'" data_id_idea="'.$idea['id'].'" size="60" maxlength="255" /></p>';
	echo '<p>';
	echo '<a style="font-size: 12px;" href="javascript: void(0);" onclick="mbgApproveIdea('.$idea['id'].')"><strong>Approve</strong></a>&nbsp;';
	echo '<a style="font-size: 10px; margin-left: 10px;" href="javascript: void(0);" onclick="mbgRejectIdea('.$idea['id'].')">Reject</a>';
	echo '</p>';
	}
echo '<p style="font-style: italic;">'.sanitize_text_field($idea['blogger_comment']).'</p>';
echo '</td>';
echo '</tr>';

?>